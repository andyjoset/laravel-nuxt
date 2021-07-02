<?php

namespace App\Exceptions;

use Exception;

class UserAccountBannedException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => __('Your account is currently banned!')], 403);
        }

        return redirect(env('SPA_URL') . '/a/login', 403);
    }
}
