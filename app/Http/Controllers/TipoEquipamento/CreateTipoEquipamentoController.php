<?php

namespace App\Http\Controllers\TipoEquipamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoEquipamento;

class CreateTipoEquipamentoController extends Controller
{

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:70',
            'name' => 'required|string|max:70',
            'ativo' => 'required|boolean',
        ], [
            'descricao.required' => 'O descricao é obrigatório',
            'descricao.string' => 'O descricao deve ser uma string',
            'descricao.max' => 'O descricao deve ter no máximo 150 caracteres',
            'name.required' => 'O name é obrigatório',
            'name.string' => 'O name deve ser uma string',
            'name.max' => 'O name deve ter no máximo 70 caracteres',
            'ativo.required' => 'O ativo é obrigatório',
            'ativo.boolean' => 'O ativo deve ser um booleano',
        ]);

        try {
            $tipoEquipamento = TipoEquipamento::create($data);
            return response()->json($tipoEquipamento);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
