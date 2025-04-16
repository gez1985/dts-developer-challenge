<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        // Ensure the token is stored in the personal_access_tokens table
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'App\Models\User',
        ]);
    }


    public function test_user_cannot_log_in_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $this->assertGuest();
    }

    public function test_login_fails_validation()
    {
        // Missing both fields
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);

        // Invalid email format
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'secret',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        // Missing password
        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_authenticated_user_can_access_a_protected_api_route()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/test-protected');

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_guest_cannot_access_a_protected_api_route()
    {
        $response = $this->getJson('/api/test-protected');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_log_out()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        // Ensure the token is stored
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'App\Models\User',
        ]);

        // Perform the logout request
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out']);

        // Ensure the token is deleted from the database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        // Check that the token is no longer valid by checking the user's tokens
        $this->assertCount(0, $user->tokens);
    }
}
