@extends('layouts.app')

@section('title', 'Detalle Estado por Lote')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">

        {{-- HEADER --}}
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <span class="d-print-none">
                        <i class="fa-solid fa-list-check me-2"></i>
                    </span>
                    Detalle estado por lote
                </h3>

                <div class="ms-auto d-flex gap-2 no-print">
                    <a href="{{ route('lote-estados.edit', $registro) }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-pen me-1"></i> Editar
                    </a>

                    <button type="button" class="btn btn-outline-dark btn-mat" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Imprimir
                    </button>

                    <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat">
                        Volver
                    </a>
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            <div class="row g-3">

                {{-- GENERALES --}}
                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Fecha</div>
                        <div class="v">{{ optional($registro->fecha)->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Estado</div>
                        <div class="v">{{ $registro->estadoCultivo->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Usuario</div>
                        <div class="v">{{ $registro->user->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Lote</div>
                        <div class="v">{{ $registro->lote->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Establecimiento</div>
                        <div class="v">{{ $registro->lote->establecimiento->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Campaña</div>
                        <div class="v">{{ $registro->lote->campania->name ?? '-' }}</div>
                    </div>
                </div>

                {{-- PRODUCTIVO --}}
                <div class="col-12"><hr></div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Producción (Tn)</div>
                        <div class="v">
                            {{ $registro->produccion_tn !== null ? number_format($registro->produccion_tn, 2, ',', '.') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Superficie (Ha)</div>
                        <div class="v">
                            {{ $registro->superficie_ha !== null ? number_format($registro->superficie_ha, 2, ',', '.') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Rinde</div>
                        <div class="v">
                            {{ $registro->rinde !== null ? number_format($registro->rinde, 2, ',', '.') : '-' }}
                        </div>
                    </div>
                </div>

                {{-- LOGÍSTICA --}}
                <div class="col-12"><hr></div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Chasis</div>
                        <div class="v">{{ $registro->chasis ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Chofer</div>
                        <div class="v">{{ $registro->chofer ?: '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Número / CTG</div>
                        <div class="v">{{ $registro->numero ?: '-' }}</div>
                    </div>
                </div>

                {{-- AMBIENTAL --}}
                <div class="col-12"><hr></div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Lluvia</div>
                        <div class="v">
                            {{ $registro->lluvia !== null ? number_format($registro->lluvia, 2, ',', '.') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Humedad</div>
                        <div class="v">
                            {{ $registro->humedad !== null ? number_format($registro->humedad, 2, ',', '.') : '-' }}
                        </div>
                    </div>
                </div>

                {{-- IMÁGENES --}}
                @if($registro->imagen_1 || $registro->imagen_2)
                    <div class="col-12"><hr></div>

                    @if($registro->imagen_1)
                        <div class="col-12 col-md-6">
                            <img src="{{ asset('storage/' . $registro->imagen_1) }}"
                                 class="img-fluid rounded border">
                        </div>
                    @endif

                    @if($registro->imagen_2)
                        <div class="col-12 col-md-6">
                            <img src="{{ asset('storage/' . $registro->imagen_2) }}"
                                 class="img-fluid rounded border">
                        </div>
                    @endif
                @endif

                {{-- OBSERVACIÓN --}}
                <div class="col-12"><hr></div>

                @if($registro->latitud && $registro->longitud)
    <div class="mb-3">
        <strong>Ubicación GPS:</strong><br>
        Lat: {{ $registro->latitud }}<br>
        Lng: {{ $registro->longitud }}<br>

        @if($registro->google_maps_url)
            <a href="{{ $registro->google_maps_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                Ver en Google Maps
            </a>
        @endif
    </div>
@endif

                <div class="col-12">
                    <div class="kv">
                        <div class="k">Observación</div>
                        <div class="v">{{ $registro->observacion ?: '' }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .kv .k {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .kv .v {
        font-weight: 600;
        font-size: 1rem;
    }

    @media print {

        nav,
        .navbar,
        .no-print,
        .btn,
        footer {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .card,
        .card-body {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card-header {
            background: #fff !important;
            border-bottom: 1px solid #ccc !important;
            padding-bottom: 10px !important;
            margin-bottom: 12px !important;
        }

        .kv {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            break-inside: avoid;
        }

        img {
            max-width: 100% !important;
            height: auto !important;
            page-break-inside: avoid;
        }

        hr {
            margin: 10px 0 !important;
        }

        @page {
            size: A4;
            margin: 15mm;
        }
    }
</style>
@endpush
