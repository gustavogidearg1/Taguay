{{-- resources/views/abm/financieras/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Financieras')

@section('content')
<div class="container py-3">

  {{-- FORM (CREATE / EDIT) --}}
  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-building-columns me-2"></i> Financieras
      </h3>

      <div class="ms-auto">
        @if($financieraEdit)
          <a href="{{ route('financieras.index') }}" class="btn btn-light btn-mat">
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
            action="{{ $financieraEdit ? route('financieras.update', $financieraEdit) : route('financieras.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($financieraEdit) @method('PUT') @endif

        {{-- Nombre --}}
        <div class="col-12 col-md-4">
          <label class="form-label">Nombre *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="100"
                 required
                 value="{{ old('name', $financieraEdit->name ?? '') }}"
                 placeholder="Ej: Banco X">
        </div>

        {{-- Tipo --}}
        <div class="col-12 col-md-2">
          <label class="form-label">Tipo</label>
          <select name="tipo" class="form-select">
            <option value="" {{ old('tipo', $financieraEdit->tipo ?? '') == '' ? 'selected' : '' }}>Sin tipo</option>
            <option value="Banco" {{ old('tipo', $financieraEdit->tipo ?? '') == 'Banco' ? 'selected' : '' }}>Banco</option>
            <option value="Tarjeta" {{ old('tipo', $financieraEdit->tipo ?? '') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
            <option value="SGR" {{ old('tipo', $financieraEdit->tipo ?? '') == 'SGR' ? 'selected' : '' }}>SGR</option>
          </select>
        </div>

        {{-- Descripción --}}
        <div class="col-12 col-md-4">
          <label class="form-label">Descripción</label>
          <input type="text"
                 name="descripcion"
                 class="form-control"
                 maxlength="100"
                 value="{{ old('descripcion', $financieraEdit->descripcion ?? '') }}"
                 placeholder="Ej: Sucursal / tipo / nota breve">
        </div>

        {{-- Botón --}}
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $financieraEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $financieraEdit ? 'Guardar' : 'Agregar' }}
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
              <th>Tipo</th>
              <th>Descripción</th>
              <th class="text-end" style="width:190px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($financieras as $f)
              <tr>
                <td class="text-muted">{{ $f->id }}</td>
                <td class="fw-semibold">{{ $f->name }}</td>
                <td>
                  @if(!empty($f->tipo))
                    <span class="badge text-bg-light border">{{ $f->tipo }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-muted">{{ $f->descripcion }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('financieras.index', ['edit' => $f->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('financieras.destroy', $f) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar financiera {{ $f->name }}?');">
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
                  No hay financieras cargadas.
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
