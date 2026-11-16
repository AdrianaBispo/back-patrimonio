<?php

namespace App\Http\Controllers\HistoricEquipaments;

use Illuminate\Http\Request;
use App\Models\HistoricEquipaments;
use App\Http\Controllers\Controller;

class GetHistoricEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request['per_page'] ?? 0;
        $historic_id = $request['historic'];
        $equipament_name = $request['equipament_name'];
        $equipament_serie = $request['equipament_serie'];
        $user_name = $request['user_name'];

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

        if ($historic_id) {
            $query->where('historic_equipaments.id', $historic_id);
        }
        elseif ($equipament_name) {
            $query->where('equipament.eqi_name', 'like', '%' . $equipament_name . '%');
        }
        elseif ($equipament_serie) {
            $query->where('equipament.eqi_serie', 'like', '%' . $equipament_serie . '%');
        }
        elseif ($user_name) {
            $query->where('users.nome', 'like', '%' . $user_name . '%');
        }

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }
        $historicEquipaments = $query->get();
        return response()->json(['historic_equipaments' => $historicEquipaments]);
    }
}
