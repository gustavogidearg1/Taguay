<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title')</title>

  <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer" />

  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (para bi bi-...) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    .bg-custom-green { background-color: #fafafa !important; }
    body { background-color: #f3f4f6; }
  </style>
@stack('styles')
</head>

<body>
<div id="app">

  <nav class="navbar navbar-expand-md navbar-light shadow-sm bg-custom-green">
    <div class="container">

      {{-- Logo --}}
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('storage/images/logo-taguay.png') }}" alt="Logo" height="40">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarMain" aria-controls="navbarMain"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMain">

        {{-- Left --}}
<ul class="navbar-nav me-auto">
  @auth
  @can('ver_ganadero')
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        Ganaderia
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('haciendas.index') }}"><i class="fa-solid fa-warehouse me-1"></i>Haciendas</a></li>
       {{--  <li><a class="dropdown-item" href="{{ route('ganadero.index') }}">Inicio Ganadero</a></li> --}}
      </ul>
    </li>
  @endcan

  @can('ver_agricola')
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        Agricola
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('lluvias.index') }}"><i class="fa-solid fa-cloud-rain me-1"></i>Lluvias</a></li>
        {{-- <li><a class="dropdown-item" href="{{ route('agricola.index') }}">Inicio AgrÃ­cola</a></li> --}}
      </ul>
    </li>
  @endcan


  @role('admin')
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">ABM</a>
      <ul class="dropdown-menu">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('establecimientos.index') }}">
            <i class="fa-solid fa-building me-1"></i>Establecimientos</a>
    </li>

<li>
    <a class="dropdown-item" href="{{ route('monedas.index') }}">
        <i class="fa-solid fa-coins me-2"></i> Monedas
    </a>
</li>

<li>
  <a class="dropdown-item" href="{{ route('cultivos.index') }}">
    <i class="fa-solid fa-seedling me-2"></i> Cultivos
  </a>
</li>

<li>
  <a class="dropdown-item" href="{{ route('campanias.index') }}">
    <i class="fa-solid fa-calendar-days me-2"></i> Campañas
  </a>
</li>


        <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fa-solid fa-users me-1"></i>Usuarios</a></li>
      </ul>
    </li>
  @endrole
@endauth

</ul>


        {{-- Right --}}
        <ul class="navbar-nav ms-auto">
          @guest
            @if (Route::has('login') && !request()->is('login'))
              <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
              </li>
            @endif

            <li class="nav-item">
              <a class="nav-link" href="mailto:lcingolani@taguay.com.ar">Solicitar Registro</a>
            </li>
          @else
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle"
                 href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->name }}
              </a>

              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                  <a class="dropdown-item" href="{{ route('logout') }}"
                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Cerrar sesion') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                </li>
              </ul>
            </li>
          @endguest
        </ul>

      </div>
    </div>
  </nav>

  <main class="py-4">
    @yield('content')
  </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
