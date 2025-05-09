<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Http\Resources\WorkshopCollection;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use App\Services\WorkshopService;

/**
 * @OA\Tag(
 *     name="Workshops",
 *     description="Operations about workshops"
 * )
 * 
 * @OA\Schema(
 *     schema="Workshop",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="artist_id", type="integer"),
 *     @OA\Property(
 *         property="title",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(property="type", type="string", enum={"permanent", "temporary"}),
 *     @OA\Property(property="start_date", type="string", format="date"),
 *     @OA\Property(property="end_date", type="string", format="date"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="max_students", type="integer"),
 *     @OA\Property(property="cover_image_url", type="string", format="url"),
 *     @OA\Property(property="skills", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="artist", ref="#/components/schemas/Artist")
 * )
 * 
 * @OA\Schema(
 *     schema="WorkshopCreate",
 *     type="object",
 *     required={"title", "type", "price", "max_students", "cover_image"},
 *     @OA\Property(
 *         property="title",
 *         type="object",
 *         required={"en", "es"},
 *         @OA\Property(property="en", type="string", maxLength=255),
 *         @OA\Property(property="es", type="string", maxLength=255)
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="object",
 *         @OA\Property(property="en", type="string"),
 *         @OA\Property(property="es", type="string")
 *     ),
 *     @OA\Property(property="type", type="string", enum={"permanent", "temporary"}),
 *     @OA\Property(property="start_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="end_date", type="string", format="date", nullable=true),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="max_students", type="integer"),
 *     @OA\Property(property="cover_image", type="string", format="binary"),
 *     @OA\Property(property="skills", type="array", @OA\Items(type="integer"))
 * )
 */
class WorkshopController extends Controller
{
    public function __construct(
        protected WorkshopService $workshopService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/workshops",
     *     tags={"Workshops"},
     *     summary="Get list of all workshops",
     *     @OA\Response(
     *         response=200,
     *         description="List of workshops",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Workshop")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return new WorkshopCollection(
            Workshop::with(['artist', 'skills'])->get()
        );    
    }

    /**
     * @OA\Post(
     *     path="/api/workshops",
     *     tags={"Workshops"},
     *     summary="Create a new workshop",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/WorkshopCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Workshop created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Workshop")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Get(
     *     path="/api/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Get workshop details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Workshop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workshop details",
     *         @OA\JsonContent(ref="#/components/schemas/Workshop")
     *     ),
     *     @OA\Response(response=404, description="Workshop not found")
     * )
     */
    public function show(Workshop $workshop)
    {
        return new WorkshopResource($workshop->load(['artist', 'skills']));
    }

    /**
     * @OA\Put(
     *     path="/api/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Update an existing workshop",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Workshop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/WorkshopCreate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workshop updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Workshop")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Workshop not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Delete(
     *     path="/api/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Delete a workshop",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Workshop ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workshop deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Workshop deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Workshop not found")
     * )
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