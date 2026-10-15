<?php

namespace App\Http\Controllers\UserControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;

class GetUsersByNameController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);
        $name = (string) $request->query('name');
       
        $query = User::query()
        // ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->leftJoin('departamentos', 'users.departamento_id', '=', 'departamentos.id')
        ->select('users.id', 'users.nome', 'users.email', 'users.celular',  
        DB::raw("
        CASE
            WHEN LENGTH(users.celular) = 11 THEN
                '(' || SUBSTRING(users.celular, 1, 2) || ') ' ||
                SUBSTRING(users.celular, 3, 5) || '-' ||
                SUBSTRING(users.celular, 8, 4)
            WHEN LENGTH(users.celular) = 10 THEN
                '(' || SUBSTRING(users.celular, 1, 2) || ') ' ||
                SUBSTRING(users.celular, 3, 4) || '-' ||
                SUBSTRING(users.celular, 7, 4)
            ELSE users.celular
        END AS celular
    "),

    DB::raw("
        CASE
            WHEN LENGTH(users.cpf) = 11 THEN
                SUBSTRING(users.cpf, 1, 3) || '.' ||
                SUBSTRING(users.cpf, 4, 3) || '.' ||
                SUBSTRING(users.cpf, 7, 3) || '-' ||
                SUBSTRING(users.cpf, 10, 2)
            ELSE users.cpf
        END AS cpf
    "),
    'departamentos.nome as departamento_name',
    )
        ->where('users.nome',  'like', '%' . $name . '%')
        ->orderBy('users.nome');
        if ($perPage > 0) {   
            return response()->json($query->paginate($perPage));
        } else {
            return response()->json($query->get());
        }
    }
}