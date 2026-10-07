<?php

namespace App\Http\Controllers\HistoricEquipaments;

use Illuminate\Http\Request;
use App\Models\HistoricEquipaments;
use App\Http\Controllers\Controller;
use App\Models\Equipaments;
use App\Models\Status;
use App\Models\Users;

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
        leftJoin('equipaments', 'historic_equipaments.equipament_id', '=', 'equipaments.id')->
        leftJoin('status', 'historic_equipaments.status_id', '=', 'status.id')->
        leftJoin('users', 'historic_equipaments.user_id', '=', 'users.id')->
        select('historic_equipaments.*', 'equipaments.nome as equipament_nome', 'status.nome as status_nome', 'users.nome as user_nome')->
        orderBy('historic_equipaments.date_time');

        if ($historic_id) {
            $query->where('historic_equipaments.id', $historic_id);
        }
        elseif ($equipament_name) {
            $query->where('equipaments.nome', 'like', '%' . $equipament_name . '%');
        }
        elseif ($equipament_serie) {
            $query->where('equipaments.serie', 'like', '%' . $equipament_serie . '%');
        }
        elseif ($user_name) {
            $query->where('users.nome', 'like', '%' . $user_name . '%');
        }
        elseif ($equipament_name && $equipament_serie) {
            $query->where('equipaments.nome', 'like', '%' . $equipament_name . '%')->where('equipaments.serie', 'like', '%' . $equipament_serie . '%');
        }
       
        elseif ($equipament_name && $equipament_serie && $user_name) {
            $query->where('equipaments.nome', 'like', '%' . $equipament_name . '%')->where('equipaments.serie', 'like', '%' . $equipament_serie . '%')->where('users.nome', 'like', '%' . $user_name . '%');
        }
        else {
            $query->orderBy('historic_equipaments.date_time');
        }
        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }
        $historicEquipaments = $query->get();
        return response()->json(['historic_equipaments' => $historicEquipaments]);
    }
}