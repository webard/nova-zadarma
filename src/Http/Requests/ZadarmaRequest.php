<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Traits\HandleValidationResponsesAsJson;

class ZadarmaRequest extends FormRequest
{
    use HandleValidationResponsesAsJson;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event' => 'required|'.Rule::in([
                'NOTIFY_START',
                'NOTIFY_END',
                'NOTIFY_OUT_START',
                'NOTIFY_OUT_END',
                'NOTIFY_RECORD',
            ]),
        ];
    }
}
