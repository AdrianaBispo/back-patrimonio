<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AuthControllers\LoginControllers;
use App\Http\Controllers\AuthControllers\RegisterControllers;
use App\Http\Controllers\AuthControllers\LogoutControllers;

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginControllers::class, 'login']);
    Route::post('/register', [RegisterControllers::class, 'register']);
    Route::post('/logout', [LogoutControllers::class, 'logout'])->middleware('jwt.auth');
    Route::get('/user', [AuthController::class, '__invoke']);
});