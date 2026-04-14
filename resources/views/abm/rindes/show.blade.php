@extends('layouts.app')

@section('title', 'Detalle de Rinde')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-chart-column me-2"></i> Detalle de rinde
                </h3>

                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('rindes.edit', $rinde) }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-pen me-1"></i> Editar
                    </a>
                    <a href="{{ route('rindes.index') }}" class="btn btn-outline-secondary btn-mat">
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Fecha</div>
                        <div class="v">{{ optional($rinde->fecha)->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Usuario</div>
                        <div class="v">{{ $rinde->user->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Rinde</div>
                        <div class="v">{{ number_format($rinde->rinde, 2, ',', '.') }} qq/ha</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Lote</div>
                        <div class="v">{{ $rinde->lote->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Establecimiento</div>
                        <div class="v">{{ $rinde->lote->establecimiento->nombre ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Campaña</div>
                        <div class="v">{{ $rinde->lote->campania->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Cultivo</div>
                        <div class="v">{{ $rinde->lote->cultivo->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Humedad</div>
                        <div class="v">
                            {{ $rinde->humedad !== null ? number_format($rinde->humedad, 2, ',', '.') . ' %' : '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Superficie cosechada</div>
                        <div class="v">
                            {{ $rinde->superficie_cosechada !== null ? number_format($rinde->superficie_cosechada, 2, ',', '.') . ' ha' : '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="kv">
                        <div class="k">Observación</div>
                        <div class="v">{{ $rinde->observacion ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
