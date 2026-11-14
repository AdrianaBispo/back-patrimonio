<?php

namespace App\Http\Controllers\TipoEquipamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoEquipamento;

class UpdateTipoEquipamentoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:tipo_equipamentos,id',
            'descricao' => 'required|string|max:70',
            'name' => 'required|string|max:70',
            'ativo' => 'required|boolean',
        ], [
            'id.required' => 'O id é obrigatório',
            'id.exists' => 'O id não existe',
        ]);

        $tipoEquipamento = TipoEquipamento::find($data['id']);
        $tipoEquipamento->descricao = $data['descricao'];
        $tipoEquipamento->name = $data['name'];
        $tipoEquipamento->ativo = $data['ativo'];
        $tipoEquipamento->save();

        return response()->json($tipoEquipamento, 200);
    }
}
