<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title')</title>

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

            {{-- Ganadería --}}
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('haciendas.*') ? 'fw-semibold' : '' }}"
                 href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Ganadería
              </a>
              <ul class="dropdown-menu">
                @if (Route::has('haciendas.index'))
                  <li><a class="dropdown-item" href="{{ route('haciendas.index') }}">Haciendas</a></li>
                @else
                  <li><span class="dropdown-item-text text-muted">Haciendas (sin ruta)</span></li>
                @endif
              </ul>
            </li>

            {{-- Agrícola --}}
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('lluvias.*') ? 'fw-semibold' : '' }}"
                 href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Agrícola
              </a>
              <ul class="dropdown-menu">
                @if (Route::has('lluvias.index'))
                  <li><a class="dropdown-item" href="{{ route('lluvias.index') }}">Lluvias</a></li>
                @else
                  <li><span class="dropdown-item-text text-muted">Lluvias (sin ruta)</span></li>
                @endif
              </ul>
            </li>

            {{-- ABM (solo admin) --}}
            @if ((Auth::user()->role_id ?? null) == 1)
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('abm/*') || request()->routeIs('users.*') ? 'fw-semibold' : '' }}"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ABM
                </a>
                <ul class="dropdown-menu">
                  @if (Route::has('users.index'))
                    <li>
                      <a class="dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                         href="{{ route('users.index') }}">
                        <i class="bi bi-people-fill me-2"></i>Usuarios
                      </a>
                    </li>
                  @else
                    <li><span class="dropdown-item-text text-muted">Usuarios (sin ruta)</span></li>
                  @endif

                  {{-- Futuro: agregás más ABM acá --}}
                  {{-- <li><a class="dropdown-item" href="#">Provincias</a></li> --}}
                  {{-- <li><a class="dropdown-item" href="#">Localidades</a></li> --}}
                </ul>
              </li>
            @endif

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
                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Cerrar sesión') }}
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
</body>
</html>
