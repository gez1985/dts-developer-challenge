<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Task Routes
    Route::get('/tasks', [TaskController::class, 'index']);             // Get all tasks for user
    Route::get('/tasks/{id}', [TaskController::class, 'show']);         // Get single task by ID
    Route::post('/tasks', [TaskController::class, 'store']);            // Create new task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);   // Delete a task by ID
});
