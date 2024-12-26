<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        static::updating(function (self $token) {
            static::cache()->remember("lastUsageUpdate.{$token->cacheKey}", 3600, function () use ($token) {
                $token->updateLastUsageUpdate($token->getDirty());
                return now();
            });

            return false;
        });

        static::created(function (self $token) {
            if (request()->filled('remember')) {
                static::cache()->forever("long-lived.{$token->cacheKey}", $token);
            }
        });

        static::deleted(function (self $token) {
            static::cache()->forget($token->cacheKey);

            if ($token->isLongLived) {
                static::cache()->forget("long-lived.{$token->cacheKey}");
            }
        });
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        $token = static::cache()->remember($token, config('sanctum.expiration'), function () use ($token) {
            return parent::findToken($token) ?? '_null_';
        });

        if ($token === '_null_') {
            return null;
        }

        return $token;
    }

    /**
     * Determine if the token is long-lived.
     */
    public function getIsLongLivedAttribute(): bool
    {
        return static::cache()->has("long-lived.{$this->cacheKey}");
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
        return $this->created_at->diffInSeconds($this->expires_at);
    }

    /**
     * Update the last usage update manually.
     */
    public function updateLastUsageUpdate(array $attrs): void
    {
        try {
            DB::table($this->getTable())
                ->where($this->getKeyName(), $this->getKey())
                ->update($attrs);
        } catch (\Exception $e) {
            logger()->critical($e->getMessage());
        }
    }

    /**
     * Get the key to use for caching.
     */
    protected function getCacheKeyAttribute(): string
    {
        return $this->getKey();
    }

    /**
     * Get the tagged cache instance.
     */
    protected static function cache(): TaggedCache
    {
        return Cache::tags('personal_access_tokens');
    }
}
