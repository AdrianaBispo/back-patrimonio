<?php

namespace App\Http\Controllers\CategoriaControllers;

use App\Enums\TipoCategoria;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetAllCategoriaController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);

        $categorias = Categoria::query()
        ->select('id', 'nome', 'tipo', 'nota',
        DB::raw("
            CASE
                WHEN tipo = 'AC' THEN 'Acessório'
                WHEN tipo = 'AT' THEN 'Ativo'
                WHEN tipo = 'CO' THEN 'Consumível'
                WHEN tipo = 'LI' THEN 'Licença'
                WHEN tipo = 'CP' THEN 'Componente'
            END as tipo_nome
        "))
        
        ->orderBy('created_at', 'desc')
        ->when($perPage > 0, function ($query) use ($perPage) {
            return $query->paginate($perPage);
        })
        ->orWhere($request->has('search'), function ($query) use ($request) {
            return $query->where('nome', 'like', '%' . $request->search . '%');
        })
        ->orWhere('id', $request->id); 
        return response()->json([
            'data' => $categorias,
            'per_page' => $perPage,
            'total' => $categorias->total(),
            'current_page' => $categorias->currentPage(),
            'last_page' => $categorias->lastPage(),
            'from' => $categorias->firstItem(),
            'to' => $categorias->lastItem(),
        ]);
    }
}