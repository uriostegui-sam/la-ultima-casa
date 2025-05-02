<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_skill()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/skills', [
                'name' => [
                    'en' => 'Painting',
                    'es' => 'Pintura'
                ]
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name'
                ]
            ]);
    }

    public function test_guest_cannot_create_skill()
    {
        $response = $this->postJson('/api/skills', [
            'name' => ['en' => 'Test']
        ]);

        $response->assertUnauthorized();
    }

    public function test_non_admin_cannot_create_skill()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/skills', [
                'name' => ['en' => 'Test']
            ]);

        $response->assertForbidden();
    }

    public function test_can_list_skills()
    {
        Skill::factory()->count(3)->create();

        $response = $this->getJson('/api/skills');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_sees_translations()
    {
        $admin = User::factory()->admin()->create();
        $skill = Skill::factory()->create();

        $response = $this->actingAs($admin)
            ->getJson("/api/skills/{$skill->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'translations'
                ]
            ]);
    }
}
