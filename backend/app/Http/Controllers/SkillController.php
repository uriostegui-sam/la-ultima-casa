<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Services\SkillService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Skills",
 *     description="Operations about skills"
 * )
 * 
 * @OA\Schema(
 *     schema="Skills",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 * )
 * @OA\Schema(
 *     schema="SkillsCreate",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 * )
 */
class SkillController extends Controller
{
    public function __construct(
        protected SkillService $skillService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/skills",
     *     security={{"bearerAuth":{}}},
     *     tags={"Skills"},
     *
     *     @OA\Response(response="200", description="Get list of all skills")
     *
     * )
     */
    public function index()
    {
        return SkillResource::collection($this->skillService->getAllSkills());
    }

    /**
     * @OA\Post(
     *     path="/api/skills",
     *     tags={"Skills"},
     *     summary="Create a new skill",
     *     description="Create a new skill with images",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *                 mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/SkillsCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Skill created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Skills")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreSkillRequest $request)
    {
        $this->authorize('create', Skill::class);

        $skill = $this->skillService->createSkill($request->validated());
        return new SkillResource($skill, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/skills/{id}",
     *     tags={"Skills"},
     *     summary="Get skill details",
     *     description="Returns detailed information about a specific skill",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Skill ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Skills")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Skill not found"
     *     )
     * )
     */
    public function show(Skill $skill)
    {
        return new SkillResource($skill);
    }

    /**
     * @OA\Put(
     *     path="/api/skills/{id}",
     *     tags={"Skills"},
     *     summary="Update an skill",
     *     description="Update skill information",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Skill ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *                 mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/SkillsCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Skill updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Skills")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Skill not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        $skill = $this->skillService->updateSkill($skill, $request->validated());
        return new SkillResource($skill);
    }

    /**
    * @OA\Delete(
    *     path="/api/skills/{id}",
    *     tags={"Skills"},
    *     summary="Delete an skill",
    *     description="Delete a specific skill",
    *     security={{"bearerAuth": {}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="Skill ID",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=204,
    *         description="Skill deleted successfully"
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=403,
    *         description="Forbidden"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Skill not found"
    *     )
    * )
    */
    public function destroy(Skill $skill)
    {
        $this->authorize('delete', $skill);
        
        $this->skillService->deleteSkill($skill);
        return response()->noContent();
    }

    public function getPublishedSkills()
    {
        $skills = Skill::where('published', true)->get();
        
        return SkillResource::collection($skills);
    }
}
