<?php

namespace Database\Seeders;

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
        $this->assignRolesToUsers($roles, $users);
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
        ];
        foreach ($permissionsNames as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }
        return Permission::all();
    }

    private function assignPermissionsToRoles(array $roles, Collection $permissions): void
    {
        
        $roles['admin']->syncPermissions($permissions);
        $roles['user']->syncPermissions($permissions->where('name', 'read-users'));

    }

    private function createUsers(): array
    {
        $users = [
            [
                'nome' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'nome' => 'User',
                'email' => 'user@example.com',
                'password' => 'password',
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
        dd($roles);
    
        foreach ($users as $role => $roleUsers) {
            foreach ($roleUsers as $user) {
                $user->assignRole($roles[$role]);
            }
        }
    }
}