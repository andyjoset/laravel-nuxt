<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_reset_password_link_can_be_requested()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $this->postJson('/forgot-password', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $this->postJson('/forgot-password', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $response = $this->postJson('/reset-password', [
                'token' => $notification->token,
                'email' => $this->user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }

    public function test_password_cannot_be_reset_with_invalid_token()
    {
        if (! Features::enabled(Features::updatePasswords())) {
            return $this->markTestSkipped('Password updates are not enabled.');
        }

        Notification::fake();

        $this->postJson('/forgot-password', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo($this->user, ResetPassword::class, function ($notification) {
            $response = $this->postJson('/reset-password', [
                'token' => 'invalid-token',
                'email' => $this->user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertStatus(422);

            return true;
        });
    }
}
