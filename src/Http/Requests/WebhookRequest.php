<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webard\NovaZadarma\Traits\HandleValidationResponseAsJson;

class WebhookRequest extends FormRequest
{
    use HandleValidationResponseAsJson;

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
