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
//todo: criar politicas de Roles para cada controller
//todo: validar a quatidade de tentativas de login que o usuario pode fazer

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginControllers::class, 'login']);
    Route::post('/register', [RegisterControllers::class, 'register']);
    Route::post('/logout', [LogoutControllers::class, 'logout'])->middleware('jwt.auth');
    Route::get('/user', [AuthController::class, '__invoke']);
    Route::put('/users/{id}', [EditUserController::class, 'editUser']);
    Route::delete('/users/{id}', [DisableUserController::class, 'disableUser']);
}); 
Route::prefix('permissions')->group(function () {
    // Route::get('/all', [Teste::class, 'index']);
    Route::post('/create', [CreatePermissionController::class, 'store'])->middleware('cookie.jwt.auth');
    // Route::put('/update/{id}', [Teste::class, 'update']);
    // Route::delete('/delete/{id}', [Teste::class, 'destroy']);
});
Route::prefix('roles')->group(function () {
    Route::get('/all', [GetAllRolesControlles::class, '__invoke']);
    // ->middleware('role:Administrador');
}); 
Route::prefix('equipaments')->group(function () {
    Route::get('/all', [GetAllEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-equipaments']);
    Route::post('/create', [CreateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-equipaments']);
    Route::put('/update/{id}', [UpdateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:update-equipaments']);
//     Route::delete('/delete/{id}', [DeleteEquipamentsController::class, 'destroy']);
});
