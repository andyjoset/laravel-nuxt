<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class UserAccountBannedException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => __('Your account is currently banned!')], 403);
        }

        return redirect(env('SPA_URL') . '/a/login', 403);
    }
}
