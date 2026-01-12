@extends('layouts.app')
@section('title','Lluvias')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Lluvias</h1>
    <a href="{{ route('lluvias.create') }}" class="btn btn-primary">+ Nuevo</a>
  </div>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
      <label class="form-label">Establecimiento</label>
      <select name="establecimiento_id" class="form-select">
        <option value="">Todos</option>
        @foreach($establecimientos as $e)
          <option value="{{ $e->id }}" @selected(request('establecimiento_id')==$e->id)>{{ $e->nombre }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Desde</label>
<input type="date" name="desde" value="{{ $desde ?? request('desde') }}" class="form-control">

    </div>
    <div class="col-md-2">
      <label class="form-label">Hasta</label>
<input type="date" name="hasta" value="{{ $hasta ?? request('hasta') }}" class="form-control">

    </div>

    <div class="col-md-2 d-grid">
      <button class="btn btn-outline-secondary">Filtrar</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Establecimiento</th>
          <th class="text-end">mm</th>
          <th>Creado por</th> {{-- nueva columna --}}
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->fecha?->format('d/m/Y') }}</td>
            {{-- ✅ hora como datetime --}}
            <td>{{ $r->hora ? $r->hora->format('H:i') : '—' }}</td>
            <td>{{ $r->establecimiento->nombre ?? '—' }}</td>
            <td class="text-end">{{ number_format($r->mm,1,',','.') }}</td>

            {{-- usuario creador --}}
            <td>
              {{ $r->user->name ?? $r->user->email ?? '—' }}
            </td>

            <td class="text-end">
              <a href="{{ route('lluvias.show',$r) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
              <a href="{{ route('lluvias.edit',$r) }}" class="btn btn-sm btn-outline-primary">Editar</a>
              <form action="{{ route('lluvias.destroy',$r) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center">Sin registros</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $rows->links() }}
  
  {{-- ===== Gráficos ===== --}}
<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="mb-0">Acumulado por Establecimiento (mm)</h6>
        </div>
        <canvas id="chartEstablecimientos" height="140"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="mb-0">Acumulado por Mes (mm)</h6>
        </div>
        <canvas id="chartMes" height="140"></canvas>
      </div>
    </div>
  </div>
</div>



</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
  const porEstablecimiento = @json($porEstablecimiento);
  const porMes = @json($porMes);

  Chart.register(window.ChartDataLabels);

  const opcionesBarra = {
    responsive: true,
    plugins: {
      legend: { display: false },
      datalabels: {
        anchor: 'end',
        align: 'top',
        color: '#000',
        font: { weight: 'bold' },
        formatter: (v) => (Number(v) || 0).toLocaleString('es-AR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })
      },
      tooltip: {
        callbacks: {
          label: (ctx) => ` ${ctx.raw.toLocaleString('es-AR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} mm`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: (v) => v
        },
        afterDataLimits: (scale) => { scale.max = scale.max * 1.1; }
      }
    }
  };

  // 1) Por establecimiento
  new Chart(document.getElementById('chartEstablecimientos'), {
    type: 'bar',
    data: {
      labels: porEstablecimiento.map(i => i.establecimiento),
      datasets: [{
        data: porEstablecimiento.map(i => i.mm),
      }]
    },
    options: opcionesBarra,
    plugins: [window.ChartDataLabels]
  });

  // 2) Por mes
  new Chart(document.getElementById('chartMes'), {
    type: 'bar',
    data: {
      labels: porMes.map(i => i.mes),
      datasets: [{
        data: porMes.map(i => i.mm),
      }]
    },
    options: opcionesBarra,
    plugins: [window.ChartDataLabels]
  });
</script>


@endsection
