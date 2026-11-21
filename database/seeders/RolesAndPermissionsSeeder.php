<?php

namespace Database\Seeders;

use App\Support\Permisos;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea roles y permisos según App\Support\Permisos.
 * - admin: todos los permisos.
 * - agricola / ganadero / comercial: su permiso de área.
 * - Los reportes (reporte_*) se asignan por usuario desde el ABM de usuarios.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permisos::todos() as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        foreach (array_keys(Permisos::ROLES) as $rol) {
            Role::findOrCreate($rol, 'web');
        }

        Role::findByName('admin', 'web')->syncPermissions(Permisos::todos());

        foreach (Permisos::ROL_PERMISOS as $rol => $permisos) {
            Role::findByName($rol, 'web')->syncPermissions($permisos);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
