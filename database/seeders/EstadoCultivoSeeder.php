<?php

namespace Database\Seeders;

use App\Models\EstadoCultivo;
use Illuminate\Database\Seeder;

class EstadoCultivoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            'Siembra',
            'Emergencia',
            'Vegetativo',
            'Floración',
            'Llenado',
            'Madurez',
            'Cosecha',
        ];

        foreach ($estados as $estado) {
            EstadoCultivo::firstOrCreate(
                ['nombre' => $estado],
                ['activo' => true]
            );
        }
    }
}
