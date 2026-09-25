<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class DisableUserController extends Controller
{
    public function disableUser(Request $request, string $id)
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
        
        $user = User::findOrFail($id);
        $isAdmin = $authUser->hasRole('Administrador');
        $isSelf = (string) $authUser->id === (string) $user->id;

        if (! $isAdmin && ! $isSelf) {
            return response()->json(['message' => 'Você não tem permissão para desabilitar este usuário'], 403);
        }

        $user->status = 'inativo';
        $user->save();
        return response()->json(['message' => 'Usuário inativado com sucesso'], 200);

    }
}