@extends('layouts.app')

@section('content')
<div class="container py-3">
  <div class="card mat-card">
    <div class="card-header header-mint py-3">
      <h3 class="mat-title mb-0"><i class="fa-solid fa-user-pen me-2"></i> Editar Usuario</h3>
    </div>

    <div class="card-body">
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('users.update', $user->id) }}" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email', $user->email) }}"
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
                   value="{{ old('telefono_whatsapp', $user->telefono_whatsapp ? '+' . $user->telefono_whatsapp : '') }}"
                   placeholder="+54 9 3534 191741"
                   autocomplete="off">
            @error('telefono_whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Opcional. Habilita a este usuario a cargar el Avance de Campo por WhatsApp (necesita rol Agrícola).</div>
          </div>
        </div>

        {{-- CONTRASEÑA: solo se cambia si se tilda el interruptor.
             Los campos quedan deshabilitados para que el navegador no los autocomplete. --}}
        @php $cambiarPassword = (bool) old('cambiar_password'); @endphp
        <div class="mb-4 p-3 border rounded-3 bg-light">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" role="switch"
                   name="cambiar_password" id="cambiar_password" value="1"
                   @checked($cambiarPassword)>
            <label class="form-check-label fw-semibold" for="cambiar_password">
              Cambiar contraseña
            </label>
            <div class="form-text mt-0">Si no lo tildás, el usuario sigue con la contraseña que ya tenía.</div>
          </div>

          <div id="passwordCampos" class="row g-3 mt-1 {{ $cambiarPassword ? '' : 'd-none' }}">
            <div class="col-12 col-md-6">
              <label class="form-label">Nueva contraseña</label>
              <input type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password"
                     autocomplete="new-password"
                     @disabled(! $cambiarPassword)>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Confirmar nueva contraseña</label>
              <input type="password"
                     class="form-control"
                     name="password_confirmation"
                     autocomplete="new-password"
                     @disabled(! $cambiarPassword)>
            </div>
          </div>
        </div>

        @include('users._accesos', [
          'selectedRoles'    => old('roles', $currentRoles),
          'selectedReportes' => old('reportes', $currentReportes),
        ])

        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Muestra/oculta y habilita/deshabilita los campos de contraseña
  document.getElementById('cambiar_password').addEventListener('change', function () {
    const campos = document.getElementById('passwordCampos');
    campos.classList.toggle('d-none', !this.checked);
    campos.querySelectorAll('input').forEach(function (input) {
      input.disabled = !this.checked;
      if (!this.checked) input.value = '';
    }, this);
  });
</script>
@endsection
