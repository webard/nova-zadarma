<?php

declare(strict_types=1);

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Webard\NovaZadarma\Http\Controllers\Webhooks\OutgoingCallEndController;
use Webard\NovaZadarma\Http\Requests\WebhookRequest;
use Webard\NovaZadarma\Services\ZadarmaService;

class ZadarmaWebhookController
{
    private LoggerInterface $log;

    public function __construct(private ZadarmaService $zadarmaService)
    {

        $this->log = Log::channel(config('nova-zadarma.webhook_log_channel'));
    }

    /**
     * @link https://zadarma.com/pl/support/api/#other_methods
     */
    public function eventWebhook(Request $request): Response
    {
        $this->log->debug('eventWebhook input', $request->all());

        return response('not implemented');
    }

    private function fixRequestPhoneNumbers(Request $request): void
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
    }

    /**
     * @link https://zadarma.com/pl/support/api/#api_webhooks
     */
    public function pbxCallWebhook(WebhookRequest $request): Response
    {
        $this->fixRequestPhoneNumbers($request);

        $container = app();

        $controller = match ((string) $request->input('event')) {
            'NOTIFY_OUT_END' => OutgoingCallEndController::class
        };

        /** @var Route $router */
        $router = $container->make(Route::class);

        $router->name('::'.$controller);

        $controllerInstance = $container->make($controller);

        return (new ControllerDispatcher($container))->dispatch($router, $controllerInstance, '__invoke');

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

    private function handleRecord(Request $request): Response
    {

        $validator = Validator::make(
            $request->all(),
            [

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
