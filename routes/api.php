<?php

use App\Http\Controllers\LoginController;
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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
