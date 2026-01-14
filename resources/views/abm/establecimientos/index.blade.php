@extends('layouts.app')

@section('title', 'Establecimientos')

@section('content')
<div class="container py-3">

    <div class="card mat-card mb-3">
        <div class="card-header mat-header d-flex align-items-center">
            <h3 class="mat-title mb-0">
                <i class="fa-solid fa-building me-2"></i> Establecimientos
            </h3>

            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('establecimientos.create') }}" class="btn btn-primary btn-mat">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('establecimientos.index') }}">
                <div class="col-12 col-md-5">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Nombre o ubicación...">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select name="sort" class="form-select">
                        <option value="nombre"    @selected($sort==='nombre')>Nombre</option>
                        <option value="ubicacion" @selected($sort==='ubicacion')>Ubicación</option>
                        <option value="created_at" @selected($sort==='created_at')>Fecha creación</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">Orden</label>
                    <select name="order" class="form-select">
                        <option value="asc"  @selected($order==='asc')>Asc</option>
                        <option value="desc" @selected($order==='desc')>Desc</option>
                    </select>
                </div>

                <div class="col-6 col-md-1">
                    <label class="form-label">Por pág.</label>
                    <select name="per_page" class="form-select">
                        <option value="10" @selected($perPage==10)>10</option>
                        <option value="20" @selected($perPage==20)>20</option>
                        <option value="50" @selected($perPage==50)>50</option>
                        <option value="100" @selected($perPage==100)>100</option>
                    </select>
                </div>

                <div class="col-6 col-md-1 d-grid">
                    <button class="btn btn-outline-secondary">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th class="text-end" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($establecimientos as $e)
                            <tr>
                                <td class="fw-semibold">{{ $e->nombre }}</td>
                                <td class="text-muted">{{ $e->ubicacion ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('establecimientos.edit', $e) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                    <form action="{{ route('establecimientos.destroy', $e) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar establecimiento?');">
                                        @csrf
                                        @method('DELETE')
                                      <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No hay establecimientos para mostrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $establecimientos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
