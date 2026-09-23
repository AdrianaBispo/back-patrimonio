<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EditUserControllers extends Controller
{
    public function editUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nome'            => 'required|string|max:255',
            'celular'         => 'required|string|max:20',
            'departamento_id' => 'required|uuid|exists:departamentos,id',
            'email'           => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password'        => 'nullable|string|min:8',
            'roles'           => 'required|array',
            'roles.*'         => 'exists:roles,id',
        ], [
            'nome.required'            => 'O nome é obrigatório',
            'nome.string'              => 'O nome deve ser uma string',
            'nome.max'                 => 'O nome deve ter no máximo 255 caracteres',
            'celular.required'         => 'O celular é obrigatório',
            'departamento_id.required' => 'O departamento é obrigatório',
            'departamento_id.exists'   => 'O departamento informado não existe',
            'email.required'           => 'O email é obrigatório',
            'email.email'              => 'O email deve ser um email válido',
            'email.max'                => 'O email deve ter no máximo 255 caracteres',
            'email.unique'             => 'O email já está em uso por outro usuário',
            'password.min'             => 'A senha deve ter no mínimo 8 caracteres',
            'roles.required'           => 'O perfil de acesso é obrigatório',
            'roles.array'              => 'O perfil de acesso deve ser um array',
            'roles.*.exists'           => 'O perfil de acesso informado não existe',
        ]);

        try {
            $user->nome = $data['nome'];
            $user->celular = $data['celular'];
            $user->departamento_id = $data['departamento_id'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return response()->json([
                'message' => 'Usuário editado com sucesso',
                'user'    => $user->load('roles')
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao editar usuário'], 500);
        }
    }
}