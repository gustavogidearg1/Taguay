@extends('layouts.app')

@section('title', 'Editar Establecimiento')

@section('content')
<div class="container py-3">

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Establecimiento
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('establecimientos.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>

        {{-- Eliminar: FORM SEPARADO (sin anidar) --}}
        <form action="{{ route('establecimientos.destroy', $establecimiento) }}"
              method="POST"
              onsubmit="return confirm('¿Eliminar establecimiento?');"
              class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-mat">
            <i class="fa-solid fa-trash me-1"></i> Eliminar
          </button>
        </form>
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

      {{-- ACTUALIZAR: UN SOLO FORM --}}
      <form method="POST" action="{{ route('establecimientos.update', $establecimiento) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre"
                   value="{{ old('nombre', $establecimiento->nombre) }}"
                   class="form-control" required maxlength="255">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Ubicación</label>
            <input type="text" name="ubicacion"
                   value="{{ old('ubicacion', $establecimiento->ubicacion) }}"
                   class="form-control" maxlength="255">
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary btn-mat">
            <i class="fa-solid fa-check me-1"></i> Guardar cambios
          </button>

          <a href="{{ route('establecimientos.index') }}" class="btn btn-outline-secondary btn-mat">
            Cancelar
          </a>
        </div>
      </form>

    </div>
  </div>

</div>
@endsection
