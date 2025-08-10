<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\SkillPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected SkillPolicy $skillPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skillPolicy = new SkillPolicy;
    }

    /** @test */
    public function user_can_view_any_skill(){
        $this->assertTrue($this->skillPolicy->viewAny());
    }

    /** @test */
    public function user_cannot_create_skill(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->skillPolicy->delete($user));
    }

    /** @test */
    public function admin_can_create_skill(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->skillPolicy->delete($admin));
    }

    /** @test */
    public function user_cannot_update_skill(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->skillPolicy->update($user));
    }

    /** @test */
    public function admin_can_update_skill(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->skillPolicy->update($admin));
    }

    /** @test */
    public function user_cannot_delete_skill(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->skillPolicy->delete($user));
    }

    /** @test */
    public function admin_can_delete_skill(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->skillPolicy->delete($admin));
    }
}
