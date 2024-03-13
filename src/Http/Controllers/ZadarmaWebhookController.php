<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Http\Controllers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Webard\NovaZadarma\Services\ZadarmaService;

/**
 * Gdy rozpoczyna się połączenie przychodzące, nie znamy użytkownika, który odbiera połączenie
 * więc przypisujemy je do konta technicznego sklepu, który ma przypisany numer infolinii.
 * Po zakończeniu połączenia (NOTIFY_END), aktualizujemy użytkownika, który odebrał połączenie.
 *
 * Gdy rozpoczyna się połączenie wychodzące, wpis w bazie danych powinien już istnieć, po to, aby znać trigger (np. lead). Gdy otrzymujemy event NOTIFY_OUT_START, dopasowujemy rozmowę za pomocą numeru telefonu oraz statusu "pending" i wiążemy z pbx_call_id.
 *
 * Jeżeli takiego wpisu nie ma, to znaczy, że połączenie wychodzące zostało zainicjowane z poziomu zadarma, a nie z CRM.
 */
class ZadarmaWebhookController extends Controller
{
    private LoggerInterface $log;

    public function __construct(private ZadarmaService $zadarmaService)
    {
        // ? zadarma webhook test
        $this->middleware(function (Request $request, Closure $next): Response|string {
            if ($request->input('zd_echo')) {
                return response($request->input('zd_echo'));
            }

            return $next($request);
        });

        $this->log = Log::channel(config('nova-zadarma.webhook_log_channel'));
    }

    /*
    * https://zadarma.com/pl/support/api/#other_methods
    */
    public function eventWebhook(Request $request): Response
    {
        $this->log->debug('eventWebhook input', $request->all());

        return response('not implemented');
    }

    /*
    * https://zadarma.com/pl/support/api/#api_webhooks
    */
    public function pbxCallWebhook(Request $request): Response
    {
        // for some reason, caller_id or called_did sometimes does not have "+" at the beginning
        // trying to fix this to make it work with validator
        $calledDid = $request->input('called_did');
        if ($calledDid !== null && str_contains($calledDid, '+') === false) {
            $request->merge(['called_did' => '+'.$calledDid]);
        }

        $callerId = $request->input('caller_id');
        if ($callerId !== null && str_contains($callerId, '+') === false) {
            $request->merge(['caller_id' => '+'.$callerId]);
        }

        $this->log->debug('[pbx] webhook came with event '.$request->input('event'), $request->all());

        return match ((string) $request->input('event')) {
            'NOTIFY_RECORD' => $this->handleRecord($request),
            'NOTIFY_OUT_START' => $this->handleOutgoingStart($request),
            'NOTIFY_OUT_END' => $this->handleOutgoingEnd($request),
            'NOTIFY_START' => $this->handleIncomingStart($request),
            'NOTIFY_END' => $this->handleIncomingEnd($request),

            // NOTIFY_INTERNAL is called multiple times during one phone call
            // Contains SIP numbers that are notified about incoming call
            'NOTIFY_INTERNAL' => response('unsupported'),
            'NOTIFY_ANSWER' => response('unsupported'),
            'NOTIFY_IVR' => response('unsupported'),
            default => response('unknown'),
        };
    }

    private function handleIncomingStart(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',
                'call_start' => 'required|date',
                'caller_id' => 'phone',
                'called_did' => 'phone',
            ],
        );

        if ($validator->fails()) {
            $this->log->error('[handleIncomingStart] validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('validation');
        }

        $validData = $validator->validated();

        $className = config('nova-zadarma.webhooks.incoming_call_start');

        $this->log->debug('[handleIncomingStart] validated successfully, handling event', [
            [
                'valid_data' => $validData,
                'handler' => $className,
            ],
        ]);

        try {
            $class = new $className($this->log);
            $response = $class($validData, $request);

            return response($response === true ? 'ok' : 'error');
        } catch (\Throwable $e) {
            $this->log->error('[handleIncomingStart] Error while handling', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('exception');
        }
    }

    private function handleIncomingEnd(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',

                'caller_id' => 'phone',

                'called_did' => 'phone',

                'duration' => 'integer',
                'is_recorded' => 'boolean',
                'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
                'status_code' => 'integer',

                'internal' => 'string|nullable',
                'destination' => 'phone',
            ]
        );

        if ($validator->fails()) {
            $this->log->error('[handleIncomingEnd] validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('validation');
        }

        $validData = $validator->validated();

        $className = config('nova-zadarma.webhooks.incoming_call_end');

        $this->log->debug('[handleIncomingEnd] validated successfully, handling event', [
            [
                'valid_data' => $validData,
                'handler' => $className,
            ],
        ]);

        try {
            $class = new $className($this->log);
            $response = $class($validData, $request);

            return response($response === true ? 'ok' : 'error');
        } catch (\Throwable $e) {
            $this->log->error('[handleIncomingEnd] Error while handling', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('exception');
        }
    }

    private function handleOutgoingStart(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',
                'call_start' => 'required|date',
                'internal' => 'string',
                'destination' => 'phone',
                'caller_id' => 'phone',
            ],
        );

        if ($validator->fails()) {
            $this->log->error('[handleOutgoingStart] validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('validation');
        }

        $validData = $validator->validated();

        $className = config('nova-zadarma.webhooks.outgoing_call_start');

        $this->log->debug('[handleOutgoingStart] validated successfully, handling event', [
            [
                'valid_data' => $validData,
                'handler' => $className,
            ],
        ]);

        try {
            $class = new $className($this->log);
            $response = $class($validData, $request);

            return response($response === true ? 'ok' : 'error');
        } catch (\Throwable $e) {
            $this->log->error('[handleOutgoingStart] Error while handling', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('exception');
        }
    }

    private function handleOutgoingEnd(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',

                'caller_id' => 'phone',

                'duration' => 'integer',
                'is_recorded' => 'boolean',
                'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
                'status_code' => 'integer',

                'internal' => 'string|nullable',
                'destination' => 'phone',
            ]
        );

        if ($validator->fails()) {
            $this->log->error('[handleOutgoingEnd] validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('validation');
        }

        $validData = $validator->validated();

        $className = config('nova-zadarma.webhooks.outgoing_call_end');

        $this->log->debug('[handleOutgoingEnd] validated successfully, handling event', [
            [
                'valid_data' => $validData,
                'handler' => $className,
            ],
        ]);

        try {
            $class = new $className($this->log);
            $response = $class($validData, $request);

            return response($response === true ? 'ok' : 'error');
        } catch (\Throwable $e) {
            $this->log->error('[handleOutgoingEnd] Error while handling', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('exception');
        }
    }

    private function handleRecord(Request $request): Response
    {

        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            $this->log->error('[handleRecord] validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('validation');
        }

        $validData = $validator->validated();

        $className = config('nova-zadarma.webhooks.phone_call_record');

        $this->log->debug('[handleRecord] validated successfully, handling event', [
            [
                'valid_data' => $validData,
                'handler' => $className,
            ],
        ]);

        $zadarmaUrl = $this->zadarmaService->getRecordingUrl($validData['pbx_call_id']);

        try {
            $class = new $className($this->log);
            $response = $class($zadarmaUrl, $validData['pbx_call_id'], $request);

            return response($response === true ? 'ok' : 'error');
        } catch (\Throwable $e) {
            $this->log->error('[handleOutgoingEnd] Error while handling', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('exception');
        }
    }
}
