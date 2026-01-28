{{-- resources/views/compras/index.blade.php --}}
@extends('layouts.app')

@section('title','Compras')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  @php
    // Filtros separados
    $idFilter   = $idFilter   ?? request('id','');
    $orgFilter  = $orgFilter  ?? request('org','');
    $campFilter = $campFilter ?? request('campania_id','');
      $prodFilter = $prodFilter ?? request('producto','');

    // Paginación / orden
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
  @endphp

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-cart-shopping me-2"></i> Compras
      </h3>

      <div class="ms-auto d-flex gap-2 flex-wrap">
        {{-- Exportar respetando filtros/sort --}}
        @if(Route::has('compras.export.excel'))
          <a href="{{ route('compras.export.excel', request()->query()) }}" class="btn btn-outline-success btn-mat">
            <i class="fa-solid fa-file-excel me-1"></i> Excel
          </a>
        @endif

        @if(Route::has('compras.export.pdf'))
          <a href="{{ route('compras.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-mat">
            <i class="fa-solid fa-file-pdf me-1"></i> PDF
          </a>
        @endif

        <a href="{{ route('compras.create') }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-plus me-1"></i> Nueva
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- Filtros (separados) --}}
      <form method="GET" action="{{ route('compras.index') }}" class="row g-2 align-items-end mb-3">
<div class="row g-2 align-items-end">

  <div class="col-12 col-sm-6 col-md-2">
    <label class="form-label mb-1">OC</label>
    <input type="text" name="id" class="form-control" value="{{ $idFilter }}" placeholder="Ej: 123">
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <label class="form-label mb-1">Organización</label>
    <input type="text" name="org" class="form-control" value="{{ $orgFilter }}" placeholder="Nombre...">
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <label class="form-label mb-1">Producto</label>
    <input type="text" name="producto" class="form-control" value="{{ $prodFilter }}" placeholder="Nombre del producto...">
  </div>

  <div class="col-12 col-sm-6 col-md-2">
    <label class="form-label mb-1">Campaña</label>
    <select name="campania_id" class="form-select">
      <option value="">Todas</option>
      @foreach(($campanias ?? collect()) as $ca)
        <option value="{{ $ca->id }}" @selected((string)$campFilter === (string)$ca->id)>{{ $ca->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-sm-6 col-md-2">
    <label class="form-label mb-1">Por página</label>
    <select name="per_page" class="form-select">
      @foreach([10,15,20,50,100] as $n)
        <option value="{{ $n }}" @selected((int)$perPage === $n)>{{ $n }}</option>
      @endforeach
    </select>
  </div>

</div>



        {{-- Mantener sort/dir --}}
        <input type="hidden" name="sort" value="{{ $sortNow }}">
        <input type="hidden" name="dir" value="{{ $dirNow }}">

        <div class="col-12 d-grid d-md-flex gap-2 mt-1">
          <button class="btn btn-dark">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
          </button>

          <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
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
                <a href="{{ $sortUrl('id') }}" class="text-decoration-none text-dark">
                  Oc <i class="{{ $sortIcon('id') }} ms-1"></i>
                </a>
              </th>

              <th style="width:120px;">
                <a href="{{ $sortUrl('fecha') }}" class="text-decoration-none text-dark">
                  Fecha <i class="{{ $sortIcon('fecha') }} ms-1"></i>
                </a>
              </th>

              <th style="min-width:240px;">
                <a href="{{ $sortUrl('organizacion') }}" class="text-decoration-none text-dark">
                  Organización <i class="{{ $sortIcon('organizacion') }} ms-1"></i>
                </a>
              </th>

              <th style="width:160px;">
                <a href="{{ $sortUrl('campania') }}" class="text-decoration-none text-dark">
                  Campaña <i class="{{ $sortIcon('campania') }} ms-1"></i>
                </a>
              </th>

              <th style="width:140px;">
                <a href="{{ $sortUrl('moneda') }}" class="text-decoration-none text-dark">
                  Moneda <i class="{{ $sortIcon('moneda') }} ms-1"></i>
                </a>
              </th>

              <th style="min-width:280px;">Productos</th>

              <th style="width:110px;">
                <a href="{{ $sortUrl('activo') }}" class="text-decoration-none text-dark">
                  Activo <i class="{{ $sortIcon('activo') }} ms-1"></i>
                </a>
              </th>

              <th style="width:320px; white-space:nowrap;" class="text-end">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($compras as $c)
              <tr>
                <td class="text-muted">{{ $c->id }}</td>

                <td>{{ optional($c->fecha)->format('d/m/Y') ?? '—' }}</td>

                <td>
                  <div class="fw-semibold">{{ $c->organizacion?->name ?? '—' }}</div>
                </td>

                <td>{{ $c->campania?->name ?? '—' }}</td>

                <td>{{ $c->moneda?->name ?? '—' }}</td>

                <td>
                  @php
                    $names = ($c->subCompras ?? collect())
                      ->pluck('producto.name')
                      ->filter()
                      ->unique()
                      ->values();
                    $first = $names->take(3);
                    $rest  = $names->count() - $first->count();
                  @endphp

                  @if($names->isEmpty())
                    <span class="text-muted">—</span>
                  @else
                    <div class="text-truncate" style="max-width: 520px;">
                      {{ $first->implode(', ') }}
                      @if($rest > 0)
                        <span class="badge text-bg-light border ms-1">+{{ $rest }}</span>
                      @endif
                    </div>
                  @endif
                </td>

                <td>
                  @if($c->activo)
                    <span class="badge text-bg-success">Sí</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>

                <td class="text-end">
                  <div class="d-grid d-sm-flex justify-content-end gap-1 flex-wrap flex-sm-nowrap">
                    <a href="{{ route('compras.show', $c) }}"
                       class="btn btn-sm btn-outline-secondary w-100 w-sm-auto">
                      <i class="fa-solid fa-eye me-1"></i> Ver
                    </a>

                    <a href="{{ route('compras.edit', $c) }}"
                       class="btn btn-sm btn-outline-primary w-100 w-sm-auto">
                      <i class="fa-solid fa-pen me-1"></i> Editar
                    </a>

                    <form action="{{ route('compras.destroy', $c) }}"
                          method="POST"
                          class="w-100 w-sm-auto m-0"
                          onsubmit="return confirm('¿Eliminar compra #{{ $c->id }}?');">
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
                <td colspan="8" class="text-center text-muted py-4">
                  No hay compras para mostrar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
          Mostrando {{ $compras->firstItem() ?? 0 }}–{{ $compras->lastItem() ?? 0 }} de {{ $compras->total() }}
        </div>
        <div>
          {{ $compras->links() }}
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
