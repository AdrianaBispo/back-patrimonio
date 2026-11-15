<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = $this->createRoles();
        $permissions = $this->createPermissions();
        $this->assignPermissionsToRoles($roles, $permissions);
        $users = $this->createUsers();
        $this->assignRolesToUsers($users, $roles);
        $this->createStatus();
    }

    private function createStatus(): void
    {
        $statusNames = [
            'Disponível',
            'Em uso',
            'Em manutenção',
            'Emprestado',
            'Baixado',
        ];

        foreach ($statusNames as $statusName) {
            Status::firstOrCreate(['nome' => $statusName]);
        }
    }

    private function createRoles(): array
    {
        return [
            'admin' => Role::firstOrCreate(['name' => 'admin']),
            'user' => Role::firstOrCreate(['name' => 'user']),
        ];
    }

    private function createPermissions(): Collection
    {
        $permissionsNames = [
            'create-users',
            'read-users',
            'update-users',
            'delete-users',
            'create-equipaments',
            'update-equipaments',
            'get-all-equipaments',
            'get-equipaments',
            'create-historic-equipaments',
            'get-all-historic-equipaments',
            'get-historic-equipaments',
            'create-categoria',
            'get-all-categoria',
            'update-categoria',
            'create-tipo-equipamento',
            'get-all-tipo-equipamento',
            'update-tipo-equipamento',
            'desactivate-tipo-equipamento',
            'activate-tipo-equipamento',
            'get-all-status',
        ];
        foreach ($permissionsNames as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        return Permission::all();
    }

    private function assignPermissionsToRoles(array $roles, Collection $permissions): void
    {
        
        $roles['admin']->syncPermissions($permissions);
        $roles['user']->syncPermissions($permissions->whereIn('name', [
            'read-users',
            'get-all-equipaments',
            'get-equipaments',
            'get-all-historic-equipaments',
            'get-historic-equipaments',
            'get-all-categoria',
            'get-all-tipo-equipamento',
            'get-all-status',
        ]));

    }

    private function createUsers(): array
    {
        $users = [
            [
                'nome' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'senha1234',
                'role' => 'admin',
            ],
            [
                'nome' => 'User',
                'email' => 'user@example.com',
                'password' => 'senha1234',
                'role' => 'user',
            ],
        ];
    
        $createdUsers = [];
    
        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'nome' => $userData['nome'],
                    'password' => bcrypt($userData['password']),
                    'celular' => '',
                    'departamento_id' => null,
                    'email_verified_at' => null,
                    'remember_token' => null,
                ]
            );
    
            if (!isset($createdUsers[$userData['role']])) {
                $createdUsers[$userData['role']] = [];
            }
    
            $createdUsers[$userData['role']][] = $user;
        }
    
        return $createdUsers;
    }

    private function assignRolesToUsers(array $users, array $roles): void
    {
        foreach ($users as $role => $roleUsers) {
            foreach ($roleUsers as $user) {
                $user->assignRole($roles[$role]);
            }
        }
    }
}