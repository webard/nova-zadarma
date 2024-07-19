<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Http\SignedRequest;

class OutgoingCallEndSignedRequest extends SignedRequest
{
    public function signatureFields(): array
    {
        return [
            'internal',
            'destination',
            'call_start',
        ];
    }

    public function rules(): array
    {
        return [
            'call_start' => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date(DATE_ATOM),
            'pbx_call_id' => 'required|string',
            'caller_id' => 'nullable|phone',
            // ? In documentation marked as optional, but in our implementation every caller should have a SIP
            'internal' => 'required|integer',
            'destination' => 'required|phone',
            // ? In seconds
            'duration' => 'integer',
            // ? Not used
            'status_code' => 'integer',
            // ? Not used, recording comes in a separate webhook
            'is_recorded' => 'boolean',
            // ? Present in webhook data but not in the documentation
            'calltype' => 'string',

            // TODO: move to a separate rule
            'disposition' => Rule::enum(PhoneCallDisposition::class),
            // 'disposition' => Rule::in([
            //     'answered',
            //     'busy',
            //     'cancel',
            //     'no answer',
            //     'failed',
            //     'no money',
            //     'unallocated number',
            //     'no limit',
            //     'no day limit',
            //     'line limit',
            //     'no money, no limit'
            // ]),

        ];
    }
}
