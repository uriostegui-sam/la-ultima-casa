<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\NewsPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPolicyTest extends TestCase
{
    use RefreshDatabase;
    private NewsPolicy $newsPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsPolicy = new NewsPolicy;
    }

    /** @test */
    public function user_can_view_any_news() {
        $this->assertTrue($this->newsPolicy->viewAny());
    }

    /** @test */
    public function user_cannot_create_news(){
        $user = User::factory()->artist()->create();

        $this->assertFalse($this->newsPolicy->create($user));
    }

    /** @test */
    public function admin_can_create_news(){
        $admin = User::factory()->admin()->create();

        $this->assertTrue($this->newsPolicy->create($admin));
    }

    /** @test */
    public function user_cannot_update_news(){
        $user = User::factory()->artist()->create();

        $this->assertFalse($this->newsPolicy->update($user));
    }

    /** @test */
    public function admin_can_update_news(){
        $admin = User::factory()->admin()->create();

        $this->assertTrue($this->newsPolicy->update($admin));
    }

    /** @test */
    public function user_cannot_delete_news(){
        $user = User::factory()->artist()->create();

        $this->assertFalse($this->newsPolicy->delete($user));
    }

    /** @test */
    public function admin_can_delete_news(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->newsPolicy->delete($admin));
    }
}
