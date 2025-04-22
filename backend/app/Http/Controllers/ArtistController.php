<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\ArtistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    public function __construct(
        protected ArtistService $artistService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Artist::with('skills')->get()->map(function ($artist) {
            return [
                'id' => $artist->id,
                'user_id' => $artist->user_id,
                'name' => $artist->user->getFullNameAttribute(),
                'profile_image' => $artist->profile_image,
                'minibio' => translate($artist->bio),
                'bio' => translate($artist->bio),
                'skills' => $artist->skills->map(fn($skill) => translate($skill->name)),
                'social_links' => [
                    'website' => $artist->social_links['website'] ?? null,
                    'instagram' => $artist->social_links['instagram'] ?? null,
                    'twitter' => $artist->social_links['twitter'] ?? null,
                    'flickr' => $artist->social_links['flickr'] ?? null,
                ],
            ];
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtistRequest $request)
    {
        $artist = $this->artistService->createArtist($request->validated());

        return response()->json([
            'artist' => $artist,
            'profile_image' => $artist->profile_image
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Artist $artist)
    {
        return $artist->load([
            'specialties',
            'artworks' => function($query) {
                $query->latest()->limit(5);
            },
            'user'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artist $artist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtistRequest $request, Artist $artist)
    {
        $artist = $this->artistService->updateArtist($artist, $request->validated());

        return response()->json([
            'artist' => $artist,
            'profile_image_url' => $artist->profile_image_url
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artist $artist)
    {
        // Prevent deletion if has related artworks
        if ($artist->artworks()->exists()) {
            return response()->json([
                'message' => 'Cannot delete artist with existing artworks'
            ], 422);
        }

        // Delete profile image if exists
        if ($artist->profile_image) {
            Storage::disk('public')->delete($artist->profile_image);
        }

        $artist->delete();

        return response()->noContent();
    }
}
