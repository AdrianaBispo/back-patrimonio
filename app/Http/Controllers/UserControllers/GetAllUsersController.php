<?php

namespace App\Http\Controllers\UserControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetAllUsersController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);
       
        if ($perPage > 0) {
            $query = User::query()->orderBy('nome');
            return response()->json($query->paginate($perPage));
        }

        $users = User::query()->orderBy('nome')->get();
        return response()
            ->json(['users' => $users]);
    }
}