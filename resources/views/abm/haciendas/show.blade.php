@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Hacienda #{{ $hacienda->id }}</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-primary" href="{{ route('haciendas.edit', $hacienda->id) }}">Editar</a>
      <a class="btn btn-secondary" href="{{ route('haciendas.index') }}">Volver</a>
    </div>
  </div>

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Datos principales</h5>
          <p class="mb-1"><strong>Cliente:</strong> {{ $hacienda->cliente }}</p>
          <p class="mb-1"><strong>Consignatario:</strong> {{ $hacienda->consignatario ?: '—' }}</p>
          <p class="mb-1"><strong>Vendedor:</strong> {{ $hacienda->vendedor ?: '—' }}</p>
          <p class="mb-1"><strong>Categoría:</strong> {{ $hacienda->categoria?->nombre }}</p>
          <p class="mb-1"><strong>Cantidad:</strong> {{ number_format($hacienda->cantidad, 1, ',', '.') }}</p>
          <p class="mb-1"><strong>Transportista:</strong> {{ $hacienda->transportista ?: '—' }}</p>
          <p class="mb-1"><strong>Patente:</strong> {{ $hacienda->patente ?: '—' }}</p>
          <p class="mb-1"><strong>Establecimiento:</strong> {{ $hacienda->establecimiento?->nombre }} @if($hacienda->establecimiento?->ubicacion) — {{ $hacienda->establecimiento->ubicacion }} @endif</p>
          <p class="mb-1"><strong>Destino:</strong> {{ $hacienda->destino ?: '—' }}</p>
          <p class="mb-0"><strong>Peso vivo (-8%):</strong> {{ $hacienda->peso_vivo_menos_8 !== null ? number_format($hacienda->peso_vivo_menos_8, 1, ',', '.') : '—' }}</p>
          
          <p class="mb-0">
  <strong>Subtotal (Peso vivo x Cantidad):</strong>
  {{ number_format($hacienda->subtotal_peso_vivo, 1, ',', '.') }}
</p>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
