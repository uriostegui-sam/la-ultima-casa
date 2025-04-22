<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;


    //WIP WORK IN PROGRESS
    /**
     * @test
     */
    public function user_can_register_with_email(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'bye@example.com',
            'password' => 'pass12345',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'bye@example.com']);
    }

    /**
     * @test
     */
    public function user_can_login_with_email(): void
    {
        User::factory()->create([
            'email' => 'bye@example.com',
            'password' => bcrypt('pass12345'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'bye@example.com',
            'password' => 'pass12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test
     */
    public function authenticated_user_can_access_protected_route()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJson(['email' => $user->email]);
    }

    /**
     * @test
     */
    public function login_endpoint_has_rate_limiting()
    {
        User::factory()->create(['email' => 'test@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }
}
