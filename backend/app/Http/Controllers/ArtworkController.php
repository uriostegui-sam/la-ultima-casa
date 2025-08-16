<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtworkRequest;
use App\Http\Requests\UpdateArtworkRequest;
use App\Http\Resources\ArtworkResource;
use App\Models\Artwork;
use App\Models\ArtworkImage;
use App\Services\ArtworkService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Artworks",
 *     description="Operations about artworks"
 * )
 * 
 * @OA\Schema(
 *     schema="Artwork",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(
 *         property="description",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="dimensions",
 *         type="object",
 *         @OA\Property(property="width", type="number"),
 *         @OA\Property(property="height", type="number"),
 *         @OA\Property(property="depth", type="number")
 *     ),
 *     @OA\Property(property="creation_date", type="string", format="date"),
 *     @OA\Property(
 *         property="artist",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ArtworkImage")
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ArtworkImage",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="is_primary", type="boolean"),
 *     @OA\Property(property="order", type="integer"),
 *     @OA\Property(property="url", type="string")
 * )
 * 
 * @OA\Schema(
 *     schema="ArtworkCreate",
 *     type="object",
 *     required={"title", "images", "creation_date"},
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(
 *         property="description",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(type="string", format="binary")
 *     ),
 *     @OA\Property(
 *         property="dimensions",
 *         type="object",
 *         @OA\Property(property="width", type="number"),
 *         @OA\Property(property="height", type="number"),
 *         @OA\Property(property="depth", type="number")
 *     ),
 *     @OA\Property(property="creation_date", type="string", format="date")
 * )
 */

class ArtworkController extends Controller
{
    public function __construct(
        protected ArtworkService $artworkService
    ) {}


    /**
     * @OA\Get(
     *     path="/api/artworks",
     *     tags={"Artworks"},
     *     summary="List all artworks",
     *     description="Returns paginated list of artworks",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Artwork")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string"),
     *                 @OA\Property(property="next", type="string")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $artwork = $this->artworkService->getPaginatedArtworks();
        return ArtworkResource::collection($artwork);
    }

    /**
     * @OA\Post(
     *     path="/api/artworks",
     *     tags={"Artworks"},
     *     summary="Create a new artwork",
     *     description="Create a new artwork with images",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ArtworkCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Artwork created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Artwork")
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
    public function store(StoreArtworkRequest $request)
    {
        $this->authorize('create', Artwork::class);

        $artwork = Artwork::create($request->except('images'));

        if ($request->hasFile('images')) {
            $this->artworkService->storeImages(
                $artwork,
                $request->file('images')
            );
        }

        return new ArtworkResource($artwork->load('images', 'artist.user'));
    }

    /**
     * @OA\Get(
     *     path="/api/artworks/{id}",
     *     tags={"Artworks"},
     *     summary="Get artwork details",
     *     description="Returns detailed information about a specific artwork",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Artwork ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Artwork")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artwork not found"
     *     )
     * )
     */
    public function show(Artwork $artwork)
    {
        return new ArtworkResource($artwork);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtworkRequest $request, Artwork $artwork)
    {
        $this->authorize('update', $artwork);

        // Update the artwork fields (except images)
        $artwork->update($request->except(['images', 'images_to_delete']));

        if ($request->has('images_to_delete')) {
            $imageIds = json_decode($request->input('images_to_delete'), true);
            ArtworkImage::whereIn('id', $imageIds)->delete();
        }

        // Handle new images if provided
        if ($request->hasFile('images')) {
            $lastImageNumber = $artwork->images->max('order') + 1;

            foreach ($request->file('images') as $index => $image) {
                $path = $this->artworkService->storeImage(
                    $image,
                    $artwork,
                    $lastImageNumber + $index
                );

                ArtworkImage::create([
                    'artwork_id' => $artwork->id,
                    'path' => $path,
                    'is_primary' => false,
                    'order' => $lastImageNumber + $index
                ]);
            }
        }

        return new ArtworkResource($artwork->load('images', 'artist.user'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        $this->authorize('delete', $artwork);

        foreach ($artwork->images as $image) {
            $this->artworkService->deleteImage($image);
        }

        $artwork->delete();

        return response()->noContent();
    }

    public function setPrimaryImage(Artwork $artwork, ArtworkImage $image)
    {
        $this->authorize('update', $artwork);

        // Remove primary status from all other images
        $artwork->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return new ArtworkResource($artwork->load('images', 'artist.user'));
    }

    public function reorderArtwork(Request $request)
    {
        $artworks = $request->get('artworks', []);

        foreach ($artworks as $artwork) {
            Artwork::where('id', $artwork['id'])
                ->update(['order' => $artwork['order']]);
        }

        return response()->json([
            'message' => 'orderUpdatedSuccessfully',
        ]);
    }

    public function deleteImage(Artwork $artwork, ArtworkImage $image)
    {
        $this->authorize('update', $artwork);

        $this->artworkService->deleteImage($image);

        // If we deleted the primary image, set a new one
        if ($image->is_primary && $artwork->images()->count() > 0) {
            $artwork->images()->first()->update(['is_primary' => true]);
        }

        return response()->noContent();
    }
}
