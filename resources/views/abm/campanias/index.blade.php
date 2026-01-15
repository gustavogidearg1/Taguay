@extends('layouts.app')

@section('title', 'Campañas')

@section('content')
<div class="container py-3">

  {{-- FORM (CREATE / EDIT) --}}
  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-calendar-days me-2"></i> Campañas
      </h3>

      <div class="ms-auto">
        @if($campaniaEdit)
          <a href="{{ route('campanias.index') }}" class="btn btn-light btn-mat">
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
            action="{{ $campaniaEdit ? route('campanias.update', $campaniaEdit) : route('campanias.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($campaniaEdit) @method('PUT') @endif

        <div class="col-12 col-md-5">
          <label class="form-label">Campaña *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="20"
                 required
                 value="{{ old('name', $campaniaEdit->name ?? '') }}"
                 placeholder="Ej: 25/26">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">CodFinneg *</label>
          <input type="text"
                 name="codfinneg"
                 class="form-control"
                 maxlength="10"
                 required
                 value="{{ old('codfinneg', $campaniaEdit->codfinneg ?? '') }}"
                 placeholder="Ej: 25">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label d-block">Activo</label>
          <div class="form-check mt-2">
            <input class="form-check-input"
                   type="checkbox"
                   id="activo"
                   name="activo"
                   value="1"
                   @checked(
                      old(
                        'activo',
                        $campaniaEdit
                          ? (bool) $campaniaEdit->activo // EDIT
                          : true                          // CREATE: por defecto activo
                      )
                   )>
            <label class="form-check-label" for="activo">Sí</label>
          </div>
        </div>

        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $campaniaEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $campaniaEdit ? 'Guardar' : 'Agregar' }}
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
              <th>Campaña</th>
              <th>CodFinneg</th>
              <th>Activo</th>
              <th class="text-end" style="width:190px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($campanias as $c)
              <tr>
                <td class="text-muted">{{ $c->id }}</td>
                <td class="fw-semibold">{{ $c->name }}</td>
                <td><span class="badge text-bg-light border">{{ $c->codfinneg }}</span></td>
                <td>
                  @if($c->activo)
                    <span class="badge text-bg-success">Sí</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('campanias.index', ['edit' => $c->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('campanias.destroy', $c) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar campaña {{ $c->name }}?');">
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
                  No hay campañas cargadas.
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
