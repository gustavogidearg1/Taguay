@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Haciendas</h1>
    <a class="btn btn-primary" href="{{ route('haciendas.create') }}">+ Nueva</a>
  </div>

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <input type="text" name="s" value="{{ request('s') }}" class="form-control" placeholder="Buscar por cliente, vendedor, destino, patente...">
    </div>
    <div class="col-md-2">
      <button class="btn btn-outline-secondary w-100">Buscar</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Categoría</th>
          <th>Establecimiento</th>
          <th>Cantidad</th>
          <th>Peso vivo (-8%)</th>
          <th>Subtotal Pv x Cant.</th>
          <th>Fecha</th>
          <th>Creado por</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->cliente }}</td>
            <td>{{ $r->categoria?->nombre }}</td>
            <td>{{ $r->establecimiento?->nombre }}</td>
            <td>{{ number_format($r->cantidad, 1, ',', '.') }}</td>

            {{-- Fecha de creación --}}
            <td>
              {{ $r->peso_vivo_menos_8 !== null ? number_format($r->peso_vivo_menos_8, 1, ',', '.') : '—' }}
            </td>

            <td>{{ number_format($r->subtotal_peso_vivo, 1, ',', '.') }}</td>

            <td>
              {{ $r->created_at ? $r->created_at->format('d/m/Y') : '—' }}
            </td>

            <td>{{ $r->user->name ?? $r->user->email ?? '—' }}</td>

            <td class="text-end">
              <a href="{{ route('haciendas.show', $r->id) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
              <a href="{{ route('haciendas.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">Editar</a>
              <form action="{{ route('haciendas.destroy', $r->id) }}" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center">Sin registros</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $rows->links() }}
</div>
@endsection
