<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

class OutgoingCallStartController
{
    public function __invoke()
    {
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

        return response()->json([
            'message' => 'Outgoing call started',
        ]);
    }
}
