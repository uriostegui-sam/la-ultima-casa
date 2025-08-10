<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;


    //WIP WORK IN PROGRESS
    /**
     * @test
     */
    // public function user_can_register_with_email(): void
    // {
    //     $response = $this->postJson('/api/auth/register', [
    //         'name' => 'Test User',
    //         'email' => 'bye@example.com',
    //         'password' => 'pass12345',
    //     ]);

    //     $response->assertStatus(201)
    //         ->assertJsonStructure(['user', 'token']);

    //     $this->assertDatabaseHas('users', ['email' => 'bye@example.com']);
    // }

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

    /**
     * @test
     */
    public function user_can_update_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $response = $this->actingAs($user)->postJson('/api/auth/update-password', [
            'password' => 'oldpassword',
            'newPassword' => 'newpassword',
            'newPassword_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'successPassword']);

        $this->assertTrue(password_verify('newpassword', $user->fresh()->password));
    }

    /**
     * @test
     */
    public function user_cannot_update_password_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);
        $response = $this->actingAs($user)->postJson('/api/auth/update-password', [
            'password' => 'wrongpassword',
            'newPassword' => 'newpassword',
            'newPassword_confirmation' => 'newpassword',
        ]);
        $response->assertStatus(422)
            ->assertJson(['message' => 'incorrectPassword']);
    }

    /**
     * @test
     */
    public function admin_can_generate_reset_token()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/auth/generate-reset-token', [
            'id' => $user->id,
            'id_admin' => $admin->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'resetTokenCreated']);

        $this->assertDatabaseHas('password_resets', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function admin_cannot_generate_reset_token_for_non_admin_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/auth/generate-reset-token', [
            'id' => $user->id,
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'No query results for model [App\\Models\\User].']);
    }

    /**
     * @test
     */
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
            'must_change_password' => true,
        ]);
        
        $token = Str::random(40);

        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addDays(3),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'new_password' => 'newpassword1',
            'new_password_confirmation' => 'newpassword1',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'resetPasswordSuccess']);

        $this->assertTrue(password_verify('newpassword1', $user->fresh()->password));
        $this->assertDatabaseMissing('password_resets', [
            'user_id' => $user->id,
            'token' => $token,
        ]);
    }

    /**
     * @test
     */
    public function user_cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
            'must_change_password' => true,
        ]);
        
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalidtoken',
            'new_password' => 'newpassword1',
            'new_password_confirmation' => 'newpassword1',
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['message' => 'invalidToken']);
        
        $this->assertTrue(password_verify('oldpassword', $user->fresh()->password));
    }
}
