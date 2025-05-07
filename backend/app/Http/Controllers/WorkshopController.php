<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Http\Resources\WorkshopCollection;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use App\Services\WorkshopService;

class WorkshopController extends Controller
{
    public function __construct(
        protected WorkshopService $workshopService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new WorkshopCollection(
            Workshop::with(['artist', 'skills'])->get()
        );    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkshopRequest $request)
    {
        $this->authorize('create', Workshop::class);

        $workshop = $this->workshopService->createWorkshop(
            $request->validated(),
            $request->file('cover_image')
        );

        return new WorkshopResource($workshop);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop)
    {
        return new WorkshopResource($workshop->load(['artist', 'skills']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkshopRequest $request, Workshop $workshop)
    {
        $this->authorize('update', $workshop);

        $workshop = $this->workshopService->updateWorkshop(
            $workshop,
            $request->validated(),
            $request->file('cover_image')
        );

        return new WorkshopResource($workshop);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        $this->authorize('delete', $workshop);

        $this->workshopService->deleteWorkshop($workshop);

        return response()->json([
            'message' => 'Workshop deleted successfully'
        ]);
    }
}