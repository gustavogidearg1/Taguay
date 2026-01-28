<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    table{ width:100%; border-collapse:collapse; }
    th,td{ border:1px solid #ccc; padding:6px; }
    th{ background:#f2f2f2; }
    .meta{ color:#666; margin-bottom:10px; }
    .text-end{ text-align:right; }
  </style>
</head>
<body>
  <h2>Compras</h2>
  <p class="meta">
    Generado: {{ now()->format('d/m/Y H:i') }}
    @if(!empty($q)) | Filtro: "{{ $q }}" @endif
    | Orden: {{ $sort }} ({{ $dir }})
  </p>

  <table>
    <thead>
      <tr>
        <th style="width:70px;">ID</th>
        <th style="width:90px;">Fecha</th>
        <th>Organización</th>
        <th style="width:150px;">Campaña</th>
        <th style="width:120px;">Moneda</th>
        <th style="width:140px;">Código</th>
        <th style="width:80px;">Activo</th>
      </tr>
    </thead>
    <tbody>
      @foreach($compras as $c)
        <tr>
          <td>{{ $c->id }}</td>
          <td>{{ optional($c->fecha)->format('d/m/Y') ?? '' }}</td>
          <td>{{ $c->organizacion->name ?? '' }}</td>
          <td>{{ $c->campania->name ?? '' }}</td>
          <td>{{ $c->moneda->name ?? '' }}</td>
          <td>{{ $c->codigo ?? '' }}</td>
          <td>{{ $c->activo ? 'SI' : 'NO' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
