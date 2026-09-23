<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AuthControllers\LoginControllers;
use App\Http\Controllers\AuthControllers\RegisterControllers;
use App\Http\Controllers\AuthControllers\LogoutControllers;
use App\Http\Controllers\RoleControllers\GetAllRolesControlles;


Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginControllers::class, 'login']);
    Route::post('/register', [RegisterControllers::class, 'register']);# colocar acesso apenas logado
    Route::post('/logout', [LogoutControllers::class, 'logout'])->middleware('jwt.auth');
    Route::get('/user', [AuthController::class, '__invoke']);
});
Route::prefix('roles')->group(function () {
    Route::get('/all', [GetAllRolesControlles::class, '__invoke'])->middleware('role:Administrador');
});