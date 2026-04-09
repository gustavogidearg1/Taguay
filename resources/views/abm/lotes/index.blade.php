@extends('layouts.app')

@section('title', 'Lotes')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-draw-polygon me-2"></i> Lotes
                </h3>

                <div class="ms-auto">
                    <a href="{{ route('lotes.create') }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-plus me-1"></i> Nuevo
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('lotes.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label">Campaña</label>
                    <select name="campania_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($campanias as $campania)
                            <option value="{{ $campania->id }}" {{ (string)$campaniaId === (string)$campania->id ? 'selected' : '' }}>
                                {{ $campania->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Establecimiento</label>
                    <select name="establecimiento_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($establecimientos as $establecimiento)
                            <option value="{{ $establecimiento->id }}" {{ (string)$establecimientoId === (string)$establecimiento->id ? 'selected' : '' }}>
                                {{ $establecimiento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Cultivo</label>
                    <select name="cultivo_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($cultivos as $cultivo)
                            <option value="{{ $cultivo->id }}" {{ (string)$cultivoId === (string)$cultivo->id ? 'selected' : '' }}>
                                {{ $cultivo->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Nombre, ubicación...">
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary btn-mat">
                        Limpiar
                    </a>
                    <button type="submit" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Establecimiento</th>
                            <th>Campaña</th>
                            <th>Cultivo</th>
                            <th class="text-end">Hectáreas</th>
                            <th>Ubicación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lotes as $lote)
                            <tr>
                                <td class="fw-semibold">{{ $lote->nombre }}</td>
                                <td>{{ $lote->establecimiento->nombre ?? '-' }}</td>
                                <td>{{ $lote->campania->name ?? '-' }}</td>
                                <td>{{ $lote->cultivo->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format($lote->hectareas, 2, ',', '.') }}</td>
                                <td>{{ $lote->ubicacion_referencia ?: '-' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        <a href="{{ route('lotes.show', $lote) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        @if($lote->google_maps_url)
                                            <a href="{{ $lote->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-map-location-dot"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('lotes.destroy', $lote) }}" method="POST" onsubmit="return confirm('¿Eliminar este lote?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay lotes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none">
                <div class="row g-3">
                    @forelse($lotes as $lote)
                        <div class="col-12">
                            <div class="border rounded-4 p-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-bold">{{ $lote->nombre }}</div>
                                        <div class="text-muted small">{{ $lote->establecimiento->nombre ?? '-' }}</div>
                                    </div>
                                    <span class="badge badge-soft">{{ $lote->cultivo->name ?? '-' }}</span>
                                </div>

                                <div class="mt-3 small">
                                    <div><strong>Campaña:</strong> {{ $lote->campania->name ?? '-' }}</div>
                                    <div><strong>Hectáreas:</strong> {{ number_format($lote->hectareas, 2, ',', '.') }}</div>
                                    <div><strong>Ubicación:</strong> {{ $lote->ubicacion_referencia ?: '-' }}</div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <a href="{{ route('lotes.show', $lote) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                    @if($lote->google_maps_url)
                                        <a href="{{ $lote->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            Mapa
                                        </a>
                                    @endif

                                    <form action="{{ route('lotes.destroy', $lote) }}" method="POST" onsubmit="return confirm('¿Eliminar este lote?')">
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
                                No hay lotes registrados.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{ $lotes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
