@php
  $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—';
  $fmtNum  = fn($n) => ($n === null || $n === '') ? '—' : number_format((float)$n, 2, ',', '.');
@endphp

<h2 style="margin:0 0 10px 0;">Contrato {{ $accion }}</h2>

<p style="margin:0 0 12px 0;">
  Se {{ $accion === 'creado' ? 'creó' : 'actualizó' }} el contrato en el sistema.
</p>

<hr>

<h3 style="margin:12px 0 6px 0;">Datos principales</h3>
<p style="margin:0;">
  <strong>Nro Contrato:</strong> {{ $contrato->nro_contrato }}<br>
  <strong>Num Forward:</strong> {{ $contrato->num_forward ?? '—' }}<br>
  <strong>Fecha:</strong> {{ $fmtDate($contrato->fecha) }}<br>
  <strong>Vendedor:</strong> {{ $contrato->vendedor ?? '—' }}<br>
</p>

<p style="margin:10px 0 0 0;">
  <strong>Organización:</strong>
  {{ $contrato->organizacion?->name ?? '—' }}
  @if($contrato->organizacion?->codigo)
    ({{ $contrato->organizacion->codigo }})
  @endif
  <br>

  <strong>Campaña:</strong> {{ $contrato->campania?->name ?? '—' }}<br>
  <strong>Cultivo:</strong> {{ $contrato->cultivo?->name ?? '—' }}<br>
  <strong>Moneda:</strong> {{ $contrato->moneda?->name ?? '—' }}<br>
</p>

<p style="margin:10px 0 0 0;">
  <strong>Entrega:</strong>
  {{ $fmtDate($contrato->entrega_inicial) }} → {{ $fmtDate($contrato->entrega_final) }}
</p>

<hr>

<h3 style="margin:12px 0 6px 0;">Cantidad y precios</h3>
<p style="margin:0;">
  <strong>Cantidad (tn):</strong> {{ $fmtNum($contrato->cantidad_tn) }}<br>
  <strong>Precio:</strong> {{ $fmtNum($contrato->precio) }}<br>
  <strong>Precio fijado:</strong> {{ $fmtNum($contrato->precio_fijado) }}<br>
</p>

<hr>

<h3 style="margin:12px 0 6px 0;">Condiciones</h3>
<p style="margin:0;">
  <strong>Característica de precio:</strong> {{ $contrato->caracteristica_precio ?? '—' }}<br>
  <strong>Formación de precio:</strong> {{ $contrato->formacion_precio ?? '—' }}<br>
  <strong>Condición de precio:</strong> {{ $contrato->condicion_precio ?? '—' }}<br>
  <strong>Condición de pago:</strong> {{ $contrato->condicion_pago ?? '—' }}<br>
  <strong>Lista de grano:</strong> {{ $contrato->lista_grano ?? '—' }}<br>
  <strong>Destino:</strong> {{ $contrato->destino ?? '—' }}<br>
  <strong>Formato:</strong> {{ $contrato->formato ?? '—' }}<br>
  <strong>Disponible tipo:</strong> {{ $contrato->disponible_tipo ?? '—' }}<br>
</p>

@if(!empty($contrato->definicion))
  <hr>
  <h3 style="margin:12px 0 6px 0;">Definición</h3>
  <p style="margin:0; white-space:pre-line;">{{ $contrato->definicion }}</p>
@endif

<hr>

<h3 style="margin:12px 0 6px 0;">Otros</h3>
<p style="margin:0;">
  <strong>Comisión:</strong> {{ $fmtNum($contrato->comision) }}<br>
  <strong>Paritaria:</strong> {{ $fmtNum($contrato->paritaria) }}<br>
  <strong>Volátil:</strong> {{ $fmtNum($contrato->volatil) }}<br>
  <strong>Obs:</strong> {{ $contrato->obs ?? '—' }}<br>
  <strong>Importante:</strong> {{ $contrato->importante ?? '—' }}<br>
</p>

@if($contrato->relationLoaded('subContratos') ? $contrato->subContratos->count() : $contrato->subContratos()->count())
  <hr>
  <h3 style="margin:12px 0 6px 0;">Historial de fijación</h3>

  @php
    $subs = $contrato->relationLoaded('subContratos')
      ? $contrato->subContratos
      : $contrato->subContratos()->orderBy('fecha')->get();
  @endphp

  <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; border:1px solid #ddd;">
    <thead>
      <tr>
        <th align="left" style="border-bottom:1px solid #ddd;">Fecha</th>
        <th align="right" style="border-bottom:1px solid #ddd;">Toneladas</th>
        <th align="right" style="border-bottom:1px solid #ddd;">Nuevo precio fijación</th>
        <th align="left" style="border-bottom:1px solid #ddd;">Observación</th>
      </tr>
    </thead>
    <tbody>
      @foreach($subs as $s)
        <tr>
          <td style="border-bottom:1px solid #eee;">{{ $fmtDate($s->fecha) }}</td>
          <td align="right" style="border-bottom:1px solid #eee;">{{ $fmtNum($s->toneladas) }}</td>
          <td align="right" style="border-bottom:1px solid #eee;">{{ $fmtNum($s->nuevo_precio_fijacion) }}</td>
          <td style="border-bottom:1px solid #eee;">{{ $s->observacion ?? '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif

<hr>

<p style="margin:12px 0 0 0;">
  Podés ver/imprimir el contrato desde el sistema.
</p>
