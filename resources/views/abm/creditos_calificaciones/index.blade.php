@extends('layouts.app')

@section('title','Calificaciones de Crédito')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center flex-wrap gap-2">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-chart-line me-2"></i> Calificaciones de Crédito
      </h3>

      <div class="ms-auto d-flex gap-2">
        <a href="{{ route('creditos-calificaciones.create') }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-plus me-1"></i> Nueva
        </a>
      </div>
    </div>

    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif

      <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-6">
          <input type="text" name="q" class="form-control"
                 value="{{ $q }}" placeholder="Buscar por tipo de crédito o financiera...">
        </div>
        <div class="col-6 col-md-2 d-grid">
          <button class="btn btn-outline-secondary btn-mat">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
          </button>
        </div>
        <div class="col-6 col-md-2 d-grid">
          <a href="{{ route('creditos-calificaciones.index') }}" class="btn btn-light btn-mat">
            <i class="fa-solid fa-rotate-left me-1"></i> Limpiar
          </a>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Financiera</th>
              <th>Tipo crédito</th>
              <th style="width:140px;">Fecha</th>
              <th style="width:140px;">Venc.</th>
              <th class="text-end">Disp. $</th>
              <th class="text-end">Disp. USD</th>
              <th class="text-end" style="width:220px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $it)
              <tr>
                <td class="text-muted">{{ $it->id }}</td>
                <td class="fw-semibold">{{ $it->financiera->name ?? '—' }}</td>
                <td>{{ $it->tipo_credito ?? '—' }}</td>
                <td class="text-muted">{{ $it->fecha ? \Carbon\Carbon::parse($it->fecha)->format('d/m/Y') : '—' }}</td>
                <td class="text-muted">{{ $it->vencimiento ? \Carbon\Carbon::parse($it->vencimiento)->format('d/m/Y') : '—' }}</td>
                <td class="text-end">{{ $it->disp_total_pesos !== null ? number_format($it->disp_total_pesos, 2, ',', '.') : '—' }}</td>
                <td class="text-end">{{ $it->disp_total_usd !== null ? number_format($it->disp_total_usd, 2, ',', '.') : '—' }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('creditos-calificaciones.show', $it) }}">
                    <i class="fa-solid fa-eye me-1"></i> Ver
                  </a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('creditos-calificaciones.edit', $it) }}">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                  </a>
                  <form action="{{ route('creditos-calificaciones.destroy', $it) }}"
                        method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar la calificación #{{ $it->id }}?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No hay registros.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $items->links() }}
      </div>
    </div>
  </div>

</div>
@endsection
