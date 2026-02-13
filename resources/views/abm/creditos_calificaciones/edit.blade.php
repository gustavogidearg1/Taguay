@extends('layouts.app')

@section('title','Editar Calificación')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-pen me-2"></i> Editar Calificación #{{ $item->id }}
      </h3>
      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('creditos-calificaciones.show', $item) }}" class="btn btn-outline-secondary btn-mat">
          <i class="fa-solid fa-eye me-1"></i> Ver
        </a>
        <a href="{{ route('creditos-calificaciones.index') }}" class="btn btn-light btn-mat">
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

      <form method="POST" action="{{ route('creditos-calificaciones.update', $item) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('abm.creditos_calificaciones._form', ['item' => $item])

        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary btn-mat">
            <i class="fa-solid fa-check me-1"></i> Guardar cambios
          </button>
          <a href="{{ route('creditos-calificaciones.show', $item) }}" class="btn btn-light btn-mat">Cancelar</a>
        </div>
      </form>

    </div>
  </div>

</div>
@endsection
