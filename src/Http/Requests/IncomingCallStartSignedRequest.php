<?php

namespace Webard\NovaZadarma\Http\Requests;

use Webard\NovaZadarma\Http\SignedRequest;

class IncomingCallStartSignedRequest extends SignedRequest
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
            'call_start' => 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date(DATE_ATOM),
            'pbx_call_id' => 'required|string',
            'caller_id' => 'required|phone',
            'called_did' => 'required|phone',
        ];
    }
}
