<?php

namespace App\Support;

/**
 * Definición central de roles y reportes del sistema.
 *
 * - ROLES: qué puede OPERAR el usuario (menús, cargas, ABM). Un usuario puede tener varios.
 *   Cada rol otorga uno o más permisos de Spatie (ver ROL_PERMISOS).
 * - REPORTES: qué tarjetas/reportes ve en el Home. Se tildan aparte, por usuario.
 *
 * Para agregar un reporte nuevo:
 *   1) Agregarlo en REPORTES (clave = nombre del permiso).
 *   2) Crear el permiso en la base (migración o SQL en la tabla `permissions`).
 *   3) Proteger su ruta con ->middleware('permission:<clave>') en routes/web.php.
 *   4) Envolver su tarjeta en home.blade.php con @can('<clave>') ... @endcan.
 */
class Permisos
{
    /** Roles que se administran desde el ABM de usuarios (clave => etiqueta). */
    public const ROLES = [
        'admin'     => 'Administrador',
        'agricola'  => 'Agrícola',
        'ganadero'  => 'Ganadero',
        'comercial' => 'Comercial',
    ];

    /** Descripción corta de cada rol, para mostrar en el formulario. */
    public const ROLES_DESCRIPCION = [
        'admin'     => 'Ve y administra todo, incluidos los usuarios y los ABM.',
        'agricola'  => 'Avance de Campo: carga, ve y edita solo sus registros. También Lluvias.',
        'ganadero'  => 'Menú Ganadería (Haciendas).',
        'comercial' => 'Menú Comerciales (Contratos, Compras, Calificación Créditos).',
    ];

    /** Permisos de área que otorga cada rol. El admin recibe todos los permisos. */
    public const ROL_PERMISOS = [
        'agricola'  => ['ver_agricola'],
        'ganadero'  => ['ver_ganadero'],
        'comercial' => ['ver_comercial'],
    ];

    /** Reportes del Home (permiso => etiqueta y detalle). */
    public const REPORTES = [
        'reporte_agricola' => [
            'label'   => 'Agrícola',
            'detalle' => 'Margen Bruto y Cosecha',
        ],
        'reporte_ganadero' => [
            'label'   => 'Ganadería',
            'detalle' => 'Tablero de Ganadería',
        ],
        'reporte_flujo_fondo' => [
            'label'   => 'Flujo de Fondo',
            'detalle' => 'Tablero de Flujo de Fondo',
        ],
    ];

    /** Permisos de área (los otorgan los roles). */
    public const AREAS = ['ver_agricola', 'ver_ganadero', 'ver_comercial'];

    /** Todos los permisos del sistema (el admin los tiene todos). */
    public static function todos(): array
    {
        return array_merge(self::AREAS, array_keys(self::REPORTES));
    }
}
