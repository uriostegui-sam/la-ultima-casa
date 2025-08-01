<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\WorkshopPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected WorkshopPolicy $workshopPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workshopPolicy = new WorkshopPolicy;
    }

    /** @test */
    public function user_can_view_any_workshop(){
        $this->assertTrue($this->workshopPolicy->viewAny());
    }

    /** @test */
    public function user_cannot_create_workshop(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->workshopPolicy->delete($user));
    }

    /** @test */
    public function admin_can_create_workshop(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->workshopPolicy->delete($admin));
    }

    /** @test */
    public function user_cannot_update_workshop(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->workshopPolicy->update($user));
    }

    /** @test */
    public function admin_can_update_workshop(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->workshopPolicy->update($admin));
    }

    /** @test */
    public function user_cannot_delete_workshop(){
        $user = User::factory()->create();
        
        $this->assertFalse($this->workshopPolicy->delete($user));
    }

    /** @test */
    public function admin_can_delete_workshop(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->workshopPolicy->delete($admin));
    }
}
