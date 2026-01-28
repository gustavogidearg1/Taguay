{{-- resources/views/compras/show_pdf.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Compra #{{ $compra->id }}</title>
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
    .total-box { width: 220px; margin-left:auto; border:1px solid #ddd; padding:8px; }
  </style>
</head>
<body>
@php
  $org  = $compra->organizacion;
  $subs = $compra->subCompras ?? collect();
  $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';
  $total = (float) $subs->sum(fn($s) => (float)($s->sub_total ?? 0));

  // Logo: si está en public/storage/images/logo-taguay.png, mejor usar public_path()
  $logoPath = public_path('storage/images/logo-taguay.png');
@endphp

  <div class="header">
    @if(file_exists($logoPath))
      <img src="{{ $logoPath }}" style="height:42px;">
    @endif
    <div>
      <p class="title">Compra #{{ $compra->id }}</p>
      <div class="muted">
        Fecha: <strong>{{ optional($compra->fecha)->format('d/m/Y') ?? '—' }}</strong>
        &nbsp;•&nbsp; Organización: <strong>{{ $org?->name ?? '—' }}</strong>
        @if($org?->codigo) ({{ $org->codigo }}) @endif
      </div>
    </div>
  </div>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Datos de la compra</h3>

  <table style="border:none;">
    <tr>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Fecha</div><div class="v">{{ optional($compra->fecha)->format('d/m/Y') ?? '—' }}</div></div>
      </td>
      <td style="border:none; width:25%;">
        <div class="kv"><div class="k">Fecha entrega</div><div class="v">{{ optional($compra->fecha_entrega)->format('d/m/Y') ?: '—' }}</div></div>
      </td>
      <td style="border:none; width:50%;">
        <div class="kv"><div class="k">Organización</div><div class="v">{{ $org?->name ?? '—' }} @if($org?->codigo) ({{ $org->codigo }}) @endif</div></div>
      </td>
    </tr>
    <tr>
      <td style="border:none;">
        <div class="kv"><div class="k">Campaña</div><div class="v">{{ $compra->campania?->name ?? '—' }}</div></div>
      </td>
      <td style="border:none;">
        <div class="kv"><div class="k">Condición pago</div><div class="v">{{ $compra->condicionPago?->name ?? '—' }}</div></div>
      </td>
      <td style="border:none;">
        <div class="kv"><div class="k">Moneda</div><div class="v">{{ $compra->moneda?->name ?? '—' }}</div></div>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Observaciones</h3>
  <div class="kv">
    <div class="k">Obs</div>
    <div class="v" style="font-weight:500;">{{ $compra->obs ?: '—' }}</div>
  </div>

  <div class="divider"></div>

  <h3 style="margin:0 0 6px 0;">Detalle</h3>

  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th class="text-end" style="width:90px;">Cant.</th>
        <th style="width:90px;">Unidad</th>
        <th class="text-end" style="width:110px;">Precio</th>
        <th style="width:120px;">Moneda</th>

        <th class="text-end" style="width:120px;">SubTotal</th>
      </tr>
    </thead>
    <tbody>
      @forelse($subs as $s)
        <tr>
          <td><strong>{{ $s->producto?->name ?? '—' }}</strong></td>
          <td class="text-end">{{ $fmtNum($s->cantidad) }}</td>
          <td>{{ $s->unidad?->corta ?? $s->unidad?->name ?? '—' }}</td>
          <td class="text-end">{{ $fmtNum($s->precio) }}</td>
          <td>{{ $s->moneda?->name ?? '—' }}</td>

          <td class="text-end"><strong>{{ $fmtNum($s->sub_total) }}</strong></td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align:center; color:#666; padding:16px;">Sin detalle cargado.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:10px;">
    <div class="total-box">
      <div style="display:flex; justify-content:space-between;">
        <span><strong>Total</strong></span>
        <span><strong>{{ $fmtNum($total) }}</strong></span>
      </div>
    </div>
  </div>

</body>
</html>
