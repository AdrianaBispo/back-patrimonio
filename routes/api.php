<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AuthControllers\LoginControllers;
use App\Http\Controllers\AuthControllers\RegisterControllers;
use App\Http\Controllers\AuthControllers\LogoutControllers;;
use App\Http\Controllers\UserControllers\EditUserController;
use App\Http\Controllers\RoleControllers\GetAllRolesControlles;
use App\Http\Controllers\UserControllers\DisableUserController;
use App\Http\Controllers\Permissions\CreatePermissionController;

#EquipamentoControllers
use App\Http\Controllers\EquipamentoControllers\CreateEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\GetAllEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\UpdateEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\GetEquipamentsController;
#HistoricEquipamentsControllers
use App\Http\Controllers\HistoricEquipaments\GetAllHistoricEquipamentsController;
use App\Http\Controllers\HistoricEquipaments\GetHistoricEquipamentsController;
use App\Http\Controllers\HistoricEquipaments\CreateHistoricEquipamentsController;
//todo: criar politicas de Roles para cada controller
//todo: validar a quatidade de tentativas de login que o usuario pode fazer

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginControllers::class, 'login'])->middleware('throttle:5,1');
    Route::post('/register', [RegisterControllers::class, 'register'])->middleware('cookie.jwt.auth', 'throttle:5,1');
    Route::post('/logout', [LogoutControllers::class, 'logout'])->middleware('cookie.jwt.auth');
    Route::get('/user', [AuthController::class, '__invoke'])->middleware('cookie.jwt.auth');
    Route::put('/users/{id}', [EditUserController::class, 'editUser'])->middleware('cookie.jwt.auth');
    Route::delete('/users/{id}', [DisableUserController::class, 'disableUser'])->middleware('cookie.jwt.auth');
}); 
Route::prefix('permissions')->group(function () {
    // Route::get('/all', [Teste::class, 'index']);
    Route::post('/create', [CreatePermissionController::class, 'store'])->middleware('cookie.jwt.auth');
    // Route::put('/update/{id}', [Teste::class, 'update']);
    // Route::delete('/delete/{id}', [Teste::class, 'destroy']);
});
Route::prefix('roles')->group(function () {
    Route::get('/all', [GetAllRolesControlles::class, '__invoke'])->middleware('cookie.jwt.auth');
    // ->middleware('role:Administrador');
}); 
Route::prefix('equipaments')->group(function () {
    Route::get('/all', [GetAllEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-equipaments']);
    Route::get('/get', [GetEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-equipaments']);
    Route::post('/create', [CreateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-equipaments']);
    Route::put('/update/{id}', [UpdateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:update-equipaments']);
});
Route::prefix('historic_equipaments')->group(function () {
    Route::get('/all', [GetAllHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-historic-equipaments']);
    Route::get('/get', [GetHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-historic-equipaments']);
    Route::post('/create', [CreateHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-historic-equipaments']);
});
