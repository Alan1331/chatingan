<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('users/me', [AuthController::class, 'getProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});