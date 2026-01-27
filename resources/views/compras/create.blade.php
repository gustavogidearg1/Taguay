@extends('layouts.app')

@section('title', 'Nueva Compra')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center flex-wrap">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-cart-shopping me-2"></i> Nueva Compra
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('compras.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">

      @if ($errors->any())
        <div class="alert alert-danger mb-3">
          <div class="fw-semibold mb-1">Revisá los campos:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      {{-- ✅ IMPORTANTE: id="compra-form" --}}
      <form id="compra-form" method="POST" action="{{ route('compras.store') }}">
        @csrf

        {{-- tu formulario grande --}}
        @include('compras._form', ['compra' => null])

        <div class="d-flex gap-2 justify-content-end mt-3">
          <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary btn-mat">
            Cancelar
          </a>
          <button type="submit" class="btn btn-primary btn-mat">
            <i class="fa-solid fa-check me-1"></i> Guardar Compra
          </button>
        </div>
      </form>

    </div>
  </div>

</div>

{{-- ✅ Modal “Cargando / Enviando…” (tipo contrato) --}}
@include('compras._modal_cargando')
@endsection
