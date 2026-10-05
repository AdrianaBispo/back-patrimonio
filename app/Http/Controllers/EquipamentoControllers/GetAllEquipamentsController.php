<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;
use App\Http\Controllers\Controller;

class GetAllEquipamentsController extends Controller
{
    public function __invoke(Request $request)
        {

        $perPage = (int) $request->query('per_page', 0);
    
        $query = Equipamento::query()->
        leftJoin('status', 'equipamentos.status_id', '=', 'status.id')->
        leftJoin('users', 'equipamentos.usuario_id', '=', 'users.id')
        ->select('equipamentos.*', 'status.nome as status_nome', 'users.nome as usuario_nome')
        ->orderBy('equipamentos.nome');

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        $equipamentos = $query->get();
        return response()
            ->json(['equipamentos' => $equipamentos]);
    }
}
