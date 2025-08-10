<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_register_a_user()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'password' => 'password123',
        ];
        $user = $this->authService->registerUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
    }

    /** @test */
    public function it_can_create_auth_token()
    {
        $user = User::factory()->create();
        $token = $this->authService->createAuthToken($user);
        $this->assertInstanceOf(\Laravel\Sanctum\NewAccessToken::class, $token);
        $this->assertNotEmpty($token->plainTextToken);
        
        Auth::login($user);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }
}
