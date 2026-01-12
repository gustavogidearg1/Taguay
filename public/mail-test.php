<?php

use Illuminate\Support\Facades\Mail;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Dirección de prueba
$para = 'gustavog@live.com.ar';

// Enviar email de prueba
Mail::raw('Este es un correo de prueba desde sistema.taguay.com.ar', function ($message) use ($para) {
    $message->to($para)
            ->subject('🛠️ Prueba de correo - Laravel');
});

echo '✅ Correo enviado (si la configuración está bien). Revisa tu bandeja.';
