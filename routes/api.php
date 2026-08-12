<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\IncidenciasController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SolicitudesController;
use App\Http\Controllers\Api\CalendarController;

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
        Route::get('/task/{id}', [TaskController::class, 'show']);
        Route::put('/tasks', [TaskController::class, 'updateStatus']);
        Route::post('/fichaje', [FichajeController::class, 'storeApi']);
        Route::get('/solicitudes', [SolicitudesController::class, 'index']);
        Route::post('/solicitudes', [SolicitudesController::class, 'store']);

        // Calendar endpoints
        Route::get('/calendar', [CalendarController::class, 'index']);
        Route::post('/calendar', [CalendarController::class, 'store']);
        Route::put('/calendar/{id}', [CalendarController::class, 'update']);
        Route::delete('/calendar/{id}', [CalendarController::class, 'destroy']);
    });
});

// Fichajes API Endpoint (recibe datos desde terceros)

