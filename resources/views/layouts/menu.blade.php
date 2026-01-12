<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  .bg-custom-green { background-color: #90EE90 !important; }

  /* Un toque "material" suave */
  .mat-nav {
    border-radius: 14px;
  }
  .mat-nav .nav-link, .mat-nav .dropdown-item {
    font-weight: 500;
  }
  .mat-nav .nav-link.active {
    background: rgba(0,0,0,.06);
    border-radius: 10px;
  }
</style>

<header class="shadow-sm bg-custom-green">
  <div class="container py-3">

    <nav class="navbar navbar-expand-lg mat-nav">
      {{-- Logo --}}
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('storage/images/logo-taguay.png') }}" alt="Logo" height="40">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
              aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          @if (Route::has('login'))
            @auth
              {{-- Dashboard --}}
              <li class="nav-item">
                <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ url('/home') }}">
                  Dashboard
                </a>
              </li>

              {{-- ABM Dropdown --}}
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('abm/*') ? 'active' : '' }}"
                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  ABM
                </a>

                <ul class="dropdown-menu">
                  {{-- Usuarios --}}
                  @if (Route::has('users.index'))
                    <li>
                      <a class="dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                         href="{{ route('users.index') }}">
                        Usuarios
                      </a>
                    </li>
                  @endif

                  {{-- Haciendas --}}
                  @if (Route::has('haciendas.index'))
                    <li>
                      <a class="dropdown-item {{ request()->routeIs('haciendas.*') ? 'active' : '' }}"
                         href="{{ route('haciendas.index') }}">
                        Haciendas
                      </a>
                    </li>
                  @endif

                  {{-- Separador (dejado listo para sumar más ABMs) --}}
                  <li><hr class="dropdown-divider"></li>
                  <li class="dropdown-header text-uppercase small">Próximos</li>
                  <li><span class="dropdown-item-text text-muted small">Más ABMs acá…</span></li>
                </ul>
              </li>

            @else
              {{-- Invitado --}}
              <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">Acceso</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="mailto:lcingolani@taguay.com.ar">Solicitar Registro</a>
              </li>
            @endauth
          @endif
        </ul>

        {{-- Derecha: usuario / logout --}}
        <ul class="navbar-nav ms-auto">
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ auth()->user()->name ?? 'Usuario' }}
              </a>

              <ul class="dropdown-menu dropdown-menu-end">
                {{-- Perfil (si lo tenés) --}}
                @if (Route::has('profile.edit'))
                  <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">Mi perfil</a>
                  </li>
                @endif

                <li><hr class="dropdown-divider"></li>

                {{-- Logout --}}
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                      Cerrar sesión
                    </button>
                  </form>
                </li>
              </ul>
            </li>
          @endauth
        </ul>
      </div>
    </nav>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</header>
