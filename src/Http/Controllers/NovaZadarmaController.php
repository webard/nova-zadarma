<?php

namespace Webard\NovaZadarma\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class NovaZadarmaController extends Controller
{
    public function getPhoneNumberInfo(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');

        $userModel = config('nova-zadarma.models.user.class');

        $userResource = config('nova-zadarma.nova_resources.user.class');

        try {
            $user = $userModel::query()
                ->where(config('nova-zadarma.models.user.phone_number_field'), $phoneNumber)
                ->firstOrFail();

            $url = '/resources/'.$userResource::uriKey().'/'.$user->id;

            return response()->json([
                'title' => $user->{config('nova-zadarma.models.user.name_field')},
                'resource_url' => $url,
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
