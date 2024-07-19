<?php

namespace Webard\NovaZadarma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webard\NovaZadarma\Traits\HandleValidationResponsesAsJson;

abstract class WebhookRequest extends FormRequest
{
    use HandleValidationResponsesAsJson;

    abstract public function signatureFields(): array;

    public function authorize(): bool
    {
        if (config('nova-zadarma.webhook_verify_signature', true) === false) {
            return true;
        }

        $requestSignature = $this->requestSignature();

        if ($requestSignature === null) {
            return false;
        }

        return hash_equals($requestSignature, $this->ourSignature());
    }

    protected function requestSignature(): ?string
    {
        return $this->header('Signature');
    }

    protected function ourSignature(): string
    {
        $string = collect($this->request->all())
            ->filter(fn ($value, $key) => in_array($key, $this->signatureFields(), true))
            ->implode('');

        return base64_encode(hash_hmac('sha1', $string, config('nova-zadarma.auth.secret')));
    }
}
