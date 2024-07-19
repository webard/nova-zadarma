<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Support\Carbon;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;
use Webard\NovaZadarma\Http\Requests\IncomingCallStartSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;

class IncomingCallStartController
{
    public function __invoke(IncomingCallStartSignedRequest $request)
    {
        $data = $request->validated();

        $startedAt = Carbon::parse($data['call_start'])->setTimezone(config('app.timezone'));

        $phoneCall = new PhoneCall([
            'type' => PhoneCallType::Incoming,
            'disposition' => PhoneCallDisposition::Pending,
            'pbx_call_id' => $data['pbx_call_id'],
            'caller_phone_number' => $data['caller_id'],
            'receiver_phone_number' => $data['called_did'],
            'started_at' => $startedAt,
        ]);

        $userModel = config('nova-zadarma.models.user.class');

        $caller = $userModel::query()
            ->where(config('nova-zadarma.models.user.phone_number_field'), $data['caller_id'])
            ->first();

        if ($caller) {
            $phoneCall->caller()->associate($caller);
        }

        $receiver = $userModel::query()
            ->where(config('nova-zadarma.models.user.phone_number_field'), $data['called_did'])
            ->first();

        if ($receiver) {
            $phoneCall->receiver()->associate($receiver);
        }

        $phoneCall->save();

        return response()->json([
            'success' => true,
            'message' => 'Incoming call start saved',
        ]);

    }
}
