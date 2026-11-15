<?php

namespace App\Http\Controllers\TipoEquipamento;

use App\Http\Controllers\Controller;
use App\Models\TipoEquipamento;
use Illuminate\Http\Request;

class DesactivateTipoEquipamentoController extends Controller
{
    public function __invoke(Request $request, string $id)
    {
        $tipoEquipamento = TipoEquipamento::find($id);

        if (! $tipoEquipamento) {
            return response()->json(['message' => 'Tipo de equipamento não encontrado'], 404);
        }

        $tipoEquipamento->ativo = false;
        $tipoEquipamento->save();

        return response()->json($tipoEquipamento, 200);
    }
}
