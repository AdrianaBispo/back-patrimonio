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
            'nota' => 'required|string|max:500',
            'nome' => 'required|string|max:70',
            'ativo' => 'required|boolean',
        ], [
            'nota.required' => 'A nota é obrigatória',
            'nota.string' => 'A nota deve ser uma string',
            'nota.max' => 'A nota deve ter no máximo 500 caracteres',
            'nome.required' => 'O nome é obrigatório',
            'nome.string' => 'O nome deve ser uma string',
            'nome.max' => 'O nome deve ter no máximo 70 caracteres',
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
