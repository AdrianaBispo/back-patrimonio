<?php

namespace App\Http\Controllers\CategoriaControllers;

use App\Enums\TipoCategoria;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetAllCategoriaController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->query('per_page', 0);
        $id = is_string($request->query('id')) ? trim($request->query('id')) : null;
        $search = is_string($request->query('search')) ? trim($request->query('search')) : null;

        if ($id === '' || strcasecmp((string) $id, 'undefined') === 0 || strcasecmp((string) $id, 'null') === 0) {
            $id = null;
        }

        if ($search === '' || strcasecmp((string) $search, 'undefined') === 0 || strcasecmp((string) $search, 'null') === 0) {
            $search = null;
        }
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
            ->when($search !== null, function ($query) use ($search) {
                $query->where('nome', 'like', '%' . $search . '%');
            })
            ->when($id !== null, function ($query) use ($id) {
                $query->where('id', $id);
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