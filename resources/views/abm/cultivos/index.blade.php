@extends('layouts.app')

@section('title', 'Cultivos')

@section('content')
<div class="container py-3">

  {{-- CARD: FORM (CREATE / EDIT) --}}
  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-seedling me-2"></i> Cultivos
      </h3>

      <div class="ms-auto">
        @if($cultivoEdit)
          <a href="{{ route('cultivos.index') }}" class="btn btn-light btn-mat">
            <i class="fa-solid fa-xmark me-1"></i> Cancelar edición
          </a>
        @endif
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
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
            action="{{ $cultivoEdit ? route('cultivos.update', $cultivoEdit) : route('cultivos.store') }}"
            class="row g-3 align-items-end">
        @csrf
        @if($cultivoEdit) @method('PUT') @endif

        <div class="col-12 col-md-6">
          <label class="form-label">Nombre *</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 maxlength="150"
                 required
                 value="{{ old('name', $cultivoEdit->name ?? '') }}"
                 placeholder="Ej: Trigo Pan">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label">CodFinneg *</label>
          <input type="text"
                 name="codfinneg"
                 class="form-control text-uppercase"
                 maxlength="30"
                 required
                 value="{{ old('codfinneg', $cultivoEdit->codfinneg ?? '') }}"
                 placeholder="Ej: TP">
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label d-block">Filtro Power BI</label>

          <div class="form-check mt-2">
            <input class="form-check-input"
                   type="checkbox"
                   id="filtro_power_bi"
                   name="filtro_power_bi"
                   value="1"
                   @checked(
                      old(
                        'filtro_power_bi',
                        $cultivoEdit
                          ? (bool) $cultivoEdit->filtro_power_bi // EDIT
                          : true                                  // CREATE: por defecto tildado
                      )
                   )>
            <label class="form-check-label" for="filtro_power_bi">
              Sí
            </label>
          </div>

          <div class="form-text text-muted">
            Si está marcado, se incluye en Power BI.
          </div>
        </div>

        <div class="col-12 d-grid d-md-flex gap-2 mt-2">
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="fa-solid {{ $cultivoEdit ? 'fa-check' : 'fa-plus' }} me-1"></i>
            {{ $cultivoEdit ? 'Guardar cambios' : 'Agregar' }}
          </button>

          @if(!$cultivoEdit)
            <button type="reset" class="btn btn-outline-secondary btn-mat">
              Limpiar
            </button>
          @endif
        </div>
      </form>

    </div>
  </div>

  {{-- CARD: TABLA --}}
  <div class="card mat-card">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th>CodFinneg</th>
              <th>Power BI</th>
              <th class="text-end" style="width: 190px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cultivos as $c)
              <tr>
                <td class="text-muted">{{ $c->id }}</td>
                <td class="fw-semibold">{{ $c->name }}</td>
                <td>
                  <span class="badge text-bg-light border">{{ $c->codfinneg }}</span>
                </td>
                <td>
                  @if($c->filtro_power_bi)
                    <span class="badge text-bg-success">Sí</span>
                  @else
                    <span class="badge text-bg-secondary">No</span>
                  @endif
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ route('cultivos.index', ['edit' => $c->id]) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>

                  <form action="{{ route('cultivos.destroy', $c) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('¿Eliminar cultivo {{ $c->name }}?');">
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
                  No hay cultivos cargados.
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
