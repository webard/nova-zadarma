<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Http\JsonResponse;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Http\Requests\OutgoingCallEndSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;

class OutgoingCallEndController
{
    public function __invoke(OutgoingCallEndSignedRequest $request): JsonResponse
    {
        $data = $request->validated();

        $phoneCall = PhoneCall::query()->pbxCallId($data['pbx_call_id'])->firstOrFail();

        $disposition = PhoneCallDisposition::from($data['disposition']);

        $phoneCall->update([
            'ended_at' => now(config('app.timezone')),
            'is_answered' => $disposition === PhoneCallDisposition::Answered,
            'disposition' => $disposition,
            'duration' => $data['duration'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Outgoing call end saved',
        ]);
    }
}
