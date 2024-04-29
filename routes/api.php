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

Route::get('/profils', [ProfilController::class, 'index']);

//*********
// Private endpoints
//*********

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
