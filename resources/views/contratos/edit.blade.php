@extends('layouts.app')

@section('title', 'Editar Contrato')

@section('content')
<div class="container-fluid container-lg py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Contrato #{{ $contrato->nro_contrato }}
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-eye me-1"></i> Ver
        </a>
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

      <form id="contrato-form" method="POST" action="{{ route('contratos.update', $contrato) }}">
        @csrf
        @method('PUT')

        @include('contratos._form', ['contrato' => $contrato])

        <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid fa-check me-1"></i> Guardar cambios
          </button>
          <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-outline-secondary btn-mat">
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
