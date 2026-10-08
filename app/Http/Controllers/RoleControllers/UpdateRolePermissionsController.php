<?php

namespace App\Http\Controllers\RoleControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UpdateRolePermissionsController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required|uuid|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'required|uuid|exists:permissions,id',
        ]);

        $role = Role::findOrFail($data['role_id']);

        try {
            $role->permissions()->sync($data['permissions']);
            return response()->json([
                'message' => 'Permissões da role atualizadas com sucesso.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar as permissões da role.',
                'error' => $e->getMessage()
            ], 500);
        }

         
    }
}