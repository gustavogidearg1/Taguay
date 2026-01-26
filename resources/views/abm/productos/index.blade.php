@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container py-3">

  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center flex-wrap gap-2">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-boxes-stacked me-2"></i> Productos
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-plus me-1"></i> Nuevo
        </a>
      </div>
    </div>

    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif

      <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-6">
          <input type="text" name="q" class="form-control" value="{{ $q }}"
                 placeholder="Buscar por nombre o código...">
        </div>
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-outline-primary btn-mat" type="submit">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
          </button>
        </div>
        <div class="col-12 col-md-2 d-grid">
          <a href="{{ route('productos.index') }}" class="btn btn-light btn-mat">
            Limpiar
          </a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th>Código</th>
              <th>Unidad</th>
              <th>Tipo</th>
              <th>Flags</th>
              <th class="text-end" style="width:230px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($productos as $p)
              <tr>
                <td class="text-muted">{{ $p->id }}</td>
                <td class="fw-semibold">{{ $p->name }}</td>
                <td><span class="badge text-bg-light border">{{ $p->codigo }}</span></td>
                <td>{{ $p->unidad?->corta ?? $p->unidad?->name }}</td>
                <td>{{ $p->tipoProducto?->name }}</td>
                <td>
                  @if($p->activo) <span class="badge text-bg-success">Activo</span> @else <span class="badge text-bg-secondary">Inactivo</span> @endif
                  @if($p->stock)  <span class="badge text-bg-info">Stock</span> @endif
                  @if($p->vende)  <span class="badge text-bg-primary">Vende</span> @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('productos.show', $p) }}">
                    <i class="fa-solid fa-eye me-1"></i> Ver
                  </a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('productos.edit', $p) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('productos.destroy', $p) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar producto {{ $p->name }}?');">
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
                <td colspan="7" class="text-center text-muted py-4">
                  No hay productos cargados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $productos->links() }}
      </div>

    </div>
  </div>

</div>
@endsection
