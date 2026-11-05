<?php

namespace App\Http\Controllers\CategoriaControllers;

use App\Enums\TipoCategoria;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateCategoriaController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:70',
            'tipo' => 'required|string|max:2',
            'nota' => 'string|max:500|nullable',
        ]);
        $categoria = Categoria::find($request->id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }
        $categoria->update($data);
        return response()->json(['message' => 'Categoria atualizada com sucesso'], 200);
    }
}