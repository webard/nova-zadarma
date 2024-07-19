<?php

namespace Webard\NovaZadarma\Http\Requests;

class IncomingCallStartRequest extends WebhookRequest
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
            'call_start' => 'required|date',
            'caller_id' => 'phone',
            'called_did' => 'phone',
        ];
    }
}
