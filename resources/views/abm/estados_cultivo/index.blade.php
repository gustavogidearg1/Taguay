@extends('layouts.app')

@section('title', 'Estados de Cultivo')

@section('content')
<div class="page-wrap">
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card mat-card">
                <div class="card-header header-mint py-3">
                    <div class="mat-header">
                        <h3 class="mat-title">
                            <i class="fa-solid fa-plus me-2"></i> Nuevo estado
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('estados-cultivo.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required maxlength="100">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1" checked>
                            <label class="form-check-label" for="activo">
                                Activo
                            </label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary btn-mat">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card mat-card">
                <div class="card-header header-mint py-3">
                    <div class="mat-header">
                        <h3 class="mat-title">
                            <i class="fa-solid fa-seedling me-2"></i> Estados de Cultivo
                        </h3>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Activo</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($estados as $estado)
                                    <tr>
                                        <td class="fw-semibold">{{ $estado->nombre }}</td>
                                        <td>
                                            @if($estado->activo)
                                                <span class="badge bg-success">Sí</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $estado->id }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            <form action="{{ route('estados-cultivo.destroy', $estado) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar este estado?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editModal{{ $estado->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('estados-cultivo.update', $estado) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar estado</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nombre</label>
                                                            <input type="text" name="nombre" class="form-control" value="{{ $estado->nombre }}" required maxlength="100">
                                                        </div>

                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="activo" id="activo{{ $estado->id }}" value="1" {{ $estado->activo ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="activo{{ $estado->id }}">
                                                                Activo
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay estados cargados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $estados->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
