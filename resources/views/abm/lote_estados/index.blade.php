@extends('layouts.app')

@section('title', 'Estados por Lote')

@section('content')
<div class="page-wrap">
    <div class="card mat-card">
        <div class="card-header header-mint py-3">
            <div class="mat-header">
                <h3 class="mat-title">
                    <i class="fa-solid fa-list-check me-2"></i> Estados por Lote
                </h3>

               <div class="ms-auto d-flex align-items-center gap-2">

    @can('ver_agricola')
        <a href="{{ route('lote-estados.quick-create') }}"
           class="btn btn-success btn-mat d-flex align-items-center">
            <i class="fa-solid fa-mobile-screen-button me-1"></i>
            Carga Movil
        </a>
    @endcan

    <a href="{{ route('lote-estados.create') }}" class="btn btn-primary btn-mat">
        <i class="fa-solid fa-plus me-1"></i> Nuevo
    </a>

</div>
            </div>
        </div>



        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- BOTÓN FILTROS --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-semibold">
        <i class="fa-solid fa-filter me-1"></i> Filtros
    </h5>

    <button class="btn btn-outline-secondary btn-sm"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosCollapse"
            aria-expanded="false">
        <i class="fa-solid fa-chevron-down me-1"></i> Mostrar / Ocultar
    </button>
</div>

{{-- FILTROS COLAPSADOS --}}
<div class="collapse" id="filtrosCollapse">

    <div class="card card-body mb-4 shadow-sm border-0">

        <form method="GET" action="{{ route('lote-estados.index') }}" class="row g-3">

            <div class="col-12 col-md-2">
                <label class="form-label">Establecimiento</label>
                <select name="establecimiento_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($establecimientos as $establecimiento)
                        <option value="{{ $establecimiento->id }}"
                            {{ (string)$establecimientoId === (string)$establecimiento->id ? 'selected' : '' }}>
                            {{ $establecimiento->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label">Lote</label>
                <select name="lote_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}"
                            {{ (string)$loteId === (string)$lote->id ? 'selected' : '' }}>
                            {{ $lote->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label">Estado</label>
                <select name="estado_cultivo_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}"
                            {{ (string)$estadoId === (string)$estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
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

            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat w-100">
                    Limpiar
                </a>
                <button type="submit" class="btn btn-primary btn-mat w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </div>

        </form>

    </div>
</div>

            @include('abm.lote_estados._tabla', ['registros' => $registros])

            <div class="mt-4">
                {{ $registros->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
