<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Taguay') }}: Welcome</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
        @endif
    </head>
    <body class="font-sans antialiased">
        <!-- Incluir el menú -->
        @include('layouts.menu')

        <!-- Contenido de la página -->
        <div class="min-h-screen" style="background-color: #f3f4f6;">
            <div class="container mx-auto px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-900">Bienvenido Taguay SRL</h1>
                <p class="mt-4 text-gray-600">
                    Si ya tienes un usuario y contraseña, por favor
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">accede aquí</a>.
                    Si no tienes acceso, solicítalo a
                    <a href="mailto:lcingolani@taguay.com.ar" class="text-blue-600 hover:underline">lcingolani@taguay.com.ar</a>.
                    ¡Gracias!
                </p>
            </div>
        </div>
    </body>
</html>
