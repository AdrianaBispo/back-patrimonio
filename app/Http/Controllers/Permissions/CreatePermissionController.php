<?php

namespace App\Http\Controllers\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreatePermissionController extends Controller
{
    public function store(Request $request)
    {
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

        if ($authUser->hasRole('users')) {
            return response()->json(['message' => 'Você não tem permissão para registrar permissões'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        $permission = Permission::create($validated);

        return response()->json([
            'message' => 'Permissão criada com sucesso',
            'permission' => $permission,
        ], 201);
    }
}
