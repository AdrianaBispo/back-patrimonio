<?php

namespace App\Http\Controllers\HistoricEquipaments;

use Illuminate\Http\Request;
use App\Models\HistoricEquipaments;
use App\Http\Controllers\Controller;
use Exception;

class CreateHistoricEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {

        $data = $request->validate([
            'equipament_id' => 'required|exists:equipaments,id',
            'status_id' => 'required|exists:status,id',
            'user_id' => 'required|exists:users,id',
        ], [
            'equipament_id.required' => 'O equipamento é obrigatório',
            'equipament_id.exists' => 'O equipamento informado não existe',
            'status_id.required' => 'O status é obrigatório',
            'status_id.exists' => 'O status informado não existe',
            'user_id.required' => 'O usuário é obrigatório',
            'user_id.exists' => 'O usuário informado não existe',
        ]);

        
        try {
            $historic_equipament = HistoricEquipaments::create([
                'equipament_id' => $data['equipament_id'],
                'status_id' => $data['status_id'],
                'user_id' => $data['user_id'],
            ]);
            $historic_equipament->save();
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar o histórico do equipamento: ' . $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Histórico do equipamento criado com sucesso', 'historic_equipament' => $historic_equipament], 201);
    }
}
