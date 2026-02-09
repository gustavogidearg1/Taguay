@extends('layouts.app')

@section('title', 'Taguay')

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

  .pbi-frame{
    position: relative;
    width: 100%;
    overflow: hidden;
    border-radius: 12px; /* opcional */
  }
  .pbi-iframe{
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
  }

  /* Ratios exactos según tus medidas */
  .pbi-frame--desktop{ padding-top: 60%; }      /* 250 / 400 */
  .pbi-frame--phone{   padding-top: 120%; }    /* 280 / 240 */

  /* Mostrar solo el que toca */
  .pbi-only-desktop{ display:block; }
  .pbi-only-phone{ display:none; }

  @media (max-width: 576px){
    .pbi-only-desktop{ display:none; }
    .pbi-only-phone{ display:block; }
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

 <div class="container">
  <div class="row g-3">  <!-- g-3 = separación prolija entre columnas -->

    <!-- TILE 1 -->
    <div class="col-12 col-md-6">
      <div class="pbi-only-desktop">
        <div class="pbi-frame pbi-frame--desktop">
          <iframe title="Estado_Campo_Rinde" class="pbi-iframe"
            src="https://app.powerbi.com/view?r=eyJrIjoiNWJkM2IyNTQtMjIyNS00M2IyLWFhNmQtNThmZDUyZDAzMGM4IiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
            loading="lazy" allowfullscreen></iframe>
        </div>
      </div>

      <div class="pbi-only-phone">
        <div class="pbi-frame pbi-frame--phone">
          <iframe title="Estado_Campo_Rinde_Telefono" class="pbi-iframe"
            src="https://app.powerbi.com/view?r=eyJrIjoiZjJjMWVmMjMtYWVlNS00ZDYxLTgwNjUtMzMwMGVkZGE4ZTkwIiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
            loading="lazy" allowfullscreen></iframe>
        </div>
      </div>
    </div>

    <!-- TILE 2 -->
    <div class="col-12 col-md-6">
      <div class="pbi-only-desktop">
        <div class="pbi-frame pbi-frame--desktop">
          <iframe title="Estado_Campo_Superficie" class="pbi-iframe"
            src="https://app.powerbi.com/view?r=eyJrIjoiYWUwNTNjYmYtMjBiZC00ZTlmLThhMmEtYzc2NmM2NjExOTQyIiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
            loading="lazy" allowfullscreen></iframe>
        </div>
      </div>

      <div class="pbi-only-phone">
        <div class="pbi-frame pbi-frame--phone">
          <iframe title="Estado_Campo_Superficie_Telefono" class="pbi-iframe"
            src="https://app.powerbi.com/view?r=eyJrIjoiMDY1N2IyNGItMDE0YS00MzU4LTliNDQtYzEzNTMwZmE0YTEyIiwidCI6ImZmZDgyMjAxLWJjNzUtNDA5OS05MjkzLWRlNDdiMzkyNmM5YiIsImMiOjR9"
            loading="lazy" allowfullscreen></iframe>
        </div>
      </div>
    </div>

  </div>
</div>


@endsection
