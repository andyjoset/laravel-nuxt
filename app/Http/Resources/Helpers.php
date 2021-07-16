<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\MissingValue;

trait Helpers
{
    /**
     * Retrieve a value when it's not null.
     *
     * @param  mixed $value
     * @param  mixed $default
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenNotNull($value, $default = null)
    {
        return $value ?? $default ?? new MissingValue;
    }

    /**
     * Retrieve a value when the user is authenteicated.
     *
     * @param  mixed $value
     * @param  mixed $default
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function whenAuthenticated($value, $default = null)
    {
        return Auth::check() ? $value : $default ?? new MissingValue;
    }
}
