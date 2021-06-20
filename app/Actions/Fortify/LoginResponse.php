<?php

namespace App\Actions\Fortify;

use App\Http\Resources\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return $request->expectsJson()
            ? new Auth($request->user())
            : redirect(config('fortify.home'));
    }
}
