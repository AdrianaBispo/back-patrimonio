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
            'nota' => 'required|string|max:500',
            'nome' => 'required|string|max:70',
            'ativo' => 'required|boolean',
        ], [
            'id.required' => 'O id é obrigatório',
            'id.exists' => 'O id não existe',
            'nota.required' => 'A nota é obrigatória',
            'nota.max' => 'A nota deve ter no máximo 500 caracteres',
            'nome.required' => 'O nome é obrigatório',
            'nome.max' => 'O nome deve ter no máximo 70 caracteres',
        ]);

        $tipoEquipamento = TipoEquipamento::find($data['id']);
        $tipoEquipamento->nota = $data['nota'];
        $tipoEquipamento->nome = $data['nome'];
        $tipoEquipamento->ativo = $data['ativo'];
        $tipoEquipamento->save();

        return response()->json($tipoEquipamento, 200);
    }
}
