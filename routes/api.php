<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//*********
// Public endpoints
//*********

Route::get('/ping', function () {
    return "Hello HelloCSE";
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
