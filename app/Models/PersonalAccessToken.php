<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken as Model;

class PersonalAccessToken extends Model
{
    /**
     * Perform any actions required before the model boots.
     */
    protected static function booting(): void
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
     */
    public function getIsLongLivedAttribute(): bool
    {
        return Cache::has($this->cacheKey);
    }

    /**
     * Get the expiration date of the token.
     */
    public function getExpirationDateAttribute(): Carbon
    {
        return $this->created_at->addMinutes(
            $this->isLongLived ? (60 * 24 * 365) : config('sanctum.expiration')
        );
    }

    /**
     * Get the seconds until the token expires.
     */
    public function getExpiresInAttribute(): int
    {
        return $this->created_at->diffInSeconds($this->expirationDate);
    }

    /**
     * Get the key to use for caching.
     */
    public function getCacheKeyAttribute(): string
    {
        return "tokens.long-lived.{$this->getKey()}";
    }
}
