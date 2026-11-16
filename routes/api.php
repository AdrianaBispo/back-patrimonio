<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers\AuthController;
use App\Http\Controllers\AuthControllers\LoginControllers;
use App\Http\Controllers\AuthControllers\RegisterControllers;
use App\Http\Controllers\AuthControllers\LogoutControllers;;
use App\Http\Controllers\UserControllers\EditUserController;
use App\Http\Controllers\RoleControllers\GetAllRolesControlles;
use App\Http\Controllers\UserControllers\DisableUserController;
use App\Http\Controllers\UserControllers\GetAllUsersController;
use App\Http\Controllers\UserControllers\GetUsersController;
#RoleControllers
use App\Http\Controllers\RoleControllers\UpdateRolePermissionsController;
use App\Http\Controllers\Permissions\CreatePermissionController;

#EquipamentoControllers
use App\Http\Controllers\EquipamentoControllers\CreateEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\GetAllEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\UpdateEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\GetEquipamentsController;
use App\Http\Controllers\EquipamentoControllers\EquipamentoImageUploadController;
#HistoricEquipamentsControllers
use App\Http\Controllers\HistoricEquipaments\GetAllHistoricEquipamentsController;
use App\Http\Controllers\HistoricEquipaments\GetHistoricEquipamentsController;
use App\Http\Controllers\HistoricEquipaments\CreateHistoricEquipamentsController;
#Categoria
use App\Http\Controllers\CategoriaControllers\CreateCategoriaController;
use App\Http\Controllers\CategoriaControllers\GetAllCategoriaController;
use App\Http\Controllers\CategoriaControllers\UpdateCategoriaController;
#TipoEquipamento
use App\Http\Controllers\TipoEquipamento\ActivateTipoEquipamentoController;
use App\Http\Controllers\TipoEquipamento\CreateTipoEquipamentoController;
use App\Http\Controllers\TipoEquipamento\DesactivateTipoEquipamentoController;
use App\Http\Controllers\TipoEquipamento\GetTipoEquipamentoController;
use App\Http\Controllers\TipoEquipamento\UpdateTipoEquipamentoController;
#Status
use App\Http\Controllers\StatusControllers\GetAllStatusController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginControllers::class, 'login'])->middleware('throttle:5,1');
    Route::post('/register', [RegisterControllers::class, 'register'])->middleware('cookie.jwt.auth', 'throttle:5,1');
    Route::post('/logout', [LogoutControllers::class, 'logout'])->middleware('cookie.jwt.auth');
    Route::get('/user', [AuthController::class, '__invoke'])->middleware('cookie.jwt.auth');
    Route::put('/users/{id}', [EditUserController::class, 'editUser'])->middleware('cookie.jwt.auth');
    Route::delete('/users/{id}', [DisableUserController::class, 'disableUser'])->middleware('cookie.jwt.auth');
    Route::get('/users', [GetUsersController::class, '__invoke']);
    // ->middleware('cookie.jwt.auth');
}); 
Route::prefix('permissions')->group(function () {
    Route::post('/create', [CreatePermissionController::class, 'store'])->middleware('cookie.jwt.auth');
});
Route::prefix('roles')->group(function () {
    Route::get('/all', [GetAllRolesControlles::class, '__invoke'])->middleware('cookie.jwt.auth');
    Route::put('/update/{id}', [UpdateRolePermissionsController::class, '__invoke'])->middleware('cookie.jwt.auth');
});
Route::prefix('equipaments')->group(function () {
    Route::get('/all', [GetAllEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-equipaments']);
    Route::get('/get', [GetEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-equipaments']);
    Route::post('/create', [CreateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-equipaments']);
    Route::put('/update/{id}', [UpdateEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:update-equipaments']);
    Route::post('/image-upload', [EquipamentoImageUploadController::class, 'upload'])->middleware(['cookie.jwt.auth','permission:create-equipaments', 'permission:update-equipaments']);
});
Route::prefix('historic_equipaments')->group(function () {
    Route::get('/all', [GetAllHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-historic-equipaments']);
    Route::get('/get', [GetHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-historic-equipaments']);
    Route::post('/create', [CreateHistoricEquipamentsController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-historic-equipaments']);
});

Route::prefix('categoria')->group(function (){
    Route::post('/create', [CreateCategoriaController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-categoria']);
    Route::get('/all', [GetAllCategoriaController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-categoria']);
    Route::put('/update/{id}', [UpdateCategoriaController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:update-categoria']);
});

route::prefix('tipo_equipamento')->group(function (){
    Route::post('/create', [CreateTipoEquipamentoController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:create-tipo-equipamento']);
    Route::get('/all', [GetTipoEquipamentoController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-tipo-equipamento']);
    Route::put('/update/{id}', [UpdateTipoEquipamentoController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:update-tipo-equipamento']);
    Route::put('/desactivate/{id}', [DesactivateTipoEquipamentoController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:desactivate-tipo-equipamento']);
    Route::put('/activate/{id}', [ActivateTipoEquipamentoController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:activate-tipo-equipamento']);
});

Route::prefix('status')->group(function () {
    Route::get('/all', [GetAllStatusController::class, '__invoke'])->middleware(['cookie.jwt.auth', 'permission:get-all-status']);
});
