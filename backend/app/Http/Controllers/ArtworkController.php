<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtworkRequest;
use App\Http\Requests\UpdateArtworkRequest;
use App\Models\Artwork;
use App\Services\ArtworkService;

class ArtworkController extends Controller
{
    public function __construct(
        protected ArtworkService $artworkService
    ) {}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Artwork::with('artist.user')->latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtworkRequest $request)
    {
        $artwork = $this->artworkService->createArtwork(
            $request->validated(),
            $request->file('image')
        );

        return response()->json($artwork, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Artwork $artwork)
    {
        return $artwork->load('artist.user');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtworkRequest $request, Artwork $artwork)
    {
        $this->authorize('update', $artwork);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        $this->authorize('delete', $artwork);
    }
}
