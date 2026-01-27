@extends('layouts.app')

@section('title', 'Condiciones de Pago')

@section('content')
<div class="container py-3">

  {{-- FORM (CREATE / EDIT) --}}
  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-hand-holding-dollar me-2"></i> Condiciones de Pago
      </h3>

      <div class="ms-auto">
        @if($condicionEdit)
          <a href="{{ route('condicion-pagos.index') }}" class="btn btn-light btn-mat">
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
            action="{{ $condicionEdit ? route('condicion-pagos.update', $condicionEdit) : route('condicion-pagos.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($condicionEdit) @method('PUT') @endif

        <div class="col-12 col-md-4">
          <label class="form-label">Nombre *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="100"
                 required
                 value="{{ old('name', $condicionEdit->name ?? '') }}"
                 placeholder="Ej: 3 cuotas / 210 días">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">Código *</label>
          <input type="text"
                 name="codigo"
                 class="form-control"
                 maxlength="50"
                 required
                 value="{{ old('codigo', $condicionEdit->codigo ?? '') }}"
                 placeholder="Ej: CP-03-210">
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label">Div. Mes</label>
          <input type="number"
                 name="div_mes"
                 class="form-control"
                 min="0"
                 value="{{ old('div_mes', $condicionEdit->div_mes ?? '') }}"
                 placeholder="Ej: 3">
        </div>

        <div class="col-6 col-md-2">
          <label class="form-label">N° días</label>
          <input type="number"
                 name="num_dias"
                 class="form-control"
                 min="0"
                 value="{{ old('num_dias', $condicionEdit->num_dias ?? '') }}"
                 placeholder="Ej: 210">
        </div>

        <div class="col-12 col-md-1">
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
                       $condicionEdit ? (bool)$condicionEdit->activo : true
                     )
                   )>
            <label class="form-check-label" for="activo">Sí</label>
          </div>
        </div>

        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $condicionEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $condicionEdit ? 'Guardar' : 'Agregar' }}
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
              <th>Div. Mes</th>
              <th>N° días</th>
              <th>Activo</th>
              <th class="text-end" style="width:190px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($condiciones as $c)
              <tr>
                <td class="text-muted">{{ $c->id }}</td>
                <td class="fw-semibold">{{ $c->name }}</td>
                <td><span class="badge text-bg-light border">{{ $c->codigo }}</span></td>
                <td>{{ $c->div_mes ?? '—' }}</td>
                <td>{{ $c->num_dias ?? '—' }}</td>
                <td>
                  @if($c->activo)
                    <span class="badge text-bg-success">Sí</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('condicion-pagos.index', ['edit' => $c->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('condicion-pagos.destroy', $c) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar condición {{ $c->name }}?');">
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
                <td colspan="7" class="text-center text-muted py-4">
                  No hay condiciones cargadas.
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
