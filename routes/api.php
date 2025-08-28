<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/reviews', ReviewController::class)->missing(function (Request $request) {
    return response()->json(['message' => 'Review not found.'], 404);
});
Route::apiResource('/destinations', DestinationController::class)->missing(function (Request $request) {
    return response()->json(['message' => 'Destination not found.'], 404);
});

Route::get('/reviews-home', [ReviewController::class, 'home'])->name('home');
