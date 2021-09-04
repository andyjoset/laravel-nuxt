<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_can_be_verified()
    {
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

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(202);
    }

    /** @test */
    public function email_can_not_verified_with_invalid_hash()
    {
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

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    /** @test */
    public function email_verification_notification_can_be_sent()
    {
        if (! Features::enabled(Features::emailVerification())) {
            return $this->markTestSkipped('Email verification not enabled.');
        }

        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->postJson(route('verification.send'));

        Notification::assertSentTo($user, VerifyEmail::class);

        $response->assertStatus(202);
    }

    /** @test */
    public function email_verification_notification_can_not_be_sent_if_email_is_already_verified()
    {
        if (! Features::enabled(Features::emailVerification())) {
            return $this->markTestSkipped('Email verification not enabled.');
        }

        Notification::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('verification.send'));

        Notification::assertNothingSent();

        $response->assertStatus(204);
    }
}
