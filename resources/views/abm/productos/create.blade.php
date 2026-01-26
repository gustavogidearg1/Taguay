@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Producto
      </h3>
      <div class="ms-auto">
        <a href="{{ route('productos.index') }}" class="btn btn-light btn-mat">
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

      <form method="POST" action="{{ route('productos.store') }}">
        @csrf

        @include('abm.productos._form', ['producto' => null])

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid fa-check me-1"></i> Guardar
          </button>
          <a href="{{ route('productos.index') }}" class="btn btn-light btn-mat">Cancelar</a>
        </div>
      </form>

    </div>
  </div>

</div>
@endsection
