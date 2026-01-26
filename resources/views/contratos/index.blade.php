{{-- resources/views/contratos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  @php
    $q = $q ?? request('q','');
    $perPage = $perPage ?? (int)request('per_page', 15);
    $sortNow = $sort ?? request('sort','id');
    $dirNow  = $dir  ?? request('dir','desc');

    $sortUrl = function(string $col) {
      $sortNow = request('sort','id');
      $dirNow  = request('dir','desc');
      $nextDir = ($sortNow === $col && $dirNow === 'asc') ? 'desc' : 'asc';
      return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir]);
    };

    $sortIcon = function(string $col) {
      $sortNow = request('sort','id');
      $dirNow  = request('dir','desc');
      if ($sortNow !== $col) return 'fa-solid fa-sort';
      return $dirNow === 'asc' ? 'fa-solid fa-sort-up' : 'fa-solid fa-sort-down';
    };

    $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';
  @endphp

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-file-contract me-2"></i> Contratos
      </h3>

      <div class="ms-auto d-flex gap-2 flex-wrap">
        {{-- Exportar respetando filtros/sort --}}
        <a href="{{ route('contratos.export.excel', request()->query()) }}" class="btn btn-outline-success btn-mat">
          <i class="fa-solid fa-file-excel me-1"></i> Excel
        </a>

        <a href="{{ route('contratos.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-mat">
          <i class="fa-solid fa-file-pdf me-1"></i> PDF
        </a>

        <a href="{{ route('contratos.create') }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-plus me-1"></i> Nuevo
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- Buscador --}}
      <form method="GET" action="{{ route('contratos.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-6">
          <label class="form-label mb-1">Buscar</label>
          <input type="text"
                 name="q"
                 class="form-control"
                 placeholder="Nro contrato, forward, cliente, vendedor..."
                 value="{{ $q }}">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label mb-1">Por página</label>
          <select name="per_page" class="form-select">
            @foreach([10,15,20,50,100] as $n)
              <option value="{{ $n }}" @selected((int)$perPage === $n)>{{ $n }}</option>
            @endforeach
          </select>
        </div>

        {{-- Mantener sort/dir al filtrar --}}
        <input type="hidden" name="sort" value="{{ $sortNow }}">
        <input type="hidden" name="dir" value="{{ $dirNow }}">

        <div class="col-12 col-md-4 d-grid d-md-flex gap-2">
          <button class="btn btn-dark">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
          </button>

          <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-rotate-left me-1"></i> Limpiar
          </a>
        </div>
      </form>

      {{-- Tabla --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:90px;">
                <a href="{{ $sortUrl('nro_contrato') }}" class="text-decoration-none text-dark">
                  Nro <i class="{{ $sortIcon('nro_contrato') }} ms-1"></i>
                </a>
              </th>

              <th style="width:110px;">
                <a href="{{ $sortUrl('num_forward') }}" class="text-decoration-none text-dark">
                  Forward <i class="{{ $sortIcon('num_forward') }} ms-1"></i>
                </a>
              </th>

              <th style="width:110px;">
                <a href="{{ $sortUrl('fecha') }}" class="text-decoration-none text-dark">
                  Fecha <i class="{{ $sortIcon('fecha') }} ms-1"></i>
                </a>
              </th>

              <th style="min-width:220px;">
                <a href="{{ $sortUrl('organizacion') }}" class="text-decoration-none text-dark">
                  Cliente <i class="{{ $sortIcon('organizacion') }} ms-1"></i>
                </a>
              </th>

              <th style="width:140px;">
                <a href="{{ $sortUrl('campania') }}" class="text-decoration-none text-dark">
                  Campaña <i class="{{ $sortIcon('campania') }} ms-1"></i>
                </a>
              </th>

              <th style="width:140px;">
                <a href="{{ $sortUrl('cultivo') }}" class="text-decoration-none text-dark">
                  Cultivo <i class="{{ $sortIcon('cultivo') }} ms-1"></i>
                </a>
              </th>

              {{-- ✅ NUEVAS COLUMNAS --}}
              <th style="width:140px;" class="text-end">
                <a href="{{ $sortUrl('cantidad_tn') }}" class="text-decoration-none text-dark">
                  Cantidad (tn) <i class="{{ $sortIcon('cantidad_tn') }} ms-1"></i>
                </a>
              </th>

              <th style="width:120px;" class="text-end">
                <a href="{{ $sortUrl('precio') }}" class="text-decoration-none text-dark">
                  Precio <i class="{{ $sortIcon('precio') }} ms-1"></i>
                </a>
              </th>

              <th style="min-width:260px;">Obs</th>

              <th style="width:320px; white-space:nowrap;" class="text-end">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($contratos as $c)
              <tr>
                <td class="fw-semibold">{{ $c->nro_contrato }}</td>
                <td>{{ $c->num_forward ?? '—' }}</td>
                <td>{{ optional($c->fecha)->format('d/m/Y') ?? '—' }}</td>

                <td>
                  <div class="fw-semibold">{{ $c->organizacion->name ?? '—' }}</div>
                </td>

                <td>{{ $c->campania->name ?? '—' }}</td>
                <td>{{ $c->cultivo->name ?? '—' }}</td>

                {{-- ✅ NUEVOS DATOS --}}
                <td class="text-end">{{ $fmtNum($c->cantidad_tn) }}</td>
                <td class="text-end">{{ $fmtNum($c->precio) }}</td>
                <td>
                  @if(!empty($c->obs))
                    <div class="text-truncate" style="max-width: 380px;">{{ $c->obs }}</div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

               <td class="text-end">
  <div class="d-grid d-sm-flex justify-content-end gap-1 flex-wrap flex-sm-nowrap">
    <a href="{{ route('contratos.show', $c) }}"
       class="btn btn-sm btn-outline-secondary w-100 w-sm-auto">
      <i class="fa-solid fa-eye me-1"></i> Ver
    </a>

    <a href="{{ route('contratos.edit', $c) }}"
       class="btn btn-sm btn-outline-primary w-100 w-sm-auto">
      <i class="fa-solid fa-pen me-1"></i> Editar
    </a>

    <form action="{{ route('contratos.destroy', $c) }}"
          method="POST"
          class="w-100 w-sm-auto m-0"
          onsubmit="return confirm('¿Eliminar el contrato #{{ $c->nro_contrato }}?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-outline-danger w-100 w-sm-auto">
        <i class="fa-solid fa-trash me-1"></i> Eliminar
      </button>
    </form>
  </div>
</td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted py-4">
                  No hay contratos para mostrar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
          Mostrando {{ $contratos->firstItem() ?? 0 }}–{{ $contratos->lastItem() ?? 0 }} de {{ $contratos->total() }}
        </div>
        <div>
          {{ $contratos->links() }}
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
