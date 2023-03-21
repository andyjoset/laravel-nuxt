<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Fortify\Http\Responses\PasswordConfirmedResponse;
use Laravel\Fortify\Http\Responses\FailedPasswordConfirmationResponse;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as Controller;

class ConfirmablePasswordController extends Controller
{
    /**
     * Confirm the user's password.
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return parent::store($request);
        }

        $confirmed = app(ConfirmPassword::class)(
            $this->guard,
            $request->user(),
            $request->input('password')
        );

        if ($confirmed) {
            Cache::put("auth.password_confirmed_at.$token->token", time(), config('auth.password_timeout', 900));
        }

        return $confirmed
                    ? app(PasswordConfirmedResponse::class)
                    : app(FailedPasswordConfirmationResponse::class);
    }
}
