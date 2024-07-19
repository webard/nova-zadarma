<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Http\SignedRequest;

class OutgoingCallEndRequest extends SignedRequest
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
            'pbx_call_id' => 'required|string',

            'caller_id' => 'phone',

            'duration' => 'integer',
            'is_recorded' => 'boolean',
            'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
            'status_code' => 'integer',

            'internal' => 'string|nullable',
            'destination' => 'phone',
        ];
    }
}
