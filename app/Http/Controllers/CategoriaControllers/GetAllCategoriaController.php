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
    
        $query = Categoria::query()
            ->select(
                'id',
                'nome',
                'tipo',
                'nota',
                DB::raw("
                    CASE
                        WHEN tipo = 'AC' THEN 'Acessório'
                        WHEN tipo = 'AT' THEN 'Ativo'
                        WHEN tipo = 'CO' THEN 'Consumível'
                        WHEN tipo = 'LI' THEN 'Licença'
                        WHEN tipo = 'CP' THEN 'Componente'
                    END as tipo_nome
                ")
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nome', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('id'), function ($query) use ($request) {
                $query->where('id', $request->id);
            })
            ->orderBy('created_at', 'desc');
    
        if ($perPage > 0) {
            $categorias = $query->paginate($perPage);
        } else {
            $categorias = $query->get();
        }
    
        return response()->json([
            'data' => $categorias instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $categorias->items()
                : $categorias,
            'per_page' => $perPage,
            'total' => $categorias instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $categorias->total()
                : $categorias->count(),
            'current_page' => $perPage > 0 ? $categorias->currentPage() : 1,
            'last_page' => $perPage > 0 ? $categorias->lastPage() : 1,
        ]);
    }
}