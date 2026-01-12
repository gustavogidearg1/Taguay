<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
                // Crear roles de prueba
                Role::factory()->create(['name' => 'admin']);
                Role::factory()->create(['name' => 'editor']);
                Role::factory()->create(['name' => 'user']);

        User::factory()->create([
            'name' => 'LuciaC',
            'email' => 'lcingolani@taguay.com.ar',
        ]);
    }
}
