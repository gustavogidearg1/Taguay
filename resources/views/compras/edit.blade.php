@extends('layouts.app')

@section('title','Editar Compra')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-pen me-2"></i> Editar Compra
        <span class="text-muted ms-2">#{{ $compra->id }}</span>
      </h3>
      <div class="ms-auto">
        <a href="{{ route('compras.show', $compra) }}" class="btn btn-light btn-mat">
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

      <form method="POST" action="{{ route('compras.update', $compra) }}">
        @csrf
        @method('PUT')

        @include('compras._form', ['compra' => $compra])

        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid fa-check me-1"></i> Guardar cambios
          </button>
          <a href="{{ route('compras.show', $compra) }}" class="btn btn-light btn-mat">Cancelar</a>
        </div>
      </form>

      @include('compras._modal_cargando')

    </div>
  </div>

</div>
@endsection
