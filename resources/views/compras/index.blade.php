@extends('layouts.app')

@section('title','Compras')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center flex-wrap gap-2">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-cart-shopping me-2"></i> Compras
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('compras.create') }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-plus me-1"></i> Nueva
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif

      <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-6">
          <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}"
                 placeholder="Buscar por organización (nombre organizacion)...">
        </div>
        <div class="col-6 col-md-2 d-grid">
          <button class="btn btn-outline-primary btn-mat" type="submit">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
          </button>
        </div>
        <div class="col-6 col-md-2 d-grid">
          <a class="btn btn-light btn-mat" href="{{ route('compras.index') }}">Limpiar</a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:90px;">ID</th>
              <th>Fecha</th>
              <th>Org.</th>
              <th>Campaña</th>
              <th>Moneda</th>
              <th>Activo</th>
              <th class="text-end" style="width:240px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($compras as $c)
              <tr>
                <td class="text-muted">{{ $c->id }}</td>
                <td>{{ optional($c->fecha)->format('d/m/Y') }}</td>
                <td>{{ $c->organizacion?->name }}</td>
                <td>{{ $c->campania?->name }}</td>
                <td>{{ $c->moneda?->name }}</td>
                <td>
                  @if($c->activo)
                    <span class="badge text-bg-success">Sí</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('compras.show', $c) }}">
                    <i class="fa-solid fa-eye me-1"></i> Ver
                  </a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('compras.edit', $c) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>
                  <form action="{{ route('compras.destroy', $c) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar compra #{{ $c->id }}?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No hay compras.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $compras->links() }}
      </div>

    </div>
  </div>

</div>
@endsection
