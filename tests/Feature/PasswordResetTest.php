<?php

use App\Models\User;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('reset password link can be requested', function () {
    if (! Features::enabled(Features::updatePasswords())) {
        return $this->markTestSkipped('Password updates are not enabled.');
    }

    Notification::fake();

    $this->postJson('/forgot-password', [
        'email' => $this->user->email,
    ]);

    Notification::assertSentTo($this->user, ResetPassword::class);
});

test('password can be reset with valid token', function () {
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
});

test('password cannot be reset with invalid token', function () {
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
});
