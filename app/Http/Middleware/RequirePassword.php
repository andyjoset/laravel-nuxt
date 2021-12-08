<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RequirePassword as Middleware;

class RequirePassword extends Middleware
{
    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldConfirmPassword($request)
    {
        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return parent::shouldConfirmPassword($request);
        }

        $confirmedAt = time() - cache("auth.password_confirmed_at.$token->token", 0);

        return $confirmedAt > $this->passwordTimeout;
    }
}
