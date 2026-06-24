<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Models\Album;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* Album routes */

Route::apiResource('album', AlbumController::class)->middleware('sanctum'); //this is a shorthand
//instead of this
// Route::post('/album',[AlbumController::class, '/store'] )->middleware('auth:sanctum');
// Route::get('/albums', [AlbumController::class, 'index']);
// Route::get('/albums/{album}', [AlbumController::class, 'show']);
// Route::patch('/albums/{album}', [AlbumController::class, 'update']);
// Route::delete('/albums/{album}', [AlbumController::class, 'destroy']);