<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EditUserControllers extends Controller
{
    public function editUser(Request $request, string $id)
    {
        try {
            $token = $request->cookie('access_token');
            if (! $token) {
                return response()->json(['message' => 'O cookie não chegou no backend'], 401);
            }

            $authUser = JWTAuth::setToken($token)->authenticate();
            if (! $authUser) {
                return response()->json(['message' => 'Token inválido'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        $user = User::findOrFail($id);
        $isAdmin = $authUser->hasRole('Administrador');
        $isSelf = (string) $authUser->id === (string) $user->id;

        if (! $isAdmin && ! $isSelf) {
            return response()->json(['message' => 'Você não tem permissão para editar este usuário'], 403);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'celular' => 'required|string|max:20',
            'departamento_id' => 'required|uuid|exists:departamentos,id',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ], [
            'nome.required' => 'O nome é obrigatório',
            'nome.string' => 'O nome deve ser uma string',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres',
            'celular.required' => 'O celular é obrigatório',
            'departamento_id.required' => 'O departamento é obrigatório',
            'departamento_id.exists' => 'O departamento informado não existe',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser um email válido',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'email.unique' => 'O email já está em uso por outro usuário',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'roles.array' => 'O perfil de acesso deve ser um array',
            'roles.*.exists' => 'O perfil de acesso informado não existe',
        ]);

        try {
            $user->nome = $data['nome'];
            $user->celular = $data['celular'];
            $user->departamento_id = $data['departamento_id'];
            $user->email = $data['email'];

            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            if ($isAdmin && array_key_exists('roles', $data) && $data['roles'] !== null) {
                $user->syncRoles($data['roles']);
            }

            return response()->json([
                'message' => 'Usuário editado com sucesso',
                'user' => $user->load('roles'),
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao editar usuário'], 500);
        }
    }
}
