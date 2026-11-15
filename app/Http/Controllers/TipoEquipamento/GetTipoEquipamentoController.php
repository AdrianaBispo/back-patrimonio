<?php

namespace App\Http\Controllers\TipoEquipamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoEquipamento;

class GetTipoEquipamentoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);
        $tipoEquipamentos = TipoEquipamento::query()
            ->select('id', 'nota', 'nome', 'ativo')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        return response()->json([
            'data' => $tipoEquipamentos,
            'total' => $tipoEquipamentos->total(),
            'page' => $tipoEquipamentos->currentPage(),
            'per_page' => $tipoEquipamentos->perPage(),
            'last_page' => $tipoEquipamentos->lastPage(),
            'from' => $tipoEquipamentos->firstItem(),
            'to' => $tipoEquipamentos->lastItem(),
        ]);
    }
}
