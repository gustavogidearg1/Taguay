@extends('layouts.app')

@section('title', 'Detalle Producto')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-eye me-2"></i> Detalle Producto
      </h3>
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-primary btn-mat">
          <i class="fa-solid fa-pen me-1"></i> Editar
        </a>
        <a href="{{ route('productos.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="text-muted small">Nombre</div>
          <div class="fw-semibold">{{ $producto->name }}</div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Código</div>
          <div><span class="badge text-bg-light border">{{ $producto->codigo }}</span></div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Unidad</div>
          <div class="fw-semibold">{{ $producto->unidad?->name }} ({{ $producto->unidad?->corta ?? $producto->unidad?->codigo }})</div>
        </div>

        <div class="col-12 col-md-4">
          <div class="text-muted small">Tipo</div>
          <div class="fw-semibold">{{ $producto->tipoProducto?->name }}</div>
        </div>

        <div class="col-12 col-md-8">
          <div class="text-muted small">Flags</div>
          <div class="d-flex gap-2 flex-wrap">
            @if($producto->activo) <span class="badge text-bg-success">Activo</span> @else <span class="badge text-bg-secondary">Inactivo</span> @endif
            @if($producto->stock)  <span class="badge text-bg-info">Stock</span> @endif
            @if($producto->vende)  <span class="badge text-bg-primary">Vende</span> @endif
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Mínimo</div>
          <div class="fw-semibold">{{ $producto->minimo ?? '—' }}</div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Máximo</div>
          <div class="fw-semibold">{{ $producto->maximo ?? '—' }}</div>
        </div>

        <div class="col-12">
          <div class="text-muted small">Observación</div>
          <div>{{ $producto->obser ?: '—' }}</div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
