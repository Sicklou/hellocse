<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//*********
// Public endpoints
//*********

Route::get('/ping', function () {
    return "Hello HelloCSE";
});

Route::post('/token/create', LoginController::class)
    ->name('sanctum.createToken');

// Profil : Consultation
Route::get('/profils', [ProfilController::class, 'index']);
Route::get('/profils/{profil}', [ProfilController::class, 'show']);

//*********
// Private endpoints
//*********

// Profil : Creation
Route::post('/profils', [ProfilController::class, 'store'])->middleware('auth:sanctum');
// Profil : Suppression
Route::delete('/profils/{profil}', [ProfilController::class, 'destroy'])->middleware('auth:sanctum');
