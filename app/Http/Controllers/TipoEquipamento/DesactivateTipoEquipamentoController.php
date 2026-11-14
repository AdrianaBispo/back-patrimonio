<?php

namespace App\Http\Controllers\TipoEquipamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoEquipamento;

class DesactivateTipoEquipamentoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:tipo_equipamentos,id',
        ], [
            'id.required' => 'O id é obrigatório',
            'id.exists' => 'O id não existe',
        ]);

        $tipoEquipamento = TipoEquipamento::find($data['id']);
        $tipoEquipamento->ativo = false;
        $tipoEquipamento->save();

        return response()->json($tipoEquipamento);
    }
}
