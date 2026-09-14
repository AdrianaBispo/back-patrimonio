<?php

namespace App\Http\Controllers\AuthControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $token = $request->cookie('access_token');
        return response()->json(compact('user', 'token'));
    } 
}