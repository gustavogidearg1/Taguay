@extends('layouts.app')

@section('content')
<div class="container">

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Usuarios</h5>

      <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Crear Usuario
      </a>
    </div>

    <div class="card-body">

      {{-- =========================
           FILTROS
      ========================== --}}
      <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end mb-3">

        <div class="col-12 col-md-4">
          <label class="form-label mb-1">Buscar</label>
          <input type="text"
                 name="q"
                 class="form-control"
                 placeholder="Nombre o email…"
                 value="{{ request('q') }}">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Rol</label>
          <select name="role" class="form-select">
            <option value="">Todos</option>
            @foreach(($roles ?? collect()) as $r)
              <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ $r->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Área habilitada</label>
          @php
            // labels a prueba de encoding (tildes con unicode)
            $permLabels = [
              'ver_agricola'  => "Agr\u{00ED}cola",
              'ver_ganadero'  => 'Ganadero',
              'ver_comercial' => 'Comercial',
            ];
          @endphp
          <select name="perm" class="form-select">
            <option value="">Todas</option>
            @foreach(($perms ?? collect()) as $p)
              <option value="{{ $p->name }}" @selected(request('perm') === $p->name)>
                {{ $permLabels[$p->name] ?? $p->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-2 d-grid d-md-flex gap-2">
          <button class="btn btn-dark">
            <i class="fa fa-search me-1"></i> Filtrar
          </button>

          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-rotate-left me-1"></i> Limpiar
          </a>
        </div>

        {{-- Mantener orden al filtrar --}}
        <input type="hidden" name="sort" value="{{ $sort ?? request('sort','name') }}">
        <input type="hidden" name="order" value="{{ $order ?? request('order','asc') }}">
      </form>

      @php
        function sortIcon($field, $sort, $order) {
          if ($field === $sort) {
            return $order === 'asc' ? 'fa fa-sort-alpha-down' : 'fa fa-sort-alpha-up';
          }
          return 'fa fa-sort';
        }

        $sort = $sort ?? request('sort','name');
        $order = $order ?? request('order','asc');

        // helper para mantener filtros al ordenar
        function sortLink($field, $sort, $order) {
          $nextOrder = ($sort === $field && $order === 'asc') ? 'desc' : 'asc';

          return route('users.index', array_merge(request()->query(), [
            'sort'  => $field,
            'order' => $nextOrder,
          ]));
        }
      @endphp

      {{-- =========================
           TABLA
      ========================== --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>
                <a href="{{ sortLink('name', $sort, $order) }}" class="text-decoration-none text-dark">
                  Nombre <i class="{{ sortIcon('name', $sort, $order) }}"></i>
                </a>
              </th>

              <th>
                <a href="{{ sortLink('email', $sort, $order) }}" class="text-decoration-none text-dark">
                  Email <i class="{{ sortIcon('email', $sort, $order) }}"></i>
                </a>
              </th>

              <th>
                <a href="{{ sortLink('role', $sort, $order) }}" class="text-decoration-none text-dark">
                  Rol <i class="{{ sortIcon('role', $sort, $order) }}"></i>
                </a>
              </th>

              <th>Áreas</th>

              <th style="width: 220px;">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($users as $user)
              @php
                $roleNames = $user->roles?->pluck('name')->values() ?? collect();
                $primaryRole = $roleNames->first() ?? '—';

                // Permisos directos del usuario (si además querés incluir heredados, avisame)
                $directPerms = method_exists($user, 'getDirectPermissions')
                  ? $user->getDirectPermissions()->pluck('name')->values()->all()
                  : [];

                $areaBadges = collect($directPerms)->filter(fn($x) => in_array($x, ['ver_agricola','ver_ganadero','ver_comercial'], true));
              @endphp

              <tr>
                <td class="fw-semibold">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>

                <td>
                  <span class="badge bg-secondary">{{ $primaryRole }}</span>
                  @if($roleNames->count() > 1)
                    <span class="text-muted small ms-1">(+{{ $roleNames->count()-1 }})</span>
                  @endif
                </td>

                <td>
                  @if($areaBadges->isEmpty())
                    <span class="text-muted">—</span>
                  @else
                    @foreach($areaBadges as $p)
                      <span class="badge text-bg-light border">
                        {{ $permLabels[$p] ?? $p }}
                      </span>
                    @endforeach
                  @endif
                </td>

                <td>
                  <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                  <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-sm btn-outline-danger">Eliminar</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  No hay usuarios para mostrar.
                </td>
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

        <div>
          {{ $users->links() }}
        </div>
      </div>

    </div>
  </div>
</div>

</div>
@endsection
