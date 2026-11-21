<?php

use App\Models\User;
use App\Support\Permisos;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Reorganiza roles y permisos:
 * - Crea los permisos de reportes del Home.
 * - Cada rol (agricola, ganadero, comercial) otorga su permiso de área; admin recibe todos.
 * - Migra a los usuarios actuales sin que nadie pierda acceso:
 *     · las áreas tildadas (permisos directos ver_*) pasan a ser roles,
 *     · se tildan los reportes que hoy ya veían (Margen Bruto, Cosecha y Flujo de Fondo
 *       los veía cualquier usuario; Ganadería solo quien tenía ver_ganadero).
 */
return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Permisos y roles
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

        // 2) Usuarios: áreas directas -> roles, y reportes que ya veían
        $areaARol = [
            'ver_agricola'  => 'agricola',
            'ver_ganadero'  => 'ganadero',
            'ver_comercial' => 'comercial',
        ];

        User::with(['roles', 'permissions'])->get()->each(function (User $user) use ($areaARol) {
            $directos = $user->permissions->pluck('name');

            if (! $user->hasRole('admin')) {
                foreach ($areaARol as $permiso => $rol) {
                    if ($directos->contains($permiso)) {
                        $user->assignRole($rol);
                    }
                }

                $reportes = ['reporte_agricola', 'reporte_flujo_fondo'];
                if ($directos->contains('ver_ganadero') || $user->hasRole('ganadero')) {
                    $reportes[] = 'reporte_ganadero';
                }
                $user->givePermissionTo($reportes);
            }

            // Las áreas ya vienen dadas por los roles: se quitan los permisos directos de área
            foreach (Permisos::AREAS as $permiso) {
                if ($directos->contains($permiso)) {
                    $user->revokePermissionTo($permiso);
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Reversión parcial: se quitan los permisos de reportes. Los roles asignados se mantienen.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', array_keys(Permisos::REPORTES))->get()->each->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
