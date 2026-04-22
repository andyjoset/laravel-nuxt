<?php

use App\Models\User;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

test('email can be verified', function () {
    if (! Features::enabled(Features::emailVerification())) {
        return $this->markTestSkipped('Email verification not enabled.');
    }

    Event::fake();

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->getJson($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertStatus(204);
});

test('email can not verified with invalid hash', function () {
    if (! Features::enabled(Features::emailVerification())) {
        return $this->markTestSkipped('Email verification not enabled.');
    }

    Event::fake();

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'api.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $response = $this->actingAs($user)->getJson($verificationUrl);

    Event::assertNotDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    $response->assertStatus(403);
});

test('email verification notification can be sent', function () {
    if (! Features::enabled(Features::emailVerification())) {
        return $this->markTestSkipped('Email verification not enabled.');
    }

    Notification::fake();

    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->postJson(route('verification.send'));

    Notification::assertSentTo($user, VerifyEmail::class);

    $response->assertStatus(202);
});

test('email verification notification can not be sent if email is already verified', function () {
    if (! Features::enabled(Features::emailVerification())) {
        return $this->markTestSkipped('Email verification not enabled.');
    }

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('verification.send'));

    Notification::assertNothingSent();

    $response->assertStatus(204);
});
