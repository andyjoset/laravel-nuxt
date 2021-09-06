<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken as Model;

class PersonalAccessToken extends Model
{
    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booting()
    {
        static::created(function ($model) {
            if (request()->filled('remember')) {
                Cache::forever($model->cacheKey, $model);
            }
        });

        static::deleted(function ($model) {
            Cache::forget($model->cacheKey);
        });
    }

    /**
     * Determine if the token is long-lived.
     *
     * @return bool
     */
    public function getIsLongLivedAttribute()
    {
        return Cache::has($this->cacheKey);
    }

    /**
     * Get the expiration date of the token.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function getExpirationDateAttribute()
    {
        return $this->created_at->addMinutes(
            $this->isLongLived ? (60 * 24 * 365) : config('sanctum.expiration')
        );
    }

    /**
     * Get the seconds until the token expires.
     *
     * @return int
     */
    public function getExpiresInAttribute()
    {
        return $this->created_at->diffInSeconds($this->expirationDate);
    }

    /**
     * Get the key to use for caching.
     *
     * @return string
     */
    protected function getCacheKeyAttribute()
    {
        return "tokens.long-lived.{$this->getKey()}";
    }
}
