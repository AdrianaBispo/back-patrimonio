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
        leftJoin('equipaments', 'historic_equipaments.equipament_id', '=', 'equipaments.id')->
        leftJoin('status', 'historic_equipaments.status_id', '=', 'status.id')->
        leftJoin('users', 'historic_equipaments.user_id', '=', 'users.id')->
        select('historic_equipaments.*', 'equipaments.nome as equipament_nome', 'status.nome as status_nome', 'users.nome as user_nome')->
        orderBy('historic_equipaments.date_time');

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }
        $historicEquipaments = $query->get();
        return response()->json(['historic_equipaments' => $historicEquipaments]);
    }
}
