<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_information_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->putJson('/user/profile-information', [
            'name'   => 'Test Name',
            'email'  => 'test@example.com',
        ]);

        $user->fresh();

        $this->assertEquals('Test Name', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /** @test */
    public function profile_information_cannot_be_updated_with_duplicated_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($user = User::factory()->create());

        $this->putJson('/user/profile-information', [
            'name'   => 'Test Name',
            'email'  => 'test@example.com',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['email']
        ]);
    }
}
