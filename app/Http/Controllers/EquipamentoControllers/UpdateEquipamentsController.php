<?php

namespace App\Http\Controllers\EquipamentoControllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;
use App\Models\Status;
use App\Models\User;
use App\Http\Controllers\Controller;

class UpdateEquipamentsController extends Controller
{
    public function __invoke(Request $request, String $id)
    {
        $equipament = Equipamento::findOrFail($id);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'serie' => 'required|string|max:255|regex:/^[0-9]+$/',
            'status_id' => 'required|integer|exists:status,id',
            'imagem_url' => 'required|string|max:255',
            'usuario_id' => 'required|uuid|exists:users,id',
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
        ]);
        $status = Status::find($data['status_id']);
        if (!$status) {
            return response()->json(['message' => 'Status informado não existe'], 400);
        }
        $usuario = User::find($data['usuario_id']);
        if (!$usuario) {
            return response()->json(['message' => 'Usuário informado não existe'], 400);
        }

        try{
            $equipament -> update($data);
            return response()->json(['message' => 'Equipamento atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar equipamento'], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Equipamento não encontrado'], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Erro ao atualizar equipamento'], 500);
        }
    }
}
