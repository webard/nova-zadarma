<?php

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Webard\NovaZadarma\Contract\PhoneNumberInfoContract;

class NovaZadarmaController extends Controller
{
    public function getPhoneNumberInfo(Request $request)
    {
        $className = config('nova-zadarma.phone_number_info_handler');

        $phoneNumber = $request->input('phoneNumber');

        try {
            $phoneNumberInfo = new $className($phoneNumber);

            assert($phoneNumberInfo instanceof PhoneNumberInfoContract);

            return response()->json([
                'title' => $phoneNumberInfo->getTitle(),
                'resource_url' => $phoneNumberInfo->getResourceUrl(),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'error' => 'not_found',
            ]);
        } catch (ValidationException) {
            return response()->json([
                'error' => 'not_valid_phone_number',
            ]);
        }
    }
}
