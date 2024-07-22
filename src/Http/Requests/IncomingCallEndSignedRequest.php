<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Enums\PhoneCallDisposition;
use Webard\NovaZadarma\Http\SignedRequest;

class IncomingCallEndSignedRequest extends SignedRequest
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
            'call_start' => 'required|date_format:Y-m-d H:i:s',
            'pbx_call_id' => 'required|string',
            'caller_id' => 'required|phone',
            'called_did' => 'required|phone',
            'duration' => 'required|integer',
            'status_code' => 'integer',
            'is_recorded' => 'boolean',
            'calltype' => 'string',
            'disposition' => Rule::enum(PhoneCallDisposition::class),

            'internal' => 'required_if:disposition,answered|integer',
            'last_internal' => 'required_if:disposition,answered|integer',
        ];
    }
}
