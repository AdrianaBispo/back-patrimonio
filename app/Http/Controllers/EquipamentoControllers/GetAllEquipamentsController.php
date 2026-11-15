<?php

namespace  App\Http\Controllers\EquipamentoControllers;
use Illuminate\Http\Request;
use App\Models\Equipamento;
use App\Http\Controllers\Controller;

class GetAllEquipamentsController extends Controller
{
    public function __invoke(Request $request)
        {

        $perPage = (int) $request->query('per_page', 0);
    
        $query = Equipamento::query()
        ->leftJoin('status', 'equipament.eqi_status_id', '=', 'status.sta_id')
        ->leftJoin('users', 'equipament.usuario_id', '=', 'users.id')
        ->leftJoin('categorias', 'equipament.categoria_id', '=', 'categorias.id')
        ->leftJoin('tipo_equipamentos', 'equipament.tipo_equipamento_id', '=', 'tipo_equipamentos.id')
        ->select(
            'equipament.*',
            'status.sta_name as status_nome',
            'users.nome as usuario_nome',
            'categorias.nome as categoria_nome',
            'tipo_equipamentos.nome as tipo_equipamento_nome',
        )
        ->orderBy('equipament.eqi_name');

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        $equipamentos = $query->get();
        return response()
            ->json(['equipamentos' => $equipamentos]);
    }
}
