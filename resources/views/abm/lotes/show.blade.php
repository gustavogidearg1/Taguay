@extends('layouts.app')

@section('title', 'Detalle de lote')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-draw-polygon me-2"></i> Detalle de lote
                </h3>

                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('lotes.edit', $lote) }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-pen me-1"></i> Editar
                    </a>
                    <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary btn-mat">
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Nombre</div>
                        <div class="v">{{ $lote->nombre }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Establecimiento</div>
                        <div class="v">{{ $lote->establecimiento->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Campaña</div>
                        <div class="v">{{ $lote->campania->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Cultivo</div>
                        <div class="v">{{ $lote->cultivo->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Hectáreas</div>
                        <div class="v">{{ number_format($lote->hectareas, 2, ',', '.') }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Creado</div>
                        <div class="v">{{ optional($lote->created_at)->format('d/m/Y H:i') ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="kv">
                        <div class="k">Ubicación de referencia</div>
                        <div class="v">{{ $lote->ubicacion_referencia ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="kv">
                        <div class="k">Latitud</div>
                        <div class="v">{{ $lote->latitud ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="kv">
                        <div class="k">Longitud</div>
                        <div class="v">{{ $lote->longitud ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2">
                        @if($lote->google_maps_url)
                            <a href="{{ $lote->google_maps_url }}" target="_blank" class="btn btn-outline-secondary btn-mat">
                                <i class="fa-solid fa-map-location-dot me-1"></i> Ver mapa
                            </a>
                        @endif

                        @if($lote->google_maps_directions_url)
                            <a href="{{ $lote->google_maps_directions_url }}" target="_blank" class="btn btn-success btn-mat">
                                <i class="fa-solid fa-route me-1"></i> Cómo llegar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
