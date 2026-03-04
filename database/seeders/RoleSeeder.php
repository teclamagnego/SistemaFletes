<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos
        $permissions = [
            'users.index', 'users.create', 'users.edit', 'users.delete',
            'roles.index', 'roles.create', 'roles.edit', 'roles.delete',
            'clientes.index', 'clientes.create', 'clientes.edit', 'clientes.delete',
            'rubros.index', 'rubros.create', 'rubros.edit', 'rubros.delete',
            'proveedores.index', 'proveedores.create', 'proveedores.edit', 'proveedores.delete',
            'articulos.index', 'articulos.create', 'articulos.edit', 'articulos.delete',
            'agencies.index', 'agencies.create', 'agencies.edit', 'agencies.delete',
            'carriers.index', 'carriers.create', 'carriers.edit', 'carriers.delete',
            'shipments.index', 'shipments.create', 'shipments.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $operador = Role::firstOrCreate(['name' => 'operador']);
        $operador->givePermissionTo([
            'clientes.index', 'clientes.create', 'clientes.edit',
            'rubros.index', 'rubros.create', 'rubros.edit',
            'proveedores.index', 'proveedores.create', 'proveedores.edit',
            'articulos.index', 'articulos.create', 'articulos.edit',
            'agencies.index', 'agencies.create', 'agencies.edit',
            'carriers.index', 'carriers.create', 'carriers.edit',
            'shipments.index', 'shipments.create', 'shipments.view',
        ]);

        $consulta = Role::firstOrCreate(['name' => 'consulta']);
        $consulta->givePermissionTo([
            'clientes.index', 'rubros.index', 'proveedores.index', 'articulos.index', 'agencies.index', 'carriers.index', 'shipments.index', 'shipments.view',
        ]);

        // Usuario Admin
        $user = User::firstOrCreate(
        ['email' => 'teclamagnego@gmail.com'],
        [
            'name' => 'Admin',
            'password' => bcrypt('tecla'),
        ]
        );
        $user->assignRole('admin');
    }
}