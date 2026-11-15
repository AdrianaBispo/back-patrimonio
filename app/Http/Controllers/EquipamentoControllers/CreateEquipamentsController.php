<?php

namespace App\Http\Controllers\EquipamentoControllers;

use App\Http\Controllers\Controller;
use App\Models\Equipamento;
use App\Models\Status;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'serie' => 'required|string|max:255|regex:/^[0-9]+$/',
            'status_id' => 'required|integer|exists:status,id',
            'imagem_url' => 'required|string|max:255',
            'usuario_id' => 'required|uuid|exists:users,id',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'tipo_equipamento_id' => 'required|integer|exists:tipo_equipamentos,id',
        ], [
            'nome.required' => 'O nome é obrigatório',
            'nome.string' => 'O nome deve ser uma string',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres',
            'descricao.required' => 'A descricao é obrigatória',
            'descricao.string' => 'A descricao deve ser uma string',
            'descricao.max' => 'A descricao deve ter no máximo 255 caracteres',
            'serie.required' => 'A serie é obrigatória',
            'serie.string' => 'A serie deve ser uma string',
            'serie.regex' => 'O número de série deve conter apenas números.',
            'serie.unique' => 'A serie já existe',
            'serie.max' => 'A serie deve ter no máximo 255 caracteres',
            'status_id.required' => 'O status é obrigatório',
            'status_id.uuid' => 'O status deve ser um UUID',
            'status_id.exists' => 'O status informado não existe',
            'imagem_url.required' => 'A imagem é obrigatória',
            'imagem_url.string' => 'A imagem deve ser uma string',
            'imagem_url.max' => 'A imagem deve ter no máximo 255 caracteres',
            'categoria_id.required' => 'A categoria é obrigatória',
            'categoria_id.exists' => 'A categoria informada não existe',
            'tipo_equipamento_id.required' => 'O tipo de equipamento é obrigatório',
            'tipo_equipamento_id.exists' => 'O tipo de equipamento informado não existe',
        ]);
        $status = Status::find($data['status_id']);
        if (!$status) {
            return response()->json(['message' => 'Status informado não existe'], 400);
        }
        $usuario = User::find($data['usuario_id']);
        if (!$usuario) {
            return response()->json(['message' => 'Usuário informado não existe'], 400);
        }

        try {
            $equipamento = Equipamento::create([
                'eqi_name' => $data['nome'],
                'eqi_descricao' => $data['descricao'],
                'eqi_serie' => $data['serie'],
                'eqi_status_id' => $status->id,
                'imagem_url' => $data['imagem_url'],
                'usuario_id' => $usuario->id,
                'categoria_id' => $data['categoria_id'],
                'tipo_equipamento_id' => $data['tipo_equipamento_id'],
            ]);

            $equipamento->save();
            return response()->json(['message' => 'Equipamento criado com sucesso', 'equipamento' => $equipamento], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao criar equipamento: ' . $e->getMessage()], 500);
        }
    }
}
