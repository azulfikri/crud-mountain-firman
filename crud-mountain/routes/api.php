<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MountainController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('mountains', MountainController::class);
    // ->only(['index', 'store', 'show', 'update', 'destroy']);
