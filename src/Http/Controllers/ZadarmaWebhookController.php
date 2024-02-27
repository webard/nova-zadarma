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

        $this->log = Log::build([
            'driver' => 'daily',
            'path' => storage_path('logs/zadarma-webhook.log'),
        ]);
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

        //Log::debug('pbxCallWebhook input', $request->all());

        return match ((string) $request->input('event')) {
            'NOTIFY_RECORD' => $this->handleRecord($request, $request->input('pbx_call_id')),
            'NOTIFY_OUT_START' => $this->handleOutgoingStart($request),
            'NOTIFY_OUT_END' => $this->handleOutgoingEnd($request),
            'NOTIFY_START' => $this->handleIncomingStart($request),
            'NOTIFY_END' => $this->handleIncomingEnd($request),

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

                // TODO: for some reason, caller id does not have "+" at the beginning, observe values if we can add it and validate as phone number
                'called_did' => 'string',
            ],
        );

        if ($validator->fails()) {
            $this->log->debug('NOTIFY_START validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('error');
        }

        $validatedRequest = $validator->validated();

        Log::debug('NOTIFY_START validated', $validatedRequest);

        $className = config('nova-zadarma.webhooks.incoming_call_start');

        $class = new $className();
        $response = $class($validatedRequest, $request);

        return response($response === true ? 'ok' : 'error');
    }

    private function handleIncomingEnd(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',

                'caller_id' => 'phone',

                // TODO: for some reason, caller id does not have "+" at the beginning, observe values if we can add it and validate as phone number
                'called_did' => 'string',

                'duration' => 'integer',
                'is_recorded' => 'boolean',
                'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
                'status_code' => 'integer',

                'internal' => 'string|nullable',
                'destination' => 'phone',
            ]
        );

        if ($validator->fails()) {
            $this->log->debug('NOTIFY_OUT_END validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('error');
        }

        $validatedRequest = $validator->validated();

        $className = config('nova-zadarma.webhooks.incoming_call_end');

        $class = new $className();
        $response = $class($validatedRequest, $request);

        return response($response === true ? 'ok' : 'error');
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
                // TODO: for some reason, caller id does not have "+" at the beginning, observe values if we can add it and validate as phone number
                'caller_id' => 'string',
            ],
        );

        if ($validator->fails()) {
            $this->log->debug('NOTIFY_OUT_START validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('error');
        }

        $validatedRequest = $validator->validated();

        if (stripos($validatedRequest['caller_id'], '+') === false) {
            $validatedRequest['caller_id'] = '+'.$validatedRequest['caller_id']; // add '+' to 'caller_id
        }

        $className = config('nova-zadarma.webhooks.outgoing_call_start');

        $class = new $className();
        $response = $class($validatedRequest, $request);

        return response($response === true ? 'ok' : 'error');
    }

    private function handleOutgoingEnd(Request $request): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pbx_call_id' => 'required|string',
                // TODO: for some reason, caller id does not have "+" at the beginning, observe values if we can add it and validate as phone number
                'caller_id' => 'string',

                'duration' => 'integer',
                'is_recorded' => 'boolean',
                'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
                'status_code' => 'integer',

                'internal' => 'string|nullable',
                'destination' => 'phone',
            ]
        );

        if ($validator->fails()) {
            $this->log->debug('NOTIFY_OUT_END validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return response('error');
        }

        $validatedRequest = $validator->validated();

        $className = config('nova-zadarma.webhooks.outgoing_call_end');

        $class = new $className();
        $response = $class($validatedRequest, $request);

        return response($response === true ? 'ok' : 'error');
    }

    private function handleRecord(Request $request, string $externalCallId): Response
    {
        $zadarmaUrl = $this->zadarmaService->getRecordingUrl($externalCallId);

        $className = config('nova-zadarma.webhooks.phone_call_record');

        $class = new $className();
        $response = $class($zadarmaUrl, $externalCallId, $request);

        return response($response === true ? 'ok' : 'error');
    }
}
