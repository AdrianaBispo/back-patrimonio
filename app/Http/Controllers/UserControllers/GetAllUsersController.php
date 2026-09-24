<?php

namespace App\Http\Controllers\UserControllers\GetAllUsersControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetAllUsersController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);
        try {
            $token = $request->cookie('access_token');
            if (! $token) {
                return response()->json(['message' => 'O cookie não chegou no backend'], 401);
            }

            $authUser = JWTAuth::setToken($token)->authenticate();
            if (! $authUser) {
                return response()->json(['message' => 'Token inválido'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
        $isAdmin = $authUser->hasRole('Administrador');

        if (! $isAdmin) {
            return response()->json(['message' => 'Você não tem permissão para editar este usuário'], 403);
        }

        if ($perPage > 0) {
            $query = User::query()->orderBy('nome');
            return response()->json($query->paginate($perPage));
        }

        $users = User::query()->orderBy('nome')->get();
        return response()
            ->json(['users' => $users]);
    }
}