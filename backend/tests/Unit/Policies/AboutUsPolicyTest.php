<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\AboutUsPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutUsPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected AboutUsPolicy $aboutUsPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aboutUsPolicy = new AboutUsPolicy;
    }
    
    /** @test */
    public function user_can_view_about_us(){        
        $this->assertTrue($this->aboutUsPolicy->viewAny());
    }

    /** @test */
    public function admin_can_create_about_us(){
        $user = User::factory()->admin()->create();
        
        $this->assertTrue($this->aboutUsPolicy->create($user));
    }

    /** @test */
    public function artist_cannot_create_about_us(){
        $artist = User::factory()->artist()->create();
        
        $this->assertFalse($this->aboutUsPolicy->create($artist));
    }

    /** @test */
    public function admin_can_update_about_us(){
        $user = User::factory()->admin()->create();
        
        $this->assertTrue($this->aboutUsPolicy->update($user));
    }

    /** @test */
    public function artist_cannot_update_about_us(){
        $artist = User::factory()->artist()->create();
        
        $this->assertFalse($this->aboutUsPolicy->update($artist));
    }

    /** @test */
    public function admin_can_delete_about_us(){
        $user = User::factory()->admin()->create();
        
        $this->assertTrue($this->aboutUsPolicy->delete($user));
    }

    /** @test */
    public function artist_cannot_delete_about_us(){
        $artist = User::factory()->artist()->create();
        
        $this->assertFalse($this->aboutUsPolicy->delete($artist));
    }
}
