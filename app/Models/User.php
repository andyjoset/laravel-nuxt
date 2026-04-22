<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'active', 'avatar', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

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
