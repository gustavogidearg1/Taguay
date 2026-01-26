@extends('layouts.app')

@section('title', 'Ver Contrato')

@push('styles')
<style>
  /* Evita overflow de acciones en mobile */
  .contrato-actions .btn { white-space: nowrap; }
  @media (max-width: 576px) {
    .contrato-header { align-items: flex-start !important; }
    .contrato-actions { width: 100%; }
    .contrato-actions .btn { width: 100%; }
    .contrato-title h3 { font-size: 1.05rem; }
    .contrato-title .badge { font-size: .75rem; }
  }
</style>
@endpush

@section('content')
@php
  $logo = asset('storage/images/logo-taguay.png');
  $org = $contrato->organizacion;

  $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';
  $subtotal = (float)($contrato->precio ?? 0) * (float)($contrato->cantidad_tn ?? 0);

  $subs = $contrato->subContratos ?? collect();
  $tieneSubs = $subs->count() > 0;

  $labels = [
    'caracteristica_precio' => [
      'PRECIO_HECHO' => 'Precio hecho',
      'A_FIJAR'      => 'A fijar',
      'CONDICIONAL'  => 'Condicional',
    ],
    'formacion_precio' => [
      'A_COBRAR'     => 'A cobrar',
      'CON_ANTICIPO' => 'Con anticipo',
      'EN_CANJE'     => 'En canje',
      'FORWARD'      => 'Forward',
    ],
    'condicion_precio' => [
      'ENTREGA_OBL'  => 'Entrega obligatoria',
      'WASHOUT'      => 'Washout',
    ],
    'condicion_pago' => [
      'A_COBRAR'     => 'A cobrar',
      'CON_ANTICIPO' => 'Con anticipo',
      'EN_CANJE'     => 'En canje',
      'NO_SE_COBRA'  => 'No se cobra',
    ],
    'lista_grano' => [
      'ABIERTA' => 'Abierta',
      'CERRADA' => 'Cerrada',
      'CAMARA'  => 'Camara',
    ],
    'destino' => [
      'GRANO'      => 'Grano',
      'OTRO_GRANO' => 'Otro grano',
      'OTRO'       => 'Otro',
    ],
    'formato' => [
      'FORWARD'    => 'Forward',
      'DISPONIBLE' => 'Disponible',
    ],
    'disponible_tipo' => [
      'PRECIO_HECHO' => 'Precio hecho',
      'A_FIJAR'      => 'A fijar',
    ],
  ];
@endphp

