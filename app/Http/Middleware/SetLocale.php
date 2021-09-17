<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locales = config('app.locales');
        $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        if (array_key_exists($locale, $locales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
