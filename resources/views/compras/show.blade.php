@extends('layouts.app')

@section('title','Compra '.$compra->id)

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  <div class="card mat-card mb-3">
    <div class="card-header mat-header d-flex align-items-center flex-wrap gap-2">
      <h3 class="mat-title mb-0">
        <i class="fa-solid fa-cart-shopping me-2"></i> Compra
        <span class="text-muted ms-2">#{{ $compra->id }}</span>
      </h3>

      <div class="ms-auto d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-primary btn-mat" href="{{ route('compras.edit', $compra) }}">
          <i class="fa-solid fa-pen me-1"></i> Editar
        </a>
        <a class="btn btn-light btn-mat" href="{{ route('compras.index') }}">
          <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-3">
          <div class="text-muted small">Fecha</div>
          <div class="fw-semibold">{{ optional($compra->fecha)->format('d/m/Y') }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Fecha entrega</div>
          <div class="fw-semibold">{{ optional($compra->fecha_entrega)->format('d/m/Y') ?: '—' }}</div>
        </div>
        <div class="col-12 col-md-6">
          <div class="text-muted small">Organización</div>
          <div class="fw-semibold">{{ $compra->organizacion?->name }}</div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Campaña</div>
          <div class="fw-semibold">{{ $compra->campania?->name }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Condición pago</div>
          <div class="fw-semibold">{{ $compra->condicionPago?->name }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Momento pago</div>
          <div class="fw-semibold">{{ optional($compra->momento_pago)->format('d/m/Y') ?: '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Activo</div>
          <div>
            @if($compra->activo)
              <span class="badge text-bg-success">Sí</span>
            @else
              <span class="badge text-bg-secondary">No</span>
            @endif
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="text-muted small">Moneda</div>
          <div class="fw-semibold">{{ $compra->moneda?->name }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Moneda financ.</div>
          <div class="fw-semibold">{{ $compra->monedaFin?->name ?: '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Tasa financ.</div>
          <div class="fw-semibold">{{ $compra->tasa_financ ?? '—' }}</div>
        </div>
        <div class="col-12 col-md-3">
          <div class="text-muted small">Lugar entrega</div>
          <div class="fw-semibold">{{ $compra->lugar_entrega ?: '—' }}</div>
        </div>

        <div class="col-12">
          <div class="text-muted small">Obs</div>
          <div>{{ $compra->obs ?: '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mat-card">
    <div class="card-header mat-header d-flex align-items-center">
      <h5 class="mat-title mb-0"><i class="fa-solid fa-list me-2"></i> Detalle</h5>
    </div>
    <div class="card-body">

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Producto</th>
              <th style="width:120px;">Cant.</th>
              <th style="width:120px;">Unidad</th>
              <th style="width:140px;">Precio</th>
              <th style="width:160px;">Moneda</th>
              <th style="width:140px;">Venc.</th>
              <th style="width:120px;">SubTotal</th>
            </tr>
          </thead>
          <tbody>
            @php $total = 0; @endphp
            @foreach($compra->subCompras as $s)
              @php $total += (float)($s->sub_total ?? 0); @endphp
              <tr>
                <td class="fw-semibold">{{ $s->producto?->name }}</td>
                <td>{{ $s->cantidad }}</td>
                <td>{{ $s->unidad?->corta ?? $s->unidad?->name }}</td>
                <td>{{ $s->precio }}</td>
                <td>{{ $s->moneda?->name }}</td>
                <td>{{ optional($s->fecha_venc)->format('d/m/Y') ?: '—' }}</td>
                <td class="fw-bold">{{ $s->sub_total }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-end">
        <div class="alert alert-light border mb-0" style="min-width:260px;">
          <div class="d-flex justify-content-between">
            <span><strong>Total</strong></span>
            <span class="fw-bold">{{ number_format($total, 2, '.', '') }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>



@endsection
