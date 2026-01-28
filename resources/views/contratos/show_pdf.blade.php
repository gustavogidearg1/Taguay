{{-- resources/views/contratos/show_pdf.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Contrato #{{ $contrato->nro_contrato }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
    .header { display:flex; align-items:center; gap:12px; }
    .title { font-size: 18px; font-weight: 800; margin:0; }
    .muted { color:#666; font-size: 11px; }
    .divider { height:1px; background:#e5e5e5; margin:12px 0; }
    .kv { margin-bottom: 6px; }
    .k { font-size: 10px; color:#666; text-transform:uppercase; letter-spacing:.02em; }
    .v { font-weight: 700; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:6px; }
    th { background:#f3f7f4; text-align:left; }
    .text-end { text-align:right; }
  </style>
</head>
<body>
@php
  $org = $contrato->organizacion;

  $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';
  $subtotal = (float)($contrato->precio ?? 0) * (float)($contrato->cantidad_tn ?? 0);

  $subs = $contrato->subContratos ?? collect();
  $tieneSubs = $subs->count() > 0;

  // Logo: mejor con public_path para DomPDF
  $logoPath = public_path('storage/images/logo-taguay.png');

  $labels = [
    'caracteristica_precio' => ['PRECIO_HECHO'=>'Precio hecho','A_FIJAR'=>'A fijar','CONDICIONAL'=>'Condicional'],
    'formacion_precio'      => ['A_COBRAR'=>'A cobrar','CON_ANTICIPO'=>'Con anticipo','EN_CANJE'=>'En canje','FORWARD'=>'Forward'],
    'condicion_precio'      => ['ENTREGA_OBL'=>'Entrega obligatoria','WASHOUT'=>'Washout'],
    'condicion_pago'        => ['A_COBRAR'=>'A cobrar','CON_ANTICIPO'=>'Con anticipo','EN_CANJE'=>'En canje','NO_SE_COBRA'=>'No se cobra'],
    'lista_grano'           => ['ABIERTA'=>'Abierta','CERRADA'=>'Cerrada','CAMARA'=>'Camara'],
    'destino'               => ['GRANO'=>'Grano','OTRO_GRANO'=>'Otro grano','OTRO'=>'Otro'],
    'formato'               => ['FORWARD'=>'Forward','DISPONIBLE'=>'Disponible'],
    'disponible_tipo'       => ['PRECIO_HECHO'=>'Precio hecho','A_FIJAR'=>'A fijar'],
  ];
@endphp

  <div class="header">
    @if(file_exists($logoPath))
      <img src="{{ $logoPath }}" style="height:42px;">
    @endif
    <div>
      <p class="title">Contrato #{{ $contrato->nro_contrato }}</p>
      <div class="muted">
        Fecha: <strong>{{ optional($contrato->fecha)->format('d/m/Y') ?? '—' }}</strong>
        @if($contrato->num_forward)
          &nbsp;•&nbsp; Forward: <strong>{{ $contrato->num_forward }}</strong>
        @endif
        &nbsp;•&nbsp; Organización: <strong>{{ $org?->name ?? '—' }}</strong>
      </div>
    </div>
  </div>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Datos del contrato</h3>
  <table style="border:none;">
    <tr>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Nro Contrato</div><div class="v">{{ $contrato->nro_contrato }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Num Forward</div><div class="v">{{ $contrato->num_forward ?? '—' }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Campaña</div><div class="v">{{ $contrato->campania->name ?? '—' }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Cultivo</div><div class="v">{{ $contrato->cultivo->name ?? '—' }}</div></div>
      </td>
    </tr>
    <tr>
      <td style="border:none;" colspan="2">
        <div class="kv">
          <div class="k">Organización</div>
          <div class="v">
            {{ $org?->name ?? '—' }}
            @if($org?->codigo) <span style="color:#666;">({{ $org->codigo }})</span> @endif
          </div>
        </div>
      </td>
      <td style="border:none;">
        <div class="kv"><div class="k">Vendedor</div><div class="v">{{ $contrato->vendedor ?? '—' }}</div></div>
      </td>
      <td style="border:none;">
        <div class="kv"><div class="k">Moneda</div><div class="v">{{ $contrato->moneda->name ?? '—' }}</div></div>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Entregas</h3>
  <table style="border:none;">
    <tr>
      <td style="border:none; width:50%;">
        <div class="kv">
          <div class="k">Entrega</div>
          <div class="v">
            {{ optional($contrato->entrega_inicial)->format('d/m/Y') ?? '—' }}
            <span style="color:#666;"> → </span>
            {{ optional($contrato->entrega_final)->format('d/m/Y') ?? '—' }}
          </div>
        </div>
      </td>
      <td style="border:none; width:50%;">
        <div class="kv">
          <div class="k">Destino / Formato / Disponible</div>
          <div class="v">
            {{ $labels['destino'][$contrato->destino] ?? ($contrato->destino ?? '—') }}
            <span style="color:#666;"> / </span>
            {{ $labels['formato'][$contrato->formato] ?? ($contrato->formato ?? '—') }}
            <span style="color:#666;"> / </span>
            {{ $labels['disponible_tipo'][$contrato->disponible_tipo] ?? ($contrato->disponible_tipo ?? '—') }}
          </div>
        </div>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Condiciones</h3>
  <table style="border:none;">
    <tr>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Característica de precio</div><div class="v">{{ $labels['caracteristica_precio'][$contrato->caracteristica_precio] ?? ($contrato->caracteristica_precio ?? '—') }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Formación de precio</div><div class="v">{{ $labels['formacion_precio'][$contrato->formacion_precio] ?? ($contrato->formacion_precio ?? '—') }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Condición de precio</div><div class="v">{{ $labels['condicion_precio'][$contrato->condicion_precio] ?? ($contrato->condicion_precio ?? '—') }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Condición de pago</div><div class="v">{{ $labels['condicion_pago'][$contrato->condicion_pago] ?? ($contrato->condicion_pago ?? '—') }}</div></div>
      </td>
    </tr>
    <tr>
      <td style="border:none;" colspan="4">
        <div class="kv"><div class="k">Lista de grano</div><div class="v">{{ $labels['lista_grano'][$contrato->lista_grano] ?? ($contrato->lista_grano ?? '—') }}</div></div>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Valores</h3>
  <table>
    <thead>
      <tr>
        <th>Cantidad (Tn)</th>
        <th class="text-end">Precio</th>
        <th class="text-end">Precio fijado</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $fmtNum($contrato->cantidad_tn) }}</td>
        <td class="text-end">{{ $fmtNum($contrato->precio) }}</td>
        <td class="text-end">{{ $fmtNum($contrato->precio_fijado) }}</td>
        <td class="text-end"><strong>{{ $fmtNum($subtotal) }}</strong></td>
      </tr>
    </tbody>
  </table>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Observaciones</h3>
  <table>
    <tbody>
      <tr><td><strong>Definición:</strong> {{ $contrato->definicion ?: '—' }}</td></tr>
      <tr><td><strong>Comisión:</strong> {{ $contrato->comision ?: '—' }}</td></tr>
      <tr><td><strong>Paritaria:</strong> {{ $contrato->paritaria ?: '—' }}</td></tr>
      <tr><td><strong>Volátil:</strong> {{ $contrato->volatil ?: '—' }}</td></tr>
      <tr><td><strong>Obs:</strong> {{ $contrato->obs ?: '—' }}</td></tr>
      <tr><td><strong>Importante:</strong> {{ $contrato->importante ?: '—' }}</td></tr>
    </tbody>
  </table>

  @if($tieneSubs)
    <div class="divider"></div>
    <h3 style="margin:0 0 6px 0;">Historial de precio fijación</h3>

    <table>
      <thead>
        <tr>
          <th style="width:120px;">Fecha</th>
          <th class="text-end" style="width:120px;">Toneladas</th>
          <th class="text-end" style="width:180px;">Nuevo precio fijación</th>
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
  @endif

</body>
</html>
