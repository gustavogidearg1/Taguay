@extends('layouts.app')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header header-mint py-3 d-flex justify-content-between align-items-center">
      <h3 class="mat-title mb-0"><i class="fa-solid fa-users me-2"></i> Usuarios</h3>

      <a href="{{ route('users.create') }}" class="btn btn-success">
        <i class="fa-solid fa-user-plus me-1"></i> Crear Usuario
      </a>
    </div>

    <div class="card-body">

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      {{-- =========================
           FILTROS
      ========================== --}}
      <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end mb-3">

        <div class="col-12 col-md-4">
          <label class="form-label mb-1">Buscar</label>
          <input type="text" name="q" class="form-control" placeholder="Nombre o email…" value="{{ $search }}">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Rol</label>
          <select name="role" class="form-select">
            <option value="">Todos</option>
            @foreach ($roles as $clave => $label)
              <option value="{{ $clave }}" @selected(request('role') === $clave)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Reporte</label>
          <select name="reporte" class="form-select">
            <option value="">Todos</option>
            @foreach ($reportes as $clave => $reporte)
              <option value="{{ $clave }}" @selected(request('reporte') === $clave)>{{ $reporte['label'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-2 d-grid d-md-flex gap-2">
          <button class="btn btn-dark">
            <i class="fa fa-search me-1"></i> Filtrar
          </button>
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-rotate-left"></i>
          </a>
        </div>

        {{-- Mantener orden al filtrar --}}
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="order" value="{{ $order }}">
      </form>

      @php
        // Link para ordenar por columna conservando los filtros
        $sortLink = function (string $field) use ($sort, $order) {
          $nextOrder = ($sort === $field && $order === 'asc') ? 'desc' : 'asc';
          return route('users.index', array_merge(request()->query(), ['sort' => $field, 'order' => $nextOrder]));
        };
        $sortIcon = function (string $field) use ($sort, $order) {
          if ($field !== $sort) return 'fa fa-sort text-muted';
          return $order === 'asc' ? 'fa fa-sort-alpha-down' : 'fa fa-sort-alpha-up';
        };
      @endphp

      {{-- =========================
           TABLA
      ========================== --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>
                <a href="{{ $sortLink('name') }}" class="text-decoration-none text-dark">
                  Nombre <i class="{{ $sortIcon('name') }}"></i>
                </a>
              </th>
              <th>
                <a href="{{ $sortLink('email') }}" class="text-decoration-none text-dark">
                  Email <i class="{{ $sortIcon('email') }}"></i>
                </a>
              </th>
              <th>Roles</th>
              <th>Reportes</th>
              <th class="text-end" style="width: 170px;">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($users as $user)
              @php
                $userRoles = $user->roles->pluck('name');
                $esAdmin = $userRoles->contains('admin');
                $userReportes = $user->permissions->pluck('name');
              @endphp

              <tr>
                <td class="fw-semibold">{{ $user->name }}</td>
                <td>
                  {{ $user->email }}
                  @if ($user->telefono_whatsapp)
                    <div class="small text-success" title="Puede cargar Avance de Campo por WhatsApp">
                      <i class="bi bi-whatsapp"></i> +{{ $user->telefono_whatsapp }}
                    </div>
                  @endif
                </td>

                <td>
                  @forelse ($userRoles as $r)
                    <span class="badge {{ $r === 'admin' ? 'bg-dark' : 'bg-success' }}">{{ $roles[$r] ?? $r }}</span>
                  @empty
                    <span class="text-muted">—</span>
                  @endforelse
                </td>

                <td>
                  @if ($esAdmin)
                    <span class="badge text-bg-light border">Todos</span>
                  @else
                    @php $tiene = collect($reportes)->keys()->filter(fn ($k) => $userReportes->contains($k)); @endphp
                    @forelse ($tiene as $k)
                      <span class="badge text-bg-light border">{{ $reportes[$k]['label'] }}</span>
                    @empty
                      <span class="text-muted">—</span>
                    @endforelse
                  @endif
                </td>

                <td class="text-end">
                  <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-pen"></i> Editar
                  </a>

                  <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar el usuario {{ $user->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay usuarios para mostrar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- =========================
           PAGINACIÓN
      ========================== --}}
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
          Mostrando {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} de {{ $users->total() }} usuarios
        </div>
        <div>{{ $users->links() }}</div>
      </div>

    </div>
  </div>
</div>
@endsection
