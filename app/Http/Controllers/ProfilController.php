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
    public function store(StoreProfilRequest $request)
    {
        $validated = $request->validated();
        $path = $request->image->store('images/profils');
        $validated['image'] = $path;
        $profil = Profil::create($validated);

        return response()->json($profil);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profil $profil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfilRequest $request, Profil $profil)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profil $profil)
    {
        //
    }
}
