<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterControllers extends Controller
{
    public function register(Request $request)
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

        if (! $authUser->hasRole('Administrador')) {
            return response()->json(['message' => 'Você não tem permissão para registrar usuários'], 403);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'departamento_id' => 'required|uuid|exists:departamentos,id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'nome.required' => 'O nome é obrigatório',
            'nome.string' => 'O nome deve ser uma string',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres',
            'celular.required' => 'O celular é obrigatório',
            'departamento_id.required' => 'O departamento é obrigatório',
            'departamento_id.exists' => 'O departamento informado não existe',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser um email válido',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'email.unique' => 'O email já está em uso',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'roles.required' => 'O perfil de acesso é obrigatório',
            'roles.array' => 'O perfil de acesso deve ser um array',
            'roles.*.exists' => 'O perfil de acesso informado não existe',
        ]);

        try {
            $newUser = User::create([
                'nome' => $data['nome'],
                'celular' => $data['celular'],
                'departamento_id' => $data['departamento_id'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
            $newUser->syncRoles($data['roles']);

            return response()->json([
                'message' => 'Usuário registrado com sucesso',
                'user' => $newUser->load('roles'),
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao registrar usuário: '.$e->getMessage()], 500);
        }
    }
}
