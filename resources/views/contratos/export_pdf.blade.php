<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color:#111; }
    h2 { margin: 0 0 8px; }
    .meta { color:#666; margin: 0 0 10px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:5px 6px; vertical-align: top; }
    th { background:#f3f4f6; text-align:left; }
    .right { text-align:right; }
    .muted { color:#666; }
  </style>
</head>
<body>
  <h2>Contratos</h2>

  <p class="meta">
    Generado: {{ now()->format('d/m/Y H:i') }}
    @if(!empty($q)) | Filtro: "{{ $q }}" @endif
    | Orden: {{ $sort }} ({{ $dir }})
    | Registros: {{ $contratos->count() }}
  </p>

  @php
    $fmtNum = fn($n) => is_numeric($n) ? number_format((float)$n, 2, ',', '.') : '—';
  @endphp

  <table>
    <thead>
      <tr>
        <th style="width:70px;">Nro</th>
        <th style="width:85px;">Forward</th>
        <th style="width:80px;">Fecha</th>
        <th>Cliente</th>
        <th style="width:110px;">Campaña</th>
        <th style="width:110px;">Cultivo</th>
        {{-- ✅ NUEVAS --}}
        <th style="width:85px;" class="right">Cant. (tn)</th>
        <th style="width:85px;" class="right">Precio</th>
        <th style="width:200px;">Obs</th>
      </tr>
    </thead>

    <tbody>
      @foreach($contratos as $c)
        <tr>
          <td>{{ $c->nro_contrato }}</td>
          <td>{{ $c->num_forward ?? '—' }}</td>
          <td>{{ optional($c->fecha)->format('d/m/Y') ?? '—' }}</td>
          <td>{{ $c->organizacion->name ?? '—' }}</td>
          <td>{{ $c->campania->name ?? '—' }}</td>
          <td>{{ $c->cultivo->name ?? '—' }}</td>

          {{-- ✅ NUEVAS --}}
          <td class="right">{{ $fmtNum($c->cantidad_tn) }}</td>
          <td class="right">{{ $fmtNum($c->precio) }}</td>
          <td>
            @if(!empty($c->obs))
              {{ $c->obs }}
            @else
              <span class="muted">—</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
