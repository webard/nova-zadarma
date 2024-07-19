<?php

namespace Webard\NovaZadarma\Http\Controllers\Webhooks;

use Illuminate\Http\JsonResponse;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Enums\PhoneCallType;
use Webard\NovaZadarma\Http\Requests\OutgoingCallStartSignedRequest;
use Webard\NovaZadarma\Models\PhoneCall;

class OutgoingCallStartController
{
    public function __invoke(OutgoingCallStartSignedRequest $request): JsonResponse
    {
        $data = $request->validated();

        $phoneCall = PhoneCall::query()
            ->where([
                'disposition' => PhoneCallDisposition::Pending,
                'type' => PhoneCallType::Outgoing,
                'caller_sip' => $data['internal'],
                'receiver_phone_number' => $data['destination'],
                'pbx_call_id' => null,
            ])
            ->firstOrFail();

        $phoneCall->update([
            'pbx_call_id' => $data['pbx_call_id'],
            'caller_phone_number' => $data['caller_id'],
            'started_at' => $data['call_start'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Outgoing call start saved',
        ]);
    }
}
