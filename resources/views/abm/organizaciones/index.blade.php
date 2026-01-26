{{-- resources/views/abm/organizaciones/index.blade.php --}}
@extends('layouts.app')

@section('title','Organizaciones')

@push('styles')
<style>
  .card.mat-card{ border:0; border-radius:16px; box-shadow:0 10px 30px rgba(15,23,42,.08); }
  .mat-header{ display:flex; align-items:center; gap:.75rem; padding:.9rem 1.1rem; border-bottom:1px solid rgba(15,23,42,.06); }
  .mat-title{ margin:0; font-weight:700; font-size:1.05rem; }
  .mat-header-actions{ margin-left:auto; display:flex; gap:.5rem; align-items:center; }
  .btn-mat{ border-radius:10px; padding:.45rem .75rem; }
  .mat-subtle{ color:#64748b; }
  .badge-soft{ font-weight:600; }
</style>
@endpush

@section('content')

{{-- Header simple (sin x-page-header) --}}
<div class="container mb-3">
  <div class="d-flex align-items-center gap-2">
    <i class="bi bi-diagram-3 fs-4 text-primary"></i>
    <h1 class="h4 mb-0">Organizaciones</h1>
  </div>
</div>

{{-- Flash / Errores (sin x-flash) --}}
<div class="container">
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
      <i class="bi bi-check-circle"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="bi bi-x-circle"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Revisá los campos:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif
</div>

<div class="container">
  <div class="card mat-card mb-3">
    <div class="mat-header">
      <h3 class="mat-title">Listado</h3>

      <div class="mat-header-actions ms-auto">
        <a href="{{ route('organizaciones.create') }}" class="btn btn-primary btn-mat">
          <i class="bi bi-plus-lg"></i> Nueva
        </a>
      </div>
    </div>

    <div class="card-body">

      {{-- Filtros --}}
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-6">
          <label class="form-label mb-1">Organización</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              class="form-control"
              placeholder="Buscar por nombre o código..."
            >
          </div>

        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Activo</label>
          <select name="activo" class="form-select">
            <option value="" @selected(request('activo')==='')>Todos</option>
            <option value="1" @selected(request('activo')==='1')>Activos</option>
            <option value="0" @selected(request('activo')==='0')>Inactivos</option>
          </select>
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary btn-mat w-100" type="submit">
            <i class="bi bi-funnel"></i> Filtrar
          </button>
          <a class="btn btn-outline-secondary btn-mat" href="{{ route('organizaciones.index') }}" title="Limpiar">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </form>

      {{-- Tabla --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width: 120px;">Código</th>
              <th>Nombre</th>
              <th style="width: 140px;">Fecha</th>
              <th style="width: 120px;">Estado</th>
              <th class="text-end" style="width: 190px;">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($organizaciones as $o)
              <tr>
                <td><span class="fw-semibold">{{ $o->codigo }}</span></td>

                <td>
                  <div class="fw-semibold">{{ $o->name }}</div>
                  @if($o->descripcion)
                    <div class="small text-muted text-truncate" style="max-width: 520px;">
                      {{ $o->descripcion }}
                    </div>
                  @endif
                </td>

                <td>{{ optional($o->fecha)->format('d/m/Y') }}</td>

                <td>
                  @if($o->activo)
                    <span class="badge text-bg-success badge-soft">
                      <i class="bi bi-check2-circle"></i> Activo
                    </span>
                  @else
                    <span class="badge text-bg-secondary badge-soft">
                      <i class="bi bi-slash-circle"></i> Inactivo
                    </span>
                  @endif
                </td>

                <td class="text-end">
                  <div class="btn-group" role="group">
                    <a href="{{ route('organizaciones.show',$o) }}" class="btn btn-outline-secondary btn-sm" title="Ver">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('organizaciones.edit',$o) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('organizaciones.destroy',$o) }}" class="d-inline"
                          onsubmit="return confirm('¿Eliminar organización {{ $o->name }}?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" type="submit" title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                  No hay organizaciones con esos filtros.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
          Mostrando
          <b>{{ $organizaciones->firstItem() ?? 0 }}</b>
          a
          <b>{{ $organizaciones->lastItem() ?? 0 }}</b>
          de
          <b>{{ $organizaciones->total() }}</b>
        </div>

        <div>
          {{ $organizaciones->links() }}
        </div>
      </div>

    </div>
  </div>
</div>

@endsection
