<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Http\JsonResponse;
use Webard\NovaZadarma\Http\Requests\OutgoingCallEndSignedRequest;

class OutgoingCallEndController
{
    public function __invoke(OutgoingCallEndSignedRequest $request): JsonResponse
    {
        $validData = $request->validated();

        $className = config('nova-zadarma.webhooks.outgoing_call_end');

        // $this->log->debug('[handleOutgoingEnd] validated successfully, handling event', [
        //     [
        //         'valid_data' => $validData,
        //         'handler' => $className,
        //     ],
        // ]);

        // try {
        //     $class = new $className($this->log);
        //     $response = $class($validData, $request);

        //     return response($response === true ? 'ok' : 'error');
        // } catch (\Throwable $e) {
        //     $this->log->error('[handleOutgoingEnd] Error while handling', [
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString(),
        //     ]);

        //     return response('exception');
        // }

        return response()->json([
            'success' => true,
        ]);
    }
}
