<?php

namespace App\Http\Controllers\HistoricEquipaments;

use Illuminate\Http\Request;
use App\Models\HistoricEquipaments; 
use App\Http\Controllers\Controller;



class GetAllHistoricEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request['per_page'] ?? 0;

        $query = HistoricEquipaments::query()->
        leftJoin('equipament', 'historic_equipaments.equipament_id', '=', 'equipament.eqi_id')->
        leftJoin('status', 'historic_equipaments.status_id', '=', 'status.id')->
        leftJoin('users', 'historic_equipaments.user_id', '=', 'users.id')->
        select(
            'historic_equipaments.*',
            'historic_equipaments.created_at as date_time',
            'equipament.eqi_name as equipament_nome',
            'equipament.eqi_serie as equipament_serie',
            'status.nome as status_nome',
            'users.nome as user_nome',
        )->
        orderBy('historic_equipaments.created_at');

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }
        $historicEquipaments = $query->get();
        return response()->json(['historic_equipaments' => $historicEquipaments]);
    }
}
