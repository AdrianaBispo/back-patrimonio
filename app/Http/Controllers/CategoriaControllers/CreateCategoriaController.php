<?php

namespace App\Http\Controllers\CategoriaControllers;

use App\Enums\TipoCategoria;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class CreateCategoriaController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:70',
            'tipo' => ['required', Rule::enum(TipoCategoria::class)],
            'nota' => 'nullable|string|max:500',
        ], [
            'nome.required' => 'O nome é obrigatório',
            'nome.string' => 'O nome deve ser uma string',
            'nome.max' => 'O nome deve ter no máximo 70 caracteres',
            'nota.string' => 'A nota deve ser uma string',
            'nota.max' => 'A nota deve ter no máximo 500 caracteres',

        ]);

        try {
            $categoria = Categoria::create(

                [
                    'nome' => $data['nome'],
                    'nota' => $data['nota'] ?? null,
                    'tipo' => $data['tipo']
                ]
            );

            return response()->json(['message' => 'Categoria criado com sucesso', 'categoria' => $categoria], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao criar categoria: ' . $e->getMessage()], 500);
        }
    }
}
