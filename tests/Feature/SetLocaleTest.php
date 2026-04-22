<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

test('set locale from header', function () {
    $response = $this->withHeaders(['Accept-Language' => 'es'])
        ->postJson('/login', [
            'email' => 'wrong-email@example.com',
            'password' => 'password',
        ]);

    expect($this->app->getLocale())->toEqual('es');
    expect('Estas credenciales no coinciden con nuestros registros.')->toEqual($response->json('errors.email.0'));
});

test('set fallback locale if locale is invalid', function () {
    $response = $this->withHeaders(['Accept-Language' => 'wrong-locale'])
        ->postJson('/login', [
            'email' => 'wrong-email@example.com',
            'password' => 'password',
        ]);

    expect($this->app->getLocale())->toEqual('en');
    expect('The provided credentials are incorrect.')->toEqual($response->json('errors.email.0'));
});
