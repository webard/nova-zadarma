<?php

namespace Webard\NovaZadarma\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

trait HandleValidationResponseAsJson
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => [
                'signature' => ['Invalid signature'],
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
