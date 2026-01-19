@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-file-contract me-2"></i> Contratos
      </h3>

      <div class="ms-auto d-flex gap-2">
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
                 value="{{ $q ?? request('q') }}">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label mb-1">Por página</label>
          <select name="per_page" class="form-select">
            @foreach([10,20,50,100] as $n)
              <option value="{{ $n }}" @selected((int)($perPage ?? request('per_page',10)) === $n)>{{ $n }}</option>
            @endforeach
          </select>
        </div>

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
          <thead>
            <tr>
              <th style="width:110px;">Nro</th>
              <th style="width:120px;">Forward</th>
              <th style="width:120px;">Fecha</th>
              <th>Cliente</th>
              <th style="width:140px;">Campaña</th>
              <th style="width:160px;">Cultivo</th>
              <th style="width:220px;" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($contratos as $c)
              <tr>
                <td class="fw-semibold">{{ $c->nro_contrato }}</td>
                <td>{{ $c->num_forward ?? '—' }}</td>
                <td>{{ optional($c->fecha)->format('d/m/Y') }}</td>
                <td>
<div class="fw-semibold">{{ $c->organizacion->name ?? '—' }}</div>
                </td>
                <td>{{ $c->campania->name ?? '—' }}</td>
                <td>{{ $c->cultivo->name ?? '—' }}</td>

                <td class="text-end">
                  <a href="{{ route('contratos.show', $c) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-eye me-1"></i> Ver
                  </a>

                  <a href="{{ route('contratos.edit', $c) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('contratos.destroy', $c) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar el contrato #{{ $c->nro_contrato }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="fa-solid fa-trash me-1"></i> Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
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
