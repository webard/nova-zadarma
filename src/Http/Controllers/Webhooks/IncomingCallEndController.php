<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Events\IncomingPhoneCall\IncomingPhoneCallAnswered;
use Webard\NovaZadarma\Events\IncomingPhoneCall\IncomingPhoneCallEnded;
use Webard\NovaZadarma\Events\IncomingPhoneCall\IncomingPhoneCallFailed;
use Webard\NovaZadarma\Http\Requests\IncomingCallEndSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;

class IncomingCallEndController
{
    public function __invoke(IncomingCallEndSignedRequest $request)
    {
        $data = $request->validated();

        $phoneCall = PhoneCall::query()
            ->pbxCallId($data['pbx_call_id'])
            ->firstOrFail();

        $disposition = PhoneCallDisposition::from($data['disposition']);

        $duration = (int) $data['duration'] > 0 ? $data['duration'] : null;

        $phoneCall->fill([
            'ended_at' => now(config('app.timezone')),
            'disposition' => $disposition,
            'duration' => $duration,
            'is_answered' => $disposition === PhoneCallDisposition::Answered,
            'receiver_sip' => $data['last_internal'] ?? null,
        ]);

        $userModel = config('nova-zadarma.models.user.class');

        if ($disposition === PhoneCallDisposition::Answered) {
            $receiver = $userModel::query()
                ->where(config('nova-zadarma.models.user.sip_field'), $data['last_internal'])
                ->first();

            if ($receiver) {
                $phoneCall->receiver()->associate($receiver);
            }
        }

        $phoneCall->save();

        event(new IncomingPhoneCallEnded($phoneCall));

        if ($disposition === PhoneCallDisposition::Answered) {
            event(new IncomingPhoneCallAnswered($phoneCall));
        } else {
            event(new IncomingPhoneCallFailed($phoneCall));
        }

        return response()->json([
            'success' => true,
            'message' => 'Incoming call end saved',
        ]);
    }
}
