<?php

namespace App\Actions\Fortify;

use App\Http\Resources\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            if ($request->routeIs('api.register')) {
                $token = $request->user()->createToken('API Token');

                return response()->json([
                    'token' => $token->plainTextToken,
                    'expires' => $token->accessToken->expiresIn,
                ], 201);
            }

            return response()->json(new Auth($request->user()), 201);
        }

        return redirect(config('fortify.home'));
    }
}
