<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Artist::with('skills')->get()->map(function ($artist) {
            return [
                'id' => $artist->id,
                'name' => $artist->user->getFullNameAttribute(),
                'profile_image_url' => $artist->profile_image_url,
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
        $data = $request->validated();
    
        // Check if user isn't already an artist
        if (Artist::where('user_id', $data['user_id'])->exists()) {
            return response()->json([
                'message' => 'This user is already an artist'
            ], 422);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store(
                'artists/profile-images',
                'public'
            );
        }
    
        $artist = Artist::create($data);
    
        // Sync skills if provided
        if (isset($data['skills'])) {
            $artist->skills()->sync($data['skills']);
        }
    
        return response()->json([
            'artist' => $artist->load('skills'),
            'profile_image_url' => $artist->profile_image_url
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
        $data = $request->validated();

        // Handle image update
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($artist->profile_image) {
                Storage::disk('public')->delete($artist->profile_image);
            }
            
            $data['profile_image'] = $request->file('profile_image')->store(
                'artists/profile-images',
                'public'
            );
        }

        $artist->update($data);

        if (isset($data['skills'])) {
            $artist->skills()->sync($data['skills']);
        }

        return response()->json([
            'artist' => $artist->fresh()->load('skills'),
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
