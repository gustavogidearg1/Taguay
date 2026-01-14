<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class MigrateLegacyRoleIdSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            1 => 'admin',
            2 => 'admin',
            3 => 'cliente', // o 'editor' si lo creás
            4 => 'cliente',
        ];

        User::query()->select(['id','role_id'])->chunkById(200, function ($users) use ($map) {
            foreach ($users as $u) {
                $roleName = $map[(int)$u->role_id] ?? 'cliente';
                $u->syncRoles([$roleName]);
            }
        });
    }
}
