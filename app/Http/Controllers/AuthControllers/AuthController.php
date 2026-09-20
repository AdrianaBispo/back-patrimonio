<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = $request->cookie('access_token');
        
        if (! $token) {
            return response()->json(['message' => 'O cookie não chegou no backend'], 401);
        } 
        
        try {
            $user = JWTAuth::setToken($token)->authenticate();
            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token chegou, mas é inválido: ' . $e->getMessage()], 401);
        }
    }
}
