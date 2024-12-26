<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable // implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * @var string
     */
    public const DEFAULT_AVATAR_PATH = 'default-avatar.png';

    /**
     * Perform any actions required before the model boots.
     */
    public static function booting(): void
    {
        static::creating(function ($model) {
            $model->active = $model->active ?? true;
            $model->avatar = $model->avatar ?? static::DEFAULT_AVATAR_PATH;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'active',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function generateToken(?string $name = null): array
    {
        $tokenName = $name ?? 'API Token';

        if ($token = $this->tokens()->where('name', $tokenName)->first()) {
            $token->delete();
        }

        $expires = now();
        $expirationTime = request()->filled('remember') ? '30 day' : config('sanctum.expiration'). 'minutes';
        $token = $this->createToken($tokenName, expiresAt: $expires->add($expirationTime));

        return [
            'token' => $token->plainTextToken,
            'expires' => $token->accessToken->expiresIn,
        ];
    }
}
