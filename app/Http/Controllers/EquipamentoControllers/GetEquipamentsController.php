<?php

namespace App\Http\Controllers\EquipamentoControllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;
use App\Http\Controllers\Controller;


class GetEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {
        $name = $request['name'];
        $serie = $request['serie'];
        $perPage = (int) $request['per_page'] ?? 0;
        if ($name) {
            $equipaments = Equipamento::where('name', 'like', '%' . $name . '%')->get()->paginate($perPage)
                ->leftJoin('status', 'equipamentos.status_id', '=', 'status.id')->leftJoin('users', 'equipamentos.usuario_id', '=', 'users.id')
                ->select('equipamentos.*', 'status.nome as status_nome', 'users.nome as usuario_nome');
        } elseif ($serie) {
            $equipaments = Equipamento::where('serie', 'like', '%' . $serie . '%')->get()->paginate($perPage)
                ->leftJoin('status', 'equipamentos.status_id', '=', 'status.id')->leftJoin('users', 'equipamentos.usuario_id', '=', 'users.id')
                ->select('equipamentos.*', 'status.nome as status_nome', 'users.nome as usuario_nome');
        } elseif ($name && $serie) {
            $equipaments = Equipamento::where('name', 'like', '%' . $name . '%')->where('serie', 'like', '%' . $serie . '%')->get()->paginate($perPage)
                ->leftJoin('status', 'equipamentos.status_id', '=', 'status.id')->leftJoin('users', 'equipamentos.usuario_id', '=', 'users.id')
                ->select('equipamentos.*', 'status.nome as status_nome', 'users.nome as usuario_nome');
        } else {
            $equipaments = Equipamento::all()->paginate($perPage)
                ->leftJoin('status', 'equipamentos.status_id', '=', 'status.id')->leftJoin('users', 'equipamentos.usuario_id', '=', 'users.id')
                ->select('equipamentos.*', 'status.nome as status_nome', 'users.nome as usuario_nome');
        }
        return response()->json($equipaments);
    }
}
