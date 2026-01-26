{{-- resources/views/abm/organizaciones/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Ver Organización')

@section('content')
<style>
  :root{
    --mint: #dff3e3;
    --mint-2:#cfead6;
    --line:#e5e7eb;
    --ink:#1f2937;
  }

  .header-mint{
    background: linear-gradient(180deg, var(--mint), #ffffff);
    border-bottom: 1px solid var(--line);
  }

  .section-title{
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .2px;
  }

  .divider{
    height: 1px;
    background: var(--line);
    margin: 1rem 0;
  }

  .card.mat-card{
    border: 0;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15,23,42,.08);
  }

  .mat-header{
    display:flex;
    align-items:center;
    gap:.75rem;
  }

  .mat-title{
    margin:0;
    font-weight: 800;
    font-size: 1.08rem;
    color: var(--ink);
  }

  .btn-mat{
    border-radius: 10px;
    padding: .45rem .75rem;
  }

  .kv{
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: .75rem .9rem;
    background: #fff;
    height: 100%;
  }
  .kv .k{
    font-size: .78rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .4px;
  }
  .kv .v{
    font-weight: 600;
    color: var(--ink);
    word-break: break-word;
  }

  .badge-soft{
    background: var(--mint);
    color: #14532d;
    border: 1px solid var(--mint-2);
    font-weight: 700;
  }

  @media print {
    nav, .no-print { display:none !important; }
    body { background: #fff !important; }
    .card { border: none !important; box-shadow: none !important; }
    .container { max-width: 100% !important; }
    .kv{ border: 1px solid #ddd !important; }
    .header-mint{ -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>

@php
  $logo = asset('storage/images/logo-taguay.png');
@endphp

<div class="container py-3">

  <div class="card mat-card">

    {{-- Header --}}
    <div class="card-header header-mint d-flex align-items-center gap-3">

      <div class="d-flex align-items-center gap-3">
        <img src="{{ $logo }}" alt="Logo" height="42" style="object-fit:contain">
        <div>
          <div class="d-flex align-items-center gap-2">
            <h3 class="mat-title mb-0">
              <i class="fa-solid fa-sitemap me-2"></i>
              {{ $organizacion->name }}
            </h3>

            @if($organizacion->activo)
              <span class="badge badge-soft">Activa</span>
            @else
              <span class="badge text-bg-secondary">Inactiva</span>
            @endif
          </div>

          <div class="text-muted small">
            Código: <strong>{{ $organizacion->codigo }}</strong>
            <span class="mx-2">•</span>
            Fecha: <strong>{{ optional($organizacion->fecha)->format('d/m/Y') }}</strong>
          </div>
        </div>
      </div>

      <div class="ms-auto d-flex gap-2 no-print">
        <button class="btn btn-outline-secondary btn-mat" onclick="window.print()">
          <i class="fa-solid fa-print me-1"></i> Imprimir
        </button>

        <a href="{{ route('organizaciones.edit', $organizacion) }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-pen me-1"></i> Editar
        </a>

        <a href="{{ route('organizaciones.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">

      {{-- Flash --}}
      @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger no-print">{{ session('error') }}</div>
      @endif

      {{-- Sección: Datos --}}
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="section-title">
          <i class="fa-solid fa-circle-info me-2 text-success"></i>Datos de la organización
        </div>

        <div class="no-print">
          <form method="POST"
                action="{{ route('organizaciones.destroy', $organizacion) }}"
                onsubmit="return confirm('¿Eliminar organización {{ $organizacion->name }}?');">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-mat" type="submit">
              <i class="fa-solid fa-trash me-1"></i> Eliminar
            </button>
          </form>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">ID</div>
            <div class="v">{{ $organizacion->id }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Código</div>
            <div class="v">{{ $organizacion->codigo }}</div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Nombre</div>
            <div class="v">{{ $organizacion->name }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Fecha</div>
            <div class="v">{{ optional($organizacion->fecha)->format('d/m/Y') }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Estado</div>
            <div class="v">
              @if($organizacion->activo)
                <span class="badge badge-soft"><i class="fa-solid fa-circle-check me-1"></i> Activa</span>
              @else
                <span class="badge text-bg-secondary"><i class="fa-solid fa-circle-minus me-1"></i> Inactiva</span>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Descripción</div>
            <div class="v">
              {{ $organizacion->descripcion ?: '—' }}
            </div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Sección: Auditoría --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Auditoría
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Creado</div>
            <div class="v">
              {{ optional($organizacion->created_at)->format('d/m/Y H:i') ?? '—' }}
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Actualizado</div>
            <div class="v">
              {{ optional($organizacion->updated_at)->format('d/m/Y H:i') ?? '—' }}
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
