<?php

namespace App\Http\Controllers\AuthControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginControllers extends Controller
{
    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser um email válido',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
        ]);
        try {
            if (! $token = JWTAuth::attempt($data)) {
                return response()->json(['error' => 'Credenciais inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Erro ao gerar token'], 500);
        }
        setcookie('access_token', $token, 60, '/');

        return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
    }
}