<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\IncidenciasController;
use App\Http\Controllers\Api\ContactController;


// Endpoint público para enviar correos de contacto
Route::post('/contacto', [ContactController::class, 'send']);

Route::middleware(['api.auth'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/incidencias', [IncidenciasController::class, 'index']);
        Route::post('/incidencias', [IncidenciasController::class, 'store']);
        Route::put('/incidencias', [IncidenciasController::class, 'update']);
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/fichaje', [FichajeController::class, 'storeApi']);
    });
});

// Fichajes API Endpoint (recibe datos desde terceros)

