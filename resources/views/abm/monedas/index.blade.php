@extends('layouts.app')

@section('title', 'Monedas')

@section('content')
<div class="container py-3">

  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-coins me-2"></i> Monedas
      </h3>

      <div class="ms-auto">
        @if($monedaEdit)
          <a href="{{ route('monedas.index') }}" class="btn btn-light btn-mat">
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

      {{-- FORM: create o edit --}}
      <form method="POST"
            action="{{ $monedaEdit ? route('monedas.update', $monedaEdit) : route('monedas.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($monedaEdit) @method('PUT') @endif

        <div class="col-12 col-md-6">
          <label class="form-label">Nombre *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="100"
                 required
                 value="{{ old('name', $monedaEdit->name ?? '') }}"
                 placeholder="Ej: Dólar">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">CodFinne *</label>
          <input type="text"
                 name="codfinne"
                 class="form-control text-uppercase"
                 maxlength="20"
                 required
                 value="{{ old('codfinne', $monedaEdit->codfinne ?? '') }}"
                 placeholder="Ej: DOL">
        </div>

        <div class="col-12 col-md-3 d-grid">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $monedaEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $monedaEdit ? 'Guardar cambios' : 'Agregar' }}
          </button>
        </div>
      </form>

    </div>
  </div>

  <div class="card mat-card">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th>CodFinne</th>
              <th class="text-end" style="width: 180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($monedas as $m)
              <tr>
                <td class="text-muted">{{ $m->id }}</td>
                <td class="fw-semibold">{{ $m->name }}</td>
                <td><span class="badge text-bg-light border">{{ $m->codfinne }}</span></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('monedas.index', ['edit' => $m->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('monedas.destroy', $m) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar moneda {{ $m->name }}?');">
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
                <td colspan="4" class="text-center text-muted py-4">No hay monedas cargadas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>
@endsection
