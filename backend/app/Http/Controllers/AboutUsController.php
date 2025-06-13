<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAboutUsRequest;
use App\Http\Requests\UpdateAboutUsRequest;
use App\Http\Resources\AboutUsResource;
use App\Models\AboutUs;
use App\Services\AboutUsService;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    public function __construct(
        protected AboutUsService $aboutUsService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AboutUsResource::collection($this->aboutUsService->getAllAboutUs());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAboutUsRequest $request)
    {
        $this->authorize('create', AboutUs::class);

        $aboutUs = $this->aboutUsService->createAboutUs($request->validated());
        return new AboutUsResource($aboutUs);
    }

    /**
     * Display the specified resource.
     */
    public function show(AboutUs $aboutUs)
    {
        return new AboutUsResource($aboutUs);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAboutUsRequest $request, AboutUs $aboutUs)
    {
         $this->authorize('update', $aboutUs);

        $aboutUs = $this->aboutUsService->updateAboutUs($aboutUs, $request->validated());
        return new AboutUsResource($aboutUs);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AboutUs $aboutUs)
    {
        $this->authorize('delete', $aboutUs); 

        // Delete cover image if exists
        if ($aboutUs->cover_image) {
            Storage::disk('public')->delete($aboutUs->cover_image);
        }

        $aboutUs->delete();

        return response()->noContent();
    }
}
