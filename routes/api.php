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
    Route::get('/user', [AuthController::class, 'getUser']);

    // Task Routes
    Route::get('/tasks', [TaskController::class, 'index']);             // Get all tasks for user
    Route::get('/tasks/{id}', [TaskController::class, 'show']);         // Get single task by ID
    Route::post('/tasks', [TaskController::class, 'store']);            // Create new task
    Route::put('/tasks/{task}', [TaskController::class, 'update']);     // Update a task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);   // Delete a task by ID
});


//  For Testing Only:
if (app()->environment('testing')) {
    Route::middleware('auth:sanctum')->get('/test-protected', function (Request $request) {
        return response()->json($request->user());
    });
}
