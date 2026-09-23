<?php

namespace App\Http\Controllers\RoleControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class GetAllRolesControlles extends Controller
{
    public function __invoke(Request $request)
    {
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        return response()->json($roles);
    }
}
