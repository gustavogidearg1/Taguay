@extends('layouts.app')

@section('title', 'Unidades')

@section('content')
<div class="container py-3">

  {{-- FORM (CREATE / EDIT) --}}
  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-ruler-combined me-2"></i> Unidades
      </h3>

      <div class="ms-auto">
        @if($unidadEdit)
          <a href="{{ route('unidades.index') }}" class="btn btn-light btn-mat">
            <i class="fa-solid fa-xmark me-1"></i> Cancelar edición
          </a>
        @endif
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger mb-3">
          <div class="fw-semibold mb-1">Revisá los campos:</div>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <form method="POST"
            action="{{ $unidadEdit ? route('unidades.update', $unidadEdit) : route('unidades.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($unidadEdit) @method('PUT') @endif

        <div class="col-12 col-md-5">
          <label class="form-label">Nombre *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="100"
                 required
                 value="{{ old('name', $unidadEdit->name ?? '') }}"
                 placeholder="Ej: Kilogramo">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Código *</label>
          <input type="text"
                 name="codigo"
                 class="form-control"
                 maxlength="50"
                 required
                 value="{{ old('codigo', $unidadEdit->codigo ?? '') }}"
                 placeholder="Ej: K">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label">Corta *</label>
          <input type="text"
                 name="corta"
                 class="form-control"
                 maxlength="50"
                 required
                 value="{{ old('corta', $unidadEdit->corta ?? '') }}"
                 placeholder="Ej: Kg">
        </div>

        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $unidadEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $unidadEdit ? 'Guardar' : 'Agregar' }}
          </button>
        </div>

      </form>

    </div>
  </div>

  {{-- TABLA --}}
  <div class="card mat-card">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th>Código</th>
              <th>Corta</th>
              <th class="text-end" style="width:190px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($unidades as $u)
              <tr>
                <td class="text-muted">{{ $u->id }}</td>
                <td class="fw-semibold">{{ $u->name }}</td>
                <td><span class="badge text-bg-light border">{{ $u->codigo }}</span></td>
                <td><span class="badge text-bg-secondary">{{ $u->corta }}</span></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('unidades.index', ['edit' => $u->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('unidades.destroy', $u) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar unidad {{ $u->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="fa-solid fa-trash me-1"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  No hay unidades cargadas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>
@endsection
