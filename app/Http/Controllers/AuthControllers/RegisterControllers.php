<?php

namespace App\Http\Controllers\AuthControllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;


class RegisterControllers extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'departamento_id' => 'required|uuid|exists:departamentos,id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array|exists:roles,id',
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
            'roles.exists' => 'O perfil de acesso informado não existe',
        ]);
        $token = $request->cookie('access_token');
        
        if (! $token) {
            return response()->json(['message' => 'O cookie não chegou no backend'], 401);
        } 

        try {
            $user = JWTAuth::setToken($token)->authenticate();
            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Você não tem permissão para registrar usuários'], 403);
            }
            $newUser = User::create($data, [
                'password' => Hash::make($data['password']),
            ]);
            return response()->json(['message' => 'Usuário registrado com sucesso', 'user' => $newUser], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
        }
    }
}