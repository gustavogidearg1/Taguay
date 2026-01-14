@extends('layouts.app')

@section('title', 'Nuevo Establecimiento')

@section('content')
<div class="container py-3">

    <div class="card mat-card">
        <div class="card-header mat-header d-flex align-items-center">
            <h3 class="mat-title mb-0">
                <i class="fa-solid fa-building-circle-arrow-right me-2"></i> Nuevo Establecimiento
            </h3>
            <div class="ms-auto">
                <a href="{{ route('establecimientos.index') }}" class="btn btn-light btn-mat">
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

            <form method="POST" action="{{ route('establecimientos.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               class="form-control" required maxlength="255">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="ubicacion" value="{{ old('ubicacion') }}"
                               class="form-control" maxlength="255" placeholder="Ciudad, provincia, etc.">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-check me-1"></i> Guardar
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
