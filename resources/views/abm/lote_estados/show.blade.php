@extends('layouts.app')

@section('title', 'Detalle Estado por Lote')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-list-check me-2"></i> Detalle estado por lote
                </h3>

                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('lote-estados.edit', $registro) }}" class="btn btn-primary btn-mat">
                        <i class="fa-solid fa-pen me-1"></i> Editar
                    </a>
                    <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat">
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

                <div class="col-12 col-md-4">
                    <div class="kv">
                        <div class="k">Cultivo</div>
                        <div class="v">{{ $registro->lote->cultivo->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="kv">
                        <div class="k">Observación</div>
                        <div class="v">{{ $registro->observacion ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
