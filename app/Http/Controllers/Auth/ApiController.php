<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UserAccountBannedException;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'nullable',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->active) {
            throw new UserAccountBannedException;
        }

        $tokenName = $request->device_name ?? 'API Token';

        if ($token = $user->tokens()->where('name', $tokenName)->first()) {
            $token->delete();
        }

        $token = $user->createToken($tokenName);

        return response()->json([
            'token' => $token->plainTextToken,
            'expires' => $token->accessToken->expiresIn,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'OK']);
    }
}
