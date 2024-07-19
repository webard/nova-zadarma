<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Http\SignedRequest;

class IncomingCallEndRequest extends SignedRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function signatureFields(): array
    {
        return [
            'caller_id',
            'caller_did',
            'call_start',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pbx_call_id' => 'required|string',

            'caller_id' => 'phone',

            'called_did' => 'phone',

            'duration' => 'integer',
            'is_recorded' => 'boolean',
            'disposition' => Rule::in(['answered', 'busy', 'cancel', 'no answer', 'failed', 'no money', 'unallocated number', 'no limit', 'no day limit', 'line limit', 'no money, no limit']),
            'status_code' => 'integer',

            'internal' => 'string|nullable',
            'destination' => 'phone',
        ];
    }
}
