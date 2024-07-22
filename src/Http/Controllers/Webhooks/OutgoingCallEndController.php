<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Http\JsonResponse;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Events\OutgoingPhoneCall\OutgoingPhoneCallAnswered;
use Webard\NovaZadarma\Events\OutgoingPhoneCall\OutgoingPhoneCallEnded;
use Webard\NovaZadarma\Events\OutgoingPhoneCall\OutgoingPhoneCallFailed;
use Webard\NovaZadarma\Http\Requests\OutgoingCallEndSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;

class OutgoingCallEndController
{
    public function __invoke(OutgoingCallEndSignedRequest $request): JsonResponse
    {
        $data = $request->validated();

        $phoneCall = PhoneCall::query()
            ->pbxCallId($data['pbx_call_id'])
            ->firstOrFail();

        $disposition = PhoneCallDisposition::from($data['disposition']);

        $duration = (int) $data['duration'] > 0 ? $data['duration'] : null;

        $phoneCall->update([
            'ended_at' => now(config('app.timezone')),
            'is_answered' => $disposition === PhoneCallDisposition::Answered,
            'disposition' => $disposition,
            'duration' => $duration,
        ]);

        event(new OutgoingPhoneCallEnded($phoneCall));

        if ($disposition === PhoneCallDisposition::Answered) {
            event(new OutgoingPhoneCallAnswered($phoneCall));
        } else {
            event(new OutgoingPhoneCallFailed($phoneCall));
        }

        return response()->json([
            'success' => true,
            'message' => 'Outgoing call end saved',
        ]);
    }
}
