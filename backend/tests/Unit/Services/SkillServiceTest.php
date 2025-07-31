<?php

namespace Tests\Unit;

use App\Models\Skill;
use App\Services\SkillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkillServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SkillService $skillService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->skillService = new SkillService();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_skill(): void
    {
        $data = [
            'name' => ['en' => 'Test Skill', 'es' => 'Habilidad de prueba'],
            'profile_image' => UploadedFile::fake()->create('skill.jpg', 100, 'image/jpeg'),
            'published' => true,
        ];
        
        $skill = $this->skillService->createSkill($data);
        $this->assertInstanceOf(Skill::class, $skill);
        $this->assertEquals('Test Skill', $skill->name['en']);
        $this->assertDatabaseHas('skills', [
            'published' => true,
        ]);
        $this->assertNotNull($skill->profile_image);
    }

    /** @test */
    public function it_can_update_skill(): void
    {
        $skill = Skill::factory()->create();
        $data = [
            'name' => ['en' => 'Updated Skill', 'es' => 'Habilidad actualizada'],
            'profile_image' => UploadedFile::fake()->create('updated_skill.jpg', 100, 'image/jpeg'),
            'published' => false,
        ];
        $updatedSkill = $this->skillService->updateSkill($skill, $data);

        $this->assertInstanceOf(Skill::class, $updatedSkill);
        $this->assertEquals('Updated Skill', $updatedSkill->name['en']);
        $this->assertFalse($updatedSkill->published);
        $this->assertNotNull($updatedSkill->profile_image);
    }

    /** @test */
    public function it_can_delete_skill(): void
    {
        $skill = Skill::factory()->create();
        $this->skillService->deleteSkill($skill);

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
