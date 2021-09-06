<?php

namespace App\Providers;

use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::after(function ($user, $ability, $result, $arguments) {
            return $user->hasRole('Super Admin');
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env('SPA_URL') . "/a/reset-password/{$token}?email={$user->email}";
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $appUrl = env('SPA_URL', config('app.url'));

            $verifyUrl = URL::temporarySignedRoute(
                'api.verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return $appUrl . '/a/email/verify?verify_url=' . urlencode($verifyUrl);
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Sanctum::authenticateAccessTokensUsing(function ($accessToken, $isValid) {
            if ($accessToken->isLongLived) {
                $isValid = now()->lt($accessToken->expirationDate);
            }

            if (!$isValid) {
                $accessToken->delete();
            }

            return $isValid;
        });
    }
}
