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
	$carlos = User::create([
            'first_name' => 'Carlos',
            'last_name' => 'Pérez',
            'email' => 'carlos.perez@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
        ]);

        $artist = Artist::create([
            'user_id' => $carlos->id,
            'profile_image' => 'artists/images/carlos-profile.jpg',
            'minibio' => [
                'en' => 'Mexican visual artist and painter.',
                'es' => 'Artista visual y pintor mexicano.',
            ],
            'bio' => [
                'en' => 'Carlos has participated in numerous exhibitions...',
                'es' => 'Carlos ha participado en numerosas exposiciones...',
            ],
            'social_links' => [
                'instagram' => 'carlos_perez_art',
            ],
        ]);

        $artist->skills()->attach([1, 2, 3]);
    }
}
