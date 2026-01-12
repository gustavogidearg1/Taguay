<?php
/**
 * Script de mantenimiento: agrega columna user_id a 'haciendas' y su FK a 'users(id)'.
 * Uso: https://tu-dominio/fix_haciendas_user_id.php?token=TU_TOKEN_SEGURO
 * IMPORTANTE: borrar el archivo luego de ejecutarlo.
 */

declare(strict_types=1);

// =================== Seguridad ===================
$token = $_GET['token'] ?? '';
$EXPECTED_TOKEN = 'REEMPLAZA_POR_UN_TOKEN_FUERTE_32+_CHARS'; // <- cámbialo
if (!hash_equals($EXPECTED_TOKEN, $token)) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}

// =================== Bootstrap Laravel ===================
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap del kernel de consola para habilitar Facades (DB/Schema/etc.)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

try {
    // Nombre de la BD actual
    $dbName = DB::getDatabaseName();

    echo "Base de datos: {$dbName}\n";

    // ---------- 1) Verificar/crear columna user_id ----------
    $col = DB::selectOne("SHOW COLUMNS FROM `haciendas` LIKE 'user_id'");
    if ($col) {
        echo "[OK] Columna `user_id` ya existe en `haciendas`.\n";
    } else {
        echo "[..] Agregando columna `user_id`...\n";
        // La ubicamos después de peso_vivo_menos_8 si existe; si no, la ubica al final
        DB::statement("ALTER TABLE `haciendas` ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `peso_vivo_menos_8`");
        echo "[OK] Columna `user_id` agregada.\n";
    }

    // ---------- 2) Verificar/crear FK a users(id) ----------
    // Chequeo en information_schema
    $fk = DB::selectOne("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME   = 'haciendas'
          AND COLUMN_NAME  = 'user_id'
          AND REFERENCED_TABLE_NAME IS NOT NULL
        LIMIT 1
    ", [$dbName]);

    if ($fk) {
        echo "[OK] Llave foránea existente sobre `haciendas`.`user_id`.\n";
    } else {
        echo "[..] Creando FK a `users`(`id`) con ON DELETE SET NULL...\n";

        // Asegurarnos de que `users` exista y tenga `id`
        $usersTable = DB::selectOne("SHOW TABLES LIKE 'users'");
        if (!$usersTable) {
            throw new RuntimeException("No existe la tabla `users`. Si tu tabla de usuarios se llama distinto, ajusta el script.");
        }

        // Nombre de constraint único (por si hay colisiones)
        $constraint = "haciendas_user_id_foreign";

        DB::statement("
            ALTER TABLE `haciendas`
              ADD CONSTRAINT `{$constraint}`
              FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
              ON DELETE SET NULL
        ");

        echo "[OK] FK creada.\n";
    }

    // ---------- 3) (Opcional) Backfill de user_id ----------
    // Si querés setear el user_id de registros antiguos a un usuario específico (ej. admin id=1),
    // descomenta estas 2 líneas:
    //
    // $asignarA = 1; // <-- ID del usuario al que asignar los existentes (cambiá según tu caso)
    // DB::statement("UPDATE `haciendas` SET `user_id` = ? WHERE `user_id` IS NULL", [$asignarA]);

    echo "\nTodo listo ✅\n";
    echo "- Si cambiaste el modelo, recordá tener 'user_id' en \$fillable.\n";
    echo "- Eliminá este archivo de /public por seguridad.\n";

} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
    // Opcional: log en Laravel
    try { logger()->error('fix_haciendas_user_id.php: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]); } catch (Throwable $ee) {}
}
