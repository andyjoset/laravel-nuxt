<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    public function test_set_locale_from_header()
    {
        $response = $this->withHeaders(['Accept-Language' => 'es'])
            ->postJson('/login', [
                'email' => 'wrong-email@example.com',
                'password' => 'password',
            ]);

        $this->assertEquals('es', $this->app->getLocale());
        $this->assertEquals(
            $response->json('errors.email.0'),
            'Estas credenciales no coinciden con nuestros registros.',
        );
    }

    public function test_set_fallback_locale_if_locale_is_invalid()
    {
        $response = $this->withHeaders(['Accept-Language' => 'wrong-locale'])
            ->postJson('/login', [
                'email' => 'wrong-email@example.com',
                'password' => 'password',
            ]);

        $this->assertEquals('en', $this->app->getLocale());
        $this->assertEquals(
            $response->json('errors.email.0'),
            'The provided credentials are incorrect.',
        );
    }
}
