<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Webard\NovaZadarma\Http\Requests\RecordingSignedRequest;

class RecordingController
{
    public function __invoke(RecordingSignedRequest $request)
    {
        $validData = $request->validated();

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
