<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);
$kernel->call('config:clear');
$kernel->call('cache:clear');
$kernel->call('route:clear');
$kernel->call('view:clear');
$kernel->call('config:cache');

echo "✅ Caché limpiada correctamente.";
