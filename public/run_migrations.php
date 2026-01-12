<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);

// Limpieza opcional
$kernel->call('config:clear');
$kernel->call('cache:clear');
$kernel->call('route:clear');
$kernel->call('view:clear');

echo "🔧 Ejecutando migraciones...\n";
$exitCode = $kernel->call('migrate', ['--force' => true]);

if ($exitCode === 0) {
    echo "✅ Migraciones ejecutadas correctamente.\n";
} else {
    echo "❌ Hubo errores al ejecutar las migraciones. Código: {$exitCode}\n";
}
