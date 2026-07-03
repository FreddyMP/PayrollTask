<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;


Route::middleware(['api.auth'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/tasks', [TaskController::class, 'index']);
    });
});

// Fichajes API Endpoint (recibe datos desde terceros)
Route::post('/fichaje', [App\Http\Controllers\FichajeController::class, 'storeApi']);
