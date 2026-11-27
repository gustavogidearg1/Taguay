@extends('layouts.app')

@section('content')
<div class="container py-3">
  <div class="card mat-card">
    <div class="card-header header-mint py-3">
      <h3 class="mat-title mb-0"><i class="fa-solid fa-user-plus me-2"></i> Crear Nuevo Usuario</h3>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
        @csrf

        <div class="row g-3 mb-4">
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   name="name"
                   value="{{ old('name') }}"
                   required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}"
                   autocomplete="off"
                   required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Teléfono para cargar Avance de Campo por WhatsApp (ver docs/whatsapp/tutorial-whatsapp.md) --}}
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold"><i class="bi bi-whatsapp text-success me-1"></i> Teléfono WhatsApp</label>
            <input type="tel"
                   class="form-control @error('telefono_whatsapp') is-invalid @enderror"
                   name="telefono_whatsapp"
                   value="{{ old('telefono_whatsapp') }}"
                   placeholder="+54 9 3534 191741"
                   autocomplete="off">
            @error('telefono_whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Opcional. Habilita a este usuario a cargar el Avance de Campo por WhatsApp (necesita rol Agrícola).</div>
          </div>
          <div class="col-12 col-md-6 d-none d-md-block"></div>

          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password"
                   autocomplete="new-password"
                   required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Confirmar contraseña</label>
            <input type="password" class="form-control" name="password_confirmation"
                   autocomplete="new-password" required>
          </div>
        </div>

        @include('users._accesos', [
          'selectedRoles'    => old('roles', []),
          'selectedReportes' => old('reportes', []),
        ])

        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
