<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserAccountBannedException;

class RedirectIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        $response = $next($request);

        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->user();

            if (Auth::guard($guard)->check() && !$user->active) {
                // Invalidate Session
                if ($request->hasSession()) {
                    Auth::guard($guard)->logout();

                    $request->session()->invalidate();

                    $request->session()->regenerateToken();
                }

                // Invalidate Access Token
                if ($request->bearerToken()) {
                    $user->currentAccessToken()->delete();
                }

                throw new UserAccountBannedException;
            }
        }

        return $response;
    }
}
