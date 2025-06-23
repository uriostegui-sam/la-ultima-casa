<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carlos = User::factory()->create([
            'first_name' => 'Carlos',
            'last_name' => 'Pérez',
            'email' => 'carlos.perez@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'artist',
        ]);

        Artist::factory()
            ->for($carlos)
            ->withSkills()
            ->withArtworks(3)
            ->create();


        Artist::factory()
            ->count(9)
            ->has(
                Artwork::factory()
                    ->count(3)
                    ->withImages()
            )
            ->withSkills()
            ->create();
    }
}
