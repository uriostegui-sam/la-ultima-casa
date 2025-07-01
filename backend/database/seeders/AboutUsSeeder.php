<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	AboutUs::create([
            'number' => '044 312 119 5692',
            'cover_image' => 'about_us/images/cover-image.jpg',
            'mail' => 'carlos.perez@gmail.com',
            'address' => [
                'text' => 'Palma Kerpis 152,</br>
                Colinas de Santa Barbara, Sta Bárbara,</br>
                28017 Colima, Col., México',
                'map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15065.757142672133!2d-103.72817832131042!3d19>'
            ],
            'description' => [
                'en' => 'It is an art workshop focused on providing <strong>enriching artistic experiences</strong> for people of all ages>',
                'es' => 'Es un taller de arte enfocado en brindar <strong>experiencias artísticas enriquecedoras</strong> para personas de>'
            ],
        ]);
    }
}
