<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

class IncomingCallEndController
{
    public function __invoke()
    {
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
}