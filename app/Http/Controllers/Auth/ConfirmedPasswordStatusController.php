<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController as Controller;

class ConfirmedPasswordStatusController extends Controller
{
    /**
     * Get the password confirmation status.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return parent::show($request);
        }

        return response()->json([
            'confirmed' => (time() - cache("auth.password_confirmed_at.$token->token", 0)) < $request->input('seconds', config('auth.password_timeout', 900)),
        ]);
    }
}
