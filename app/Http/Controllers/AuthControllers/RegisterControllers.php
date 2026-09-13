<?php

namespace App\Http\Controllers\AuthControllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterControllers extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.max' => 'O nome deve ter no máximo 255 caracteres',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser um email válido',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'email.unique' => 'O email já está em uso',
            'password.required' => 'A senha é obrigatória',
        ]);

        try {
            $user = User::create($data, [
                'password' => Hash::make($data['password']),
            ]);
            return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao registrar usuário'], 500);
        }
    }
}