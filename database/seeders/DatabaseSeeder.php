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
            'admin' => Role::create(['name' => 'admin']),
            'user' => Role::create(['name' => 'user']),
        ];
    }

    private function createPermissions(): Collection
    {
        $permissionsNames = [
            'create-users',
            'read-users',
            'update-users',
            'delete-users',
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
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => 'password',
                'role' => 'user',
            ],
        ];
        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
            ]);
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