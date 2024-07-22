<?php

namespace Webard\NovaZadarma\Http\Requests;

use Webard\NovaZadarma\Http\SignedRequest;

class RecordingSignedRequest extends SignedRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function signatureFields(): array
    {
        return [
            'pbx_call_id',
            'call_id_with_rec',
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
            'call_id_with_rec' => 'required|string',
        ];
    }
}