{{-- ✅ Mejor container para mobile --}}
<div class="container-fluid container-lg py-3">

  <div class="card mat-card">

    {{-- Header --}}
    <div class="card-header header-mint d-flex flex-wrap gap-3 contrato-header">

      {{-- Bloque título --}}
      <div class="d-flex align-items-start gap-3 contrato-title flex-grow-1">
        <img src="{{ $logo }}" alt="Logo" height="42" style="object-fit:contain">

        <div class="w-100">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <h3 class="mat-title mb-0">
              <i class="fa-solid fa-file-contract me-2"></i>
              Contrato #{{ $contrato->nro_contrato }}
            </h3>
            <span class="badge badge-soft">Comercial</span>
          </div>

          <div class="text-muted small">
            Fecha: <strong>{{ optional($contrato->fecha)->format('d/m/Y') ?? '—' }}</strong>
            @if($contrato->num_forward)
              <span class="mx-2">•</span> Forward: <strong>{{ $contrato->num_forward }}</strong>
            @endif
            <span class="mx-2">•</span>
            Organización: <strong>{{ $org?->name ?? '—' }}</strong>
          </div>
        </div>
      </div>


    </div>

    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger no-print">{{ session('error') }}</div>
      @endif

      {{-- ... TODO el resto de tu vista queda igual ... --}}
      {{-- (no hace falta tocar el contenido, ya es bastante responsive con col-12 col-md-*) --}}


      {{-- Datos del contrato --}}
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="section-title">
          <i class="fa-solid fa-circle-info me-2 text-success"></i>Datos del contrato
        </div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Nro Contrato</div>
            <div class="v">{{ $contrato->nro_contrato }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Num Forward</div>
            <div class="v">{{ $contrato->num_forward ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Campaña</div>
            <div class="v">{{ $contrato->campania->name ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Cultivo</div>
            <div class="v">{{ $contrato->cultivo->name ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Organización</div>
            <div class="v">
              {{ $org?->name ?? '—' }}
              @if($org)
                <span class="text-muted">({{ $org->codigo }})</span>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Vendedor</div>
            <div class="v">{{ $contrato->vendedor ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Moneda</div>
            <div class="v">{{ $contrato->moneda->name ?? '—' }}</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Entregas --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-truck-ramp-box me-2 text-success"></i>Entregas
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Entrega</div>
            <div class="v">
              {{ optional($contrato->entrega_inicial)->format('d/m/Y') ?? '—' }}
              <span class="text-muted mx-1">→</span>
              {{ optional($contrato->entrega_final)->format('d/m/Y') ?? '—' }}
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Destino / Formato / Disponible</div>
            <div class="v">
              {{ $labels['destino'][$contrato->destino] ?? ($contrato->destino ?? '—') }}
              <span class="text-muted">/</span>
              {{ $labels['formato'][$contrato->formato] ?? ($contrato->formato ?? '—') }}
              <span class="text-muted">/</span>
              {{ $labels['disponible_tipo'][$contrato->disponible_tipo] ?? ($contrato->disponible_tipo ?? '—') }}
            </div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Condiciones --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-sliders me-2 text-success"></i>Condiciones
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Característica de precio</div>
            <div class="v">{{ $labels['caracteristica_precio'][$contrato->caracteristica_precio] ?? $contrato->caracteristica_precio ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Formación de precio</div>
            <div class="v">{{ $labels['formacion_precio'][$contrato->formacion_precio] ?? $contrato->formacion_precio ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Condición de precio</div>
            <div class="v">{{ $labels['condicion_precio'][$contrato->condicion_precio] ?? $contrato->condicion_precio ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Condición de pago</div>
            <div class="v">{{ $labels['condicion_pago'][$contrato->condicion_pago] ?? $contrato->condicion_pago ?? '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="kv">
            <div class="k">Lista de grano</div>
            <div class="v">{{ $labels['lista_grano'][$contrato->lista_grano] ?? $contrato->lista_grano ?? '—' }}</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      {{-- Valores --}}
      <div class="section-title mb-2">
        <i class="fa-solid fa-coins me-2 text-success"></i>Valores
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Cantidad (Tn)</div>
            <div class="v">{{ $fmtNum($contrato->cantidad_tn) }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Precio</div>
            <div class="v">{{ $fmtNum($contrato->precio) }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Precio fijado</div>
            <div class="v">{{ $fmtNum($contrato->precio_fijado) }}</div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="kv">
            <div class="k">Subtotal (Precio x Tn)</div>
            <div class="v">{{ $fmtNum($subtotal) }}</div>
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
            <div class="k">Definición</div>
            <div class="v">{{ $contrato->definicion ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="kv">
            <div class="k">Comisión</div>
            <div class="v">{{ $contrato->comision ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="kv">
            <div class="k">Paritaria</div>
            <div class="v">{{ $contrato->paritaria ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="kv">
            <div class="k">Volátil</div>
            <div class="v">{{ $contrato->volatil ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Obs</div>
            <div class="v">{{ $contrato->obs ?: '—' }}</div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="kv">
            <div class="k">Importante</div>
            <div class="v">{{ $contrato->importante ?: '—' }}</div>
          </div>
        </div>
      </div>

      {{-- Historial --}}
      @if($tieneSubs)
        <div class="divider"></div>

        <div class="section-title mb-2">
          <i class="fa-solid fa-tag me-2 text-success"></i>Historial de precio fijación
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-bordered table-soft align-middle">
            <thead>
              <tr>
                <th style="width: 140px;">Fecha</th>
                <th style="width: 140px;" class="text-end">Toneladas</th>
                <th style="width: 220px;" class="text-end">Nuevo precio fijación</th>
                <th>Observación</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subs->sortByDesc('fecha') as $sc)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($sc->fecha)->format('d/m/Y') }}</td>
                  <td class="text-end">{{ $fmtNum($sc->toneladas) }}</td>
                  <td class="text-end">{{ $fmtNum($sc->nuevo_precio_fijacion) }}</td>
                  <td>{{ $sc->observacion ?: '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
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

    <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-primary btn-mat">
      <i class="fa-solid fa-pen me-1"></i> Editar
    </a>

    <a href="{{ route('contratos.index') }}" class="btn btn-light btn-mat">
      <i class="fa-solid fa-arrow-left me-1"></i> Volver
    </a>
  </div>
</div>




</div>
@endsection
