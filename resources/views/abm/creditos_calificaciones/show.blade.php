{{-- resources/views/abm/creditos_calificaciones/show.blade.php --}}
@extends('layouts.app')

@section('title','Calificación #'.$item->id)

@push('styles')
<style>
  .kv{ padding:12px 12px; border:1px solid rgba(0,0,0,.06); border-radius:14px; background:#fff; }
  .kv .k{ font-size:.78rem; color:#6b7280; text-transform:uppercase; letter-spacing:.02em; }
  .kv .v{ font-weight:600; color:#0f172a; margin-top:2px; }

  .section-title{ font-weight:700; color:#0f172a; }
  .divider{ height:1px; background:rgba(0,0,0,.06); margin:18px 0; }

  /* PRINT */
  @media print{
    .no-print, .no-print * { display:none !important; visibility:hidden !important; }
    nav, header, footer, aside, .navbar, .sidebar, .offcanvas, .offcanvas-backdrop { display:none !important; }
    body{ margin:0 !important; background:#fff !important; }
    .container, .container-fluid, .container-lg{ max-width:100% !important; width:100% !important; padding:0 !important; margin:0 !important; }
    .card{ border:none !important; box-shadow:none !important; }
  }
</style>
@endpush

@section('content')
@php
  // Helpers
  $hasText = fn($v) => is_string($v) ? trim($v) !== '' : !empty($v);
  $hasNum  = fn($v) => $v !== null && $v !== '' && is_numeric($v) && (float)$v != 0.0;

  $fmtMoney = fn($v) => number_format((float)$v, 2, ',', '.');
  $fmtDate  = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : null;

  // Pares de líneas (igual que el form)
  $pairs = [
    ['Préstamo Inmediato Pesos (Largo plazo)', 'prest_inm_pesos_lp_usado', 'prest_inm_pesos_lp_disp'],
    ['Préstamo Inmediato Pesos (Corto plazo)', 'prest_inm_pesos_cp_usado', 'prest_inm_pesos_cp_disp'],
    ['Acuerdo descubierto Cta. Cte.', 'acuerdo_descubierto_ctacte_usado', 'acuerdo_descubierto_ctacte_disp'],
    ['Préstamo Inmediato USD (Largo plazo)', 'prest_inm_usd_lp_usado', 'prest_inm_usd_lp_disp'],
    ['Préstamo Inmediato USD (Corto plazo)', 'prest_inm_usd_cp_usado', 'prest_inm_usd_cp_disp'],
    ['Financiaciones Galicia Nera USD (Corto plazo)', 'fin_galicia_nera_usd_cp_usado', 'fin_galicia_nera_usd_cp_disp'],
    ['Financiaciones Galicia Nera $ (Corto plazo)', 'fin_galicia_nera_pesos_cp_usado', 'fin_galicia_nera_pesos_cp_disp'],
    ['Prendarios $', 'prendarios_pesos_usado', 'prendarios_pesos_disp'],
    ['Prendarios USD', 'prendarios_usd_usado', 'prendarios_usd_disp'],
    ['Garantizados SGR $', 'garant_sgr_pesos_usado', 'garant_sgr_pesos_disp'],
    ['Garantizados SGR USD', 'garant_sgr_usd_usado', 'garant_sgr_usd_disp'],
  ];

  // Para saber si hay algo que mostrar en "Totales"
  $hasTotales = $hasNum($item->calif_total_pesos) || $hasNum($item->calif_total_usd) ||
                $hasNum($item->usado_total_pesos) || $hasNum($item->usado_total_usd) ||
                $hasNum($item->disp_total_pesos)  || $hasNum($item->disp_total_usd)  ||
                $hasNum($item->sola_firma)        || $hasText($item->obs_firma);

  // Para saber si hay líneas que mostrar
  $hasLineas = false;
  foreach($pairs as $p){
    [$label, $used, $avail] = $p;
    if ($hasNum(data_get($item, $used)) || $hasNum(data_get($item, $avail))) { $hasLineas = true; break; }
  }
@endphp

<div class="container-fluid container-lg py-3">

  <div class="card mat-card">

    {{-- Header --}}
    <div class="card-header mat-header d-flex flex-wrap gap-2 align-items-start">
      <div class="flex-grow-1">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <h3 class="mat-title mb-0">
            <i class="fa-solid fa-chart-line me-2"></i>
            Calificación #{{ $item->id }}
          </h3>
          <span class="badge text-bg-light border">
            {{ $item->financiera->name ?? 'Sin financiera' }}
          </span>
          @if($hasText($item->tipo_credito))
            <span class="badge text-bg-secondary">{{ $item->tipo_credito }}</span>
          @endif
        </div>

        <div class="text-muted small mt-1">
          @if($item->fecha)
            Fecha: <strong>{{ $fmtDate($item->fecha) }}</strong>
          @endif
          @if($item->vencimiento)
            <span class="mx-2">•</span> Vencimiento: <strong>{{ $fmtDate($item->vencimiento) }}</strong>
          @endif
        </div>
      </div>

      <div class="ms-auto d-flex gap-2 no-print">
        <a href="{{ route('creditos-calificaciones.edit', $item) }}" class="btn btn-primary btn-mat">
          <i class="fa-solid fa-pen me-1"></i> Editar
        </a>
        <a href="{{ route('creditos-calificaciones.index') }}" class="btn btn-light btn-mat">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger no-print">{{ session('error') }}</div>
      @endif

      {{-- Datos generales (solo si hay algo) --}}
      @if($item->financiera || $hasText($item->tipo_credito) || $item->fecha || $item->vencimiento)
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="section-title">
            <i class="fa-solid fa-circle-info me-2 text-success"></i>Datos generales
          </div>
        </div>

        <div class="row g-3">
          @if($item->financiera)
            <div class="col-12 col-md-6">
              <div class="kv">
                <div class="k">Proveedor financiero</div>
                <div class="v">{{ $item->financiera->name }}</div>
              </div>
            </div>
          @endif

          @if($hasText($item->tipo_credito))
            <div class="col-12 col-md-3">
              <div class="kv">
                <div class="k">Tipo de crédito</div>
                <div class="v">{{ $item->tipo_credito }}</div>
              </div>
            </div>
          @endif

          @if($item->fecha)
            <div class="col-6 col-md-1"></div> {{-- opcional para balancear si querés --}}
          @endif

          @if($item->fecha)
            <div class="col-6 col-md-3">
              <div class="kv">
                <div class="k">Fecha</div>
                <div class="v">{{ $fmtDate($item->fecha) }}</div>
              </div>
            </div>
          @endif

          @if($item->vencimiento)
            <div class="col-6 col-md-3">
              <div class="kv">
                <div class="k">Vencimiento</div>
                <div class="v">{{ $fmtDate($item->vencimiento) }}</div>
              </div>
            </div>
          @endif
        </div>

        <div class="divider"></div>
      @endif

      {{-- Totales --}}
      @if($hasTotales)
        <div class="section-title mb-2">
          <i class="fa-solid fa-coins me-2 text-success"></i>Totales
        </div>

        <div class="row g-3">
          @if($hasNum($item->calif_total_pesos))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Calificación total $</div>
                <div class="v">{{ $fmtMoney($item->calif_total_pesos) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->calif_total_usd))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Calificación total USD</div>
                <div class="v">{{ $fmtMoney($item->calif_total_usd) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->sola_firma))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Sola firma</div>
                <div class="v">{{ $fmtMoney($item->sola_firma) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->usado_total_pesos))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Usado total $</div>
                <div class="v">{{ $fmtMoney($item->usado_total_pesos) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->usado_total_usd))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Usado total USD</div>
                <div class="v">{{ $fmtMoney($item->usado_total_usd) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->disp_total_pesos))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Disponible total $</div>
                <div class="v">{{ $fmtMoney($item->disp_total_pesos) }}</div>
              </div>
            </div>
          @endif

          @if($hasNum($item->disp_total_usd))
            <div class="col-12 col-md-4">
              <div class="kv">
                <div class="k">Disponible total USD</div>
                <div class="v">{{ $fmtMoney($item->disp_total_usd) }}</div>
              </div>
            </div>
          @endif

          @if($hasText($item->obs_firma))
            <div class="col-12 col-md-8">
              <div class="kv">
                <div class="k">Obs de firma</div>
                <div class="v">{{ $item->obs_firma }}</div>
              </div>
            </div>
          @endif
        </div>

        <div class="divider"></div>
      @endif

      {{-- Líneas / Productos --}}
      @if($hasLineas)
        <div class="section-title mb-2">
          <i class="fa-solid fa-layer-group me-2 text-success"></i>Líneas / Productos
        </div>

        <div class="row g-3">
          @foreach($pairs as [$label, $used, $avail])
            @php
              $u = data_get($item, $used);
              $d = data_get($item, $avail);
              $show = $hasNum($u) || $hasNum($d);
            @endphp

            @if($show)
              <div class="col-12">
                <div class="card border-0 bg-light">
                  <div class="card-body py-2">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-4 fw-semibold">{{ $label }}</div>

                      @if($hasNum($u))
                        <div class="col-6 col-md-4">
                          <div class="kv">
                            <div class="k">Usado</div>
                            <div class="v">{{ $fmtMoney($u) }}</div>
                          </div>
                        </div>
                      @endif

                      @if($hasNum($d))
                        <div class="col-6 col-md-4">
                          <div class="kv">
                            <div class="k">Disponible</div>
                            <div class="v">{{ $fmtMoney($d) }}</div>
                          </div>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        </div>

        <div class="divider"></div>
      @endif

      {{-- Imagen --}}
      @if($hasText($item->imagen))
        <div class="section-title mb-2">
          <i class="fa-solid fa-image me-2 text-success"></i>Imagen
        </div>

        <div class="row g-3">
          <div class="col-12">
            <a href="{{ asset('storage/'.$item->imagen) }}" target="_blank" class="d-inline-block">
              <img src="{{ asset('storage/'.$item->imagen) }}"
                   alt="Imagen"
                   style="max-width:520px;width:100%;height:auto;border-radius:16px;">
            </a>
          </div>
        </div>

        <div class="divider"></div>
      @endif

      {{-- Observación --}}
      @if($hasText($item->observacion))
        <div class="section-title mb-2">
          <i class="fa-solid fa-pen-nib me-2 text-success"></i>Observación
        </div>

        <div class="row g-3">
          <div class="col-12">
            <div class="kv">
              <div class="k">Notas</div>
              <div class="v" style="font-weight:500;">{!! nl2br(e($item->observacion)) !!}</div>
            </div>
          </div>
        </div>
      @endif

    </div>
  </div>

  {{-- Acciones (al final) --}}
  <div class="no-print mt-4 pt-3 border-top">
    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
      <button class="btn btn-outline-secondary btn-mat" onclick="window.print()">
        <i class="fa-solid fa-print me-1"></i> Imprimir
      </button>

      <a href="{{ route('creditos-calificaciones.edit', $item) }}" class="btn btn-primary btn-mat">
        <i class="fa-solid fa-pen me-1"></i> Editar
      </a>

      <a href="{{ route('creditos-calificaciones.index') }}" class="btn btn-light btn-mat">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

</div>
@endsection
