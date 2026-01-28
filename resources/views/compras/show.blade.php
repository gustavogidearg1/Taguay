{{-- resources/views/compras/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Ver Compra #' . $compra->id)

@push('styles')
<style>
  /* Evita overflow de acciones en mobile */
  .compra-actions .btn { white-space: nowrap; }
  @media (max-width: 576px) {
    .compra-header { align-items: flex-start !important; }
    .compra-actions { width: 100%; }
    .compra-actions .btn { width: 100%; }
    .compra-title h3 { font-size: 1.05rem; }
    .compra-title .badge { font-size: .75rem; }
  }

  /* Si ya los tenés en tu app.css podés borrar esto.
     Los dejo por si compras.show se ve distinto al contrato. */
  .kv .k{ font-size:.78rem; color:#6c757d; text-transform:uppercase; letter-spacing:.02em; }
  .kv .v{ font-weight:600; }
  .divider{ height:1px; background:rgba(0,0,0,.08); margin:1rem 0; }
  .section-title{ font-weight:800; display:flex; align-items:center; gap:.35rem; }
  .table-soft thead th{ background:rgba(25,135,84,.06); }

  /* ===================== PRINT ===================== */
  @media print {

    /* Oculta todo lo marcado como no imprimible */
    .no-print,
    .no-print * {
      display: none !important;
      visibility: hidden !important;
    }

    /* Intento de ocultar menú/layout (ajustá selectores si tu layout usa otros) */
    nav,
    header,
    footer,
    aside,
    .navbar,
    .sidebar,
    .offcanvas,
    .offcanvas-backdrop,
    .topbar,
    .bottom-nav,
    #sidebar,
    #rightSidebar,
    #topbar,
    #app-sidebar {
      display: none !important;
      visibility: hidden !important;
    }

    /* Que el contenido use todo el ancho al imprimir */
    body {
      padding: 0 !important;
      margin: 0 !important;
      background: #fff !important;
    }

    .container,
    .container-fluid,
    .container-lg {
      max-width: 100% !important;
      width: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    .card {
      border: none !important;
      box-shadow: none !important;
    }

    /* Evita cortes raros */
    table, tr, td, th { page-break-inside: avoid; }
  }

</style>
@endpush

@section('content')
@php
  $logo = asset('storage/images/logo-taguay.png');
  $org  = $compra->organizacion;

  $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';

  $subs = $compra->subCompras ?? collect();
  $total = (float) $subs->sum(fn($s) => (float)($s->sub_total ?? 0));
@endphp

<div class="container-fluid container-lg py-3">

  <div class="card mat-card">

    {{-- Header (igual al contrato) --}}
    <div class="card-header header-mint d-flex flex-wrap gap-3 compra-header">

      <div class="d-flex align-items-start gap-3 compra-title flex-grow-1">
        <img src="{{ $logo }}" alt="Logo" height="42" style="object-fit:contain">

        <div class="w-100">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <h3 class="mat-title mb-0">
              <i class="fa-solid fa-cart-shopping me-2"></i>
              Compra #{{ $compra->id }}
            </h3>
            <span class="badge badge-soft">Comercial</span>
          </div>

          <div class="text-muted small">
            Fecha: <strong>{{ optional($compra->fecha)->format('d/m/Y') ?? '—' }}</strong>
            <span class="mx-2">•</span>
            Organización: <strong>{{ $org?->name ?? '—' }}</strong>
            @if($org?->codigo)
              <span class="text-muted">({{ $org->codigo }})</span>
            @endif
          </div>
        </div>
      </div>

    </div>

    <div class="card-body">

      {{-- Alertas (no imprimir) --}}
      @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger no-print">{{ session('error') }}</div>
      @endif

      {{-- Datos de la compra --}}
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="section-title">
          <i class="fa-solid fa-circle-info me-2 text-success"></i>Datos de la compra
        </div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Fecha</div>
            <div class="v">{{ optional($compra->fecha)->format('d/m/Y') ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Fecha entrega</div>
            <div class="v">{{ optional($compra->fecha_entrega)->format('d/m/Y') ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Organización</div>
            <div class="v">
              {{ $org?->name ?? '—' }}
              @if($org?->codigo)
                <span class="text-muted">({{ $org->codigo }})</span>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Campaña</div>
            <div class="v">{{ $compra->campania?->name ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Condición pago</div>
            <div class="v">{{ $compra->condicionPago?->name ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Momento pago</div>
            <div class="v">{{ optional($compra->momento_pago)->format('d/m/Y') ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Activo</div>
            <div class="v">
              @if($compra->activo)
                <span class="badge text-bg-success">Sí</span>
              @else
                <span class="badge text-bg-secondary">No</span>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Moneda</div>
            <div class="v">{{ $compra->moneda?->name ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Moneda financ.</div>
            <div class="v">{{ $compra->monedaFin?->name ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Tasa financ.</div>
            <div class="v">{{ $compra->tasa_financ ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Lugar entrega</div>
            <div class="v">{{ $compra->lugar_entrega ?: '—' }}</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Observaciones --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-pen-nib me-2 text-success"></i>Observaciones
      </div>
      <div class="row g-3">
        <div class="col-12">
          <div class="kv">
            <div class="k">Obs</div>
            <div class="v" style="font-weight: 500;">{{ $compra->obs ?: '—' }}</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Detalle --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-list me-2 text-success"></i>Detalle
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-soft align-middle mb-0">
          <thead>
            <tr>
              <th>Producto</th>
              <th style="width:120px;" class="text-end">Cant.</th>
              <th style="width:120px;">Unidad</th>
              <th style="width:140px;" class="text-end">Precio</th>
              <th style="width:160px;">Moneda</th>
              <th style="width:140px;">Venc.</th>
              <th style="width:140px;" class="text-end">SubTotal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subs as $s)
              <tr>
                <td class="fw-semibold">{{ $s->producto?->name ?? '—' }}</td>
                <td class="text-end">{{ $fmtNum($s->cantidad) }}</td>
                <td>{{ $s->unidad?->corta ?? $s->unidad?->name ?? '—' }}</td>
                <td class="text-end">{{ $fmtNum($s->precio) }}</td>
                <td>{{ $s->moneda?->name ?? '—' }}</td>
                <td>{{ optional($s->fecha_venc)->format('d/m/Y') ?: '—' }}</td>
                <td class="text-end fw-bold">{{ $fmtNum($s->sub_total) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Sin detalle cargado.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-end">
        <div class="alert alert-light border mb-0" style="min-width:260px;">
          <div class="d-flex justify-content-between">
            <span><strong>Total</strong></span>
            <span class="fw-bold">{{ $fmtNum($total) }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- Acciones (al final, igual al contrato) --}}
  <div class="no-print mt-4 pt-3 border-top">
    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end compra-actions">

        <a href="{{ route('compras.show_pdf', $compra) }}" class="btn btn-outline-danger btn-mat">
  <i class="fa-solid fa-file-pdf me-1"></i> PDF
</a>

      <button class="btn btn-outline-secondary btn-mat" onclick="window.print()">
        <i class="fa-solid fa-print me-1"></i> Imprimir
      </button>

      <a href="{{ route('compras.edit', $compra) }}" class="btn btn-primary btn-mat">
        <i class="fa-solid fa-pen me-1"></i> Editar
      </a>

      <a href="{{ route('compras.index') }}" class="btn btn-light btn-mat">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

</div>
@endsection
