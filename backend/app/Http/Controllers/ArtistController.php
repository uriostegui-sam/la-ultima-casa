<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\ArtistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Artists",
 *     description="Operations about artists"
 * )
 * 
 * @OA\Schema(
 *     schema="Artist",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="profile_image", type="string", nullable=true),
 *     @OA\Property(
 *         property="minibio",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="bio",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="social_links",
 *         type="object",
 *         @OA\Property(property="website", type="string"),
 *         @OA\Property(property="instagram", type="string"),
 *         @OA\Property(property="twitter", type="string"),
 *         @OA\Property(property="flickr", type="string")
 *     ),
 *     @OA\Property(property="skills", type="array", @OA\Items(type="string")),
 * )
 * 
 * @OA\Schema(
 *     schema="ArtistCreate",
 *     type="object",
 *     required={"user_id", "minibio", "bio"},
 *     @OA\Property(property="profile_image", type="string", format="binary", nullable=true),
 *     @OA\Property(
 *         property="minibio",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="bio",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="social_links",
 *         type="object",
 *         @OA\Property(property="website", type="string", format="url", nullable=true),
 *         @OA\Property(property="instagram", type="string", nullable=true),
 *         @OA\Property(property="twitter", type="string", nullable=true),
 *         @OA\Property(property="flickr", type="string", format="url", nullable=true)
 *     ),
 *     @OA\Property(property="skills", type="array", @OA\Items(type="integer"))
 * )
 */
class ArtistController extends Controller
{
    public function __construct(
        protected ArtistService $artistService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/artists",
     *     tags={"Artists"},
     *     summary="Get list of all artists",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of artists",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Artist"))
     *     )
     * )
     */
    public function index()
    {
        return new ArtistCollection(
            Artist::with(['user', 'skills'])->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/artists",
     *     tags={"Artists"},
     *     summary="Create a new artist",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ArtistCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Artist created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreArtistRequest $request)
    {
        $artist = $this->artistService->createArtist($request->validated());
        return new ArtistResource($artist);
    }

    /**
     * @OA\Get(
     *     path="/api/artists/{id}",
     *     tags={"Artists"},
     *     summary="Get artist details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Artist ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist details",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function show(Artist $artist)
    {
        return new ArtistResource(
            $artist->load(['user', 'skills', 'artworks'])
        );
    }

    /**
     * @OA\Put(
     *     path="/api/artists/{id}",
     *     tags={"Artists"},
     *     summary="Update an existing artist",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Artist ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ArtistCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateArtistRequest $request, Artist $artist)
    {
        $artist = $this->artistService->updateArtist($artist, $request->validated());
        return new ArtistResource($artist);
    }

    /**
     * @OA\Delete(
     *     path="/api/artists/{id}",
     *     tags={"Artists"},
     *     summary="Delete an artist",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Artist ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Artist deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete artist with existing artworks"
     *     ),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function destroy(Artist $artist)
    {
        // Prevent deletion if has related artists
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
