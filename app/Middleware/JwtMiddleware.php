<?php

namespace App\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->header('Authorization');
            if (! $token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }
            $token = str_replace('Bearer ', '', $token);
            $request->merge(['token' => $token]);

            return $next($request);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        }
    }
}
