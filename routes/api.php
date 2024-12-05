<?php

use App\Http\Controllers\ApiAdminCuisineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auditLog'])->group(function () {
    Route::middleware('apiAdmin')->group(function () {
        Route::apiResource('cuisines', ApiAdminCuisineController::class);
    });
});