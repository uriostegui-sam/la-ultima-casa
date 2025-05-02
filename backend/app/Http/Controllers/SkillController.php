<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
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
        return Skill::all()->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name[app()->getLocale()] ?? $skill->name['es']
            ];
        });
    }

    public function create()
    {
        return response()->json([
            'default_fields' => ['name' => ['es' => '', 'en' => '']]
        ]);
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
        $skill = Skill::create($request->validated());
        return response()->json($skill, 201);
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
        return response()->json([
            'id' => $skill->id,
            'name' => $skill->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill)
    {
        return response()->json([
            'skill' => $skill,
            'available_languages' => ['es', 'en']
        ]);
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
        $skill->update($request->validated());
        return response()->json($skill);
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
        $skill->delete();
        return response()->noContent();
    }
}
