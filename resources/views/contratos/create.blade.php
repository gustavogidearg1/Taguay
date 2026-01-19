@extends('layouts.app')

@section('title', 'Nuevo Contrato')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-file-circle-plus me-2"></i> Nuevo Contrato
      </h3>

      <div class="ms-auto">
        <a href="{{ route('contratos.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">

      @if ($errors->any())
        <div class="alert alert-danger">
          <div class="fw-semibold mb-1">Revisá los campos:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form id="contrato-form" method="POST" action="{{ route('contratos.store') }}">
        @csrf

        @include('contratos._form', ['contrato' => null])

        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid fa-check me-1"></i> Guardar
          </button>
          <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary btn-mat">
            Cancelar
          </a>
        </div>
      </form>

    </div>
  </div>

</div>

@include('contratos._modal_buscar_cliente')
@include('contratos._modal_cargando')

@endsection
