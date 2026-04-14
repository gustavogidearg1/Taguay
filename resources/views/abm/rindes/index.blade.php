@extends('layouts.app')

@section('title', 'Rindes')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-chart-column me-2"></i> Rindes
                </h3>

                <div class="ms-auto">
                    <a href="{{ route('rindes.create') }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-plus me-1"></i> Nuevo
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('rindes.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label">Lote</label>
                    <select name="lote_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}" {{ (string)$loteId === (string)$lote->id ? 'selected' : '' }}>
                                {{ $lote->nombre }} - {{ $lote->establecimiento->nombre ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Obs, lote, establecimiento...">
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('rindes.index') }}" class="btn btn-outline-secondary btn-mat">Limpiar</a>
                    <button type="submit" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Lote</th>
                            <th>Establecimiento</th>
                            <th>Campaña</th>
                            <th>Cultivo</th>
                            <th class="text-end">Rinde</th>
                            <th class="text-end">Humedad</th>
                            <th class="text-end">Sup. Cosechada</th>
                            <th>Usuario</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $item)
                            <tr>
                                <td>{{ optional($item->fecha)->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $item->lote->nombre ?? '-' }}</td>
                                <td>{{ $item->lote->establecimiento->nombre ?? '-' }}</td>
                                <td>{{ $item->lote->campania->name ?? '-' }}</td>
                                <td>{{ $item->lote->cultivo->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->rinde, 2, ',', '.') }}</td>
                                <td class="text-end">{{ $item->humedad !== null ? number_format($item->humedad, 2, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $item->superficie_cosechada !== null ? number_format($item->superficie_cosechada, 2, ',', '.') : '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('rindes.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('rindes.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('rindes.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar este rinde?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @if($item->observacion)
                                <tr>
                                    <td></td>
                                    <td colspan="9" class="text-muted small">
                                        <strong>Observación:</strong> {{ $item->observacion }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No hay rindes cargados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none">
                <div class="row g-3">
                    @forelse($registros as $item)
                        <div class="col-12">
                            <div class="border rounded-4 p-3 bg-white shadow-sm">
                                <div class="fw-bold">{{ $item->lote->nombre ?? '-' }}</div>
                                <div class="small text-muted">{{ $item->lote->establecimiento->nombre ?? '-' }}</div>

                                <div class="mt-2 small">
                                    <div><strong>Fecha:</strong> {{ optional($item->fecha)->format('d/m/Y') }}</div>
                                    <div><strong>Campaña:</strong> {{ $item->lote->campania->name ?? '-' }}</div>
                                    <div><strong>Cultivo:</strong> {{ $item->lote->cultivo->name ?? '-' }}</div>
                                    <div><strong>Rinde:</strong> {{ number_format($item->rinde, 2, ',', '.') }} qq/ha</div>
                                    <div><strong>Humedad:</strong> {{ $item->humedad !== null ? number_format($item->humedad, 2, ',', '.') . ' %' : '-' }}</div>
                                    <div><strong>Sup. cosechada:</strong> {{ $item->superficie_cosechada !== null ? number_format($item->superficie_cosechada, 2, ',', '.') . ' ha' : '-' }}</div>
                                    <div><strong>Usuario:</strong> {{ $item->user->name ?? '-' }}</div>
                                    @if($item->observacion)
                                        <div><strong>Obs.:</strong> {{ $item->observacion }}</div>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <a href="{{ route('rindes.show', $item) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('rindes.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                    <form action="{{ route('rindes.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar este rinde?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border text-center mb-0">
                                No hay rindes cargados.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{ $registros->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
