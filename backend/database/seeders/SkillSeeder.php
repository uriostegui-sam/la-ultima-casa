<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            ['name' => ['en' => 'Watercolor', 'es' => 'Acuarela']],
            ['name' => ['en' => 'Oil Painting', 'es' => 'Óleo']],
            ['name' => ['en' => 'Acrylic Painting', 'es' => 'Pintura Acrílica']],
            ['name' => ['en' => 'Sketching', 'es' => 'Dibujo']],
            ['name' => ['en' => 'Sculpting', 'es' => 'Escultura']],
            ['name' => ['en' => 'Illustration', 'es' => 'Ilustración']],
            ['name' => ['en' => 'Mixed Media', 'es' => 'Técnica Mixta']],
            ['name' => ['en' => 'Printmaking', 'es' => 'Grabado']],
            ['name' => ['en' => 'Calligraphy', 'es' => 'Caligrafía']],
            ['name' => ['en' => 'Ceramics', 'es' => 'Cerámica']],
            ['name' => ['en' => 'Mosaic', 'es' => 'Mosaico']],
        ];
    
        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
