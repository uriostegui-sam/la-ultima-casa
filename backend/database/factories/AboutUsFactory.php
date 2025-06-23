<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AboutUs>
 */
class AboutUsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => '044 312 119 5692',
            'cover_image' => 'about_us/images/cover-image.jpg',
            'mail' => 'carlos.perez@gmail.com',
            'address' => [
                'text' => 'Palma Kerpis 152,</br>
                Colinas de Santa Barbara, Sta Bárbara,</br>
                28017 Colima, Col., México',
                'map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15065.757142672133!2d-103.72817832131042!3d19.26325381168291!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x84255b09965e0ce3%3A0xc086d17bda474fa2!2sEstudio%20La%20%C3%9Altima%20Casa!5e0!3m2!1ses-419!2sfr!4v1750693866012!5m2!1ses-419!2sfr" width="300" height="225" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            ],
            'description' => [
                'en' => 'It is an art workshop focused on providing <strong>enriching artistic experiences</strong> for people of all ages and skill levels. It offers an inspiring environment surrounded by art and enthusiastic students, where <strong>participants can learn painting techniques in a friendly, judgment-free setting</strong>. The studio provides professional materials so attendees can focus on the creative process and <strong>take home vibrant, colorful artworks</strong>.',
                'es' => 'Es un taller de arte enfocado en brindar <strong>experiencias artísticas enriquecedoras</strong> para personas de todas las edades y niveles de experiencia. Ofrece un ambiente inspirador rodeado de arte y estudiantes entusiastas, donde <strong   >los participantes pueden aprender técnicas de pintura en un entorno amigable y sin   juicios</strong >. El estudio proporciona materiales profesionales para que los asistentes puedan concentrarse en el proceso creativo y <strong>llevarse a casa obras llenas de vida y color</strong>.',
            ],
        ];
    }
}
