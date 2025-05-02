<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'default_fields' => ['name' => ['es' => '', 'en' => '']]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request)
    {
        $skill = Skill::create($request->validated());
        return response()->json($skill, 201);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $skill->update($request->validated());
        return response()->json($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->noContent();
    }
}
