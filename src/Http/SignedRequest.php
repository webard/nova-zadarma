<?php

namespace Webard\NovaZadarma\Http;

use Illuminate\Foundation\Http\FormRequest;
use Webard\NovaZadarma\Traits\HandleValidationResponseAsJson;

abstract class SignedRequest extends FormRequest
{
    use HandleValidationResponseAsJson;

    protected function fixRequestPhoneNumbers(): void
    {
        // for some reason, caller_id or called_did sometimes does not have "+" at the beginning
        // trying to fix this to make it work with validator
        $calledDid = $this->input('called_did');
        if ($calledDid !== null && str_contains($calledDid, '+') === false) {
            $this->merge(['called_did' => '+'.$calledDid]);
        }

        $callerId = $this->input('caller_id');
        if ($callerId !== null && str_contains($callerId, '+') === false) {
            $this->merge(['caller_id' => '+'.$callerId]);
        } elseif ($callerId === '0') {
            $this->merge(['caller_id' => null]);
        }
    }

    public function validationData()
    {
        $this->fixRequestPhoneNumbers();

        return $this->all();
    }

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
