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
        ]);
      
        try {
            if (! $token = JWTAuth::attempt($data)) {
                return response()->json(['error' => 'Credenciais inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Erro ao gerar token'], 500);
        }

        return response()
            ->json(['access_token' => $token, 'token_type' => 'bearer'])
            ->cookie('access_token', $token, 60, '/', null, false, true); 
    }
}