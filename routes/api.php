<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('users/me', [AuthController::class, 'getProfile']);
    Route::put('users/me', [AuthController::class, 'updateProfile']);

    Route::get('messages/{contact}', [MessageController::class, 'getMessages']);
    Route::post('messages', [MessageController::class, 'sendMessage']);
    Route::put('messages/{message}', [MessageController::class, 'updateMessage']);
    Route::delete('messages/{message}', [MessageController::class, 'deleteMessage']);
});