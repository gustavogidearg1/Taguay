<h2>Contrato {{ $accion }}</h2>

<p><strong>Nro Contrato:</strong> {{ $contrato->nro_contrato }}</p>
<p><strong>Num Forward:</strong> {{ $contrato->num_forward ?? '—' }}</p>
<p><strong>Fecha:</strong> {{ optional($contrato->fecha)->format('d/m/Y') }}</p>

<p><strong>Cliente:</strong>  {{ $contrato->cliente_nombre }}</p>
<p><strong>Campaña:</strong> {{ $contrato->campania->name ?? '' }}</p>
<p><strong>Cultivo:</strong> {{ $contrato->cultivo->name ?? '' }}</p>
<p><strong>Cantidad (tn):</strong> {{ $contrato->cantidad_tn ?? '' }}</p>
<p><strong>Precio:</strong> {{ $contrato->precio ?? '' }}</p>

<p><strong>Entrega:</strong>
  {{ optional($contrato->entrega_inicial)->format('d/m/Y') }} →
  {{ optional($contrato->entrega_final)->format('d/m/Y') }}
</p>

<p><strong>Importante:</strong> {{ $contrato->importante ?? '—' }}</p>

<p>Podés ver/imprimir el contrato desde el sistema.</p>
