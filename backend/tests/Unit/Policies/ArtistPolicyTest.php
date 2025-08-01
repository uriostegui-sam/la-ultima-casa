<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\User;
use App\Policies\ArtistPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected ArtistPolicy $artistPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artistPolicy = new ArtistPolicy;
    }
    
    /** @test */
    public function user_can_view_any_artist(){        
        $this->assertTrue($this->artistPolicy->viewAny());
    }

    /** @test */
    public function admin_can_create_artist(){
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($this->artistPolicy->create($admin));
    }

    /** @test */
    public function artist_cannot_create_artist(){
        $artist = User::factory()->artist()->create();
        
        $this->assertFalse($this->artistPolicy->create($artist));
    }

    /** @test */
    public function admin_can_update_any_artist(){
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        
        $this->assertTrue($this->artistPolicy->update($admin, $artist));
    }

    /** @test */
    public function artist_can_update_their_artist_profile(){
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($this->artistPolicy->update($user, $artist));
    }

    /** @test */
    public function artist_cannot_update_another_artist_profile(){
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create();

        $this->assertFalse($this->artistPolicy->update($user, $artist));
    }

    /** @test */
    public function admin_can_delete_any_artist(){
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        
        $this->assertTrue($this->artistPolicy->delete($admin, $artist));
    }

    /** @test */
    public function artist_cannot_delete_their_artist_profile(){
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $this->assertFalse($this->artistPolicy->delete($user, $artist));
    }

    /** @test */
    public function artist_cannot_delete_another_artist_profile(){
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create();

        $this->assertFalse($this->artistPolicy->delete($user, $artist));
    }
}
