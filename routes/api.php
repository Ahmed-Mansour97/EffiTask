<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware([JwtMiddleware::class])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('', [TaskController::class, 'index']);
        Route::post('', [TaskController::class, 'create']);
        Route::put('/{task}/change-status', [TaskController::class, 'changeStatus']);
        Route::get('/{task}', [TaskController::class, 'show']);
        Route::put('/{task}', [TaskController::class, 'update']);
        Route::delete('/{task}', [TaskController::class, 'delete']);
    });
});
