<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use App\Policies\ArtworkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworkPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected ArtworkPolicy $artworkPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artworkPolicy = new ArtworkPolicy();
    }

    /** @test */
    public function user_can_view_any_artwork()
    {
        $user = User::factory()->create();
        $this->assertTrue($this->artworkPolicy->viewAny($user));
    }

    /** @test */
    public function user_can_create_artwork_if_artist_or_admin()
    {
        $artist = User::factory()->admin()->create();
        $admin = User::factory()->artist()->create();

        $this->assertTrue($this->artworkPolicy->create($artist));
        $this->assertTrue($this->artworkPolicy->create($admin));
    }

    /** @test */
    public function user_can_update_artwork_if_it_belongs_to_them()
    {
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create(['user_id' => $user->id]);
        $artwork = Artwork::factory(['artist_id' => $artist->id])->withImages()->create();

        $this->assertTrue($this->artworkPolicy->update($user, $artwork));
    }

    /** @test */
    public function user_cannot_update_artwork_if_it_does_not_belong_to_them()
    {
        $user = User::factory()->artist()->create();
        $artwork = Artwork::factory()->withImages()->create();

        $this->assertFalse($this->artworkPolicy->update($user, $artwork));
    }

    /** @test */
    public function admin_can_update_any_artwork()
    {
        $user = User::factory()->admin()->create();
        $artwork = Artwork::factory()->withImages()->create();

        $this->assertTrue($this->artworkPolicy->update($user, $artwork));
    }

    /** @test */
    public function user_can_delete_artwork_if_it_belongs_to_them()
    {
        $user = User::factory()->artist()->create();
        $artist = Artist::factory()->create(['user_id' => $user->id]);
        $artwork = Artwork::factory(['artist_id' => $artist->id])->withImages()->create();

        $this->assertTrue($this->artworkPolicy->delete($user, $artwork));
    }

    /** @test */
    public function user_cannot_delete_artwork_if_it_does_not_belong_to_them()
    {
        $user = User::factory()->artist()->create();
        $artwork = Artwork::factory()->withImages()->create();

        $this->assertFalse($this->artworkPolicy->delete($user, $artwork));
    }

    /** @test */
    public function admin_can_delete_any_artwork()
    {
        $user = User::factory()->admin()->create();
        $artwork = Artwork::factory()->withImages()->create();

        $this->assertTrue($this->artworkPolicy->delete($user, $artwork));
    }
}
