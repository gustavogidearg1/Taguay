@extends('layouts.app')

@section('content')

<style>
    /* Estilo personalizado para reducir el tamaño de las imágenes */
    .img-reduced {
        width: 70%; 
        height: auto; 
        margin: 0 auto; /* Centra la imagen horizontalmente */
        display: block; /* Asegura que la imagen se comporte como un bloque */
    }

    /* Estilo para hacer el fondo de las tarjetas transparente */
    .card-transparent {
        background-color: transparent !important; /* Fondo transparente */
        border: none !important; /* Elimina el borde */
    }
</style>

<div class="container-fluid bg-custom-gray"> <!-- Contenedor con fondo gris -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="row">
            <!-- Tarjeta 1 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">
                <div class="card h-100 card-transparent"> <!-- Aplicamos card-transparent aquí -->
                    <a href="{{ route('margen-bruto') }}">
                        <img src="{{ asset('storage/images/BtnMargenBruto.png') }}" class="card-img-top img-reduced" alt="Margen Bruto">
                    </a>
                </div>
            </div>

            <!-- Tarjeta 2 -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">
                <div class="card h-100 card-transparent"> <!-- Aplicamos card-transparent aquí -->
                    <a href="{{ route('cosecha') }}">
                        <img src="{{ asset('storage/images/BtnCosecha.png') }}" class="card-img-top img-reduced" alt="Cosecha">
                    </a>
                </div>
            </div>
            
            <!-- Tarjeta 3 (solo para usuarios no invitados) -->
            @if (Auth::user()->role_id !== 4)
            
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">
                    <div class="card h-100 card-transparent">
                    <a href="{{ route('flujo-fondo') }}">
                        <img src="{{ asset('storage/images/BtnFlujoFondo.png') }}" class="card-img-top img-reduced" alt="Flujo de fondo">
                    </div>
                </div>
            @else
                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        Vista Solamente para Administradores y Editores
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection