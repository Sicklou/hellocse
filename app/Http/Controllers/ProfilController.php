<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use App\Http\Requests\StoreProfilRequest;
use App\Http\Requests\UpdateProfilRequest;
use App\Http\Resources\ProfilResource;
use Illuminate\Http\JsonResponse;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $profils = ProfilResource::collection(Profil::active()->get());
        return response()->json($profils);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfilRequest $request): JsonResponse
    {
        if (auth()->user()->isNotAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        // Enregistrement de l'image dans storage/app/images/profils
        $path = $request->image->store('images/profils');
        $validated['image'] = $path;

        $profil = Profil::create($validated);
        $profilResource = new ProfilResource($profil);

        return response()->json([
                'message' => 'created',
                'data' => $profilResource
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profil $profil): JsonResponse
    {
        $profil = new ProfilResource($profil);
        return response()->json($profil);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfilRequest $request, Profil $profil): JsonResponse
    {
        if (auth()->user()->isNotAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        // Enregistrement de l'image dans storage/app/images/profils
        $path = $request->image->store('images/profils');
        $validated['image'] = $path;

        $profil = Profil::create($validated);
        $profilResource = new ProfilResource($profil);

        return response()->json(['message' => 'updated', 'data' => $profilResource]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profil $profil): JsonResponse
    {
        if (auth()->user()->isNotAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $profil->delete();
        return response()->json(['message' => 'deleted']);
    }
}
