<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

class IncomingCallStartController
{
    public function __invoke()
    {
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
}
