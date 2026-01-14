<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles base
        $admin   = Role::findOrCreate('admin', 'web');
        $cliente = Role::findOrCreate('cliente', 'web');

        // Permisos (áreas combinables)
        $perms = ['ver_agricola','ver_ganadero','ver_comercial'];
        foreach ($perms as $p) {
            Permission::findOrCreate($p, 'web');
        }

        // Admin ve todo
        $admin->syncPermissions($perms);
    }
}
