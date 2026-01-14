@extends('layouts.app')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Editar Usuario</span>
      <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text"
                 class="form-control @error('name') is-invalid @enderror"
                 name="name"
                 value="{{ old('name', $user->name) }}"
                 required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email"
                 class="form-control @error('email') is-invalid @enderror"
                 name="email"
                 value="{{ old('email', $user->email) }}"
                 required>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">
            Nueva Contraseña
            <small class="text-muted">(dejá en blanco si no querés cambiar)</small>
          </label>
          <input type="password"
                 class="form-control @error('password') is-invalid @enderror"
                 name="password">
          @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Confirmar Nueva Contraseña</label>
          <input type="password" class="form-control" name="password_confirmation">
        </div>

        {{-- ROL (Spatie) --}}
        @php($selectedRole = old('roles.0', ($currentRoles[0] ?? 'cliente')))

        <div class="mb-3">
          <label class="form-label">Rol (base)</label>
          <select class="form-select @error('roles') is-invalid @enderror" name="roles[]">
            @foreach($roles as $role)
              <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>
                {{ $role->name }}
              </option>
            @endforeach
          </select>
          @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Se guarda con syncRoles().</div>
        </div>

        {{-- PERMISOS / ÁREAS --}}
        @php($selectedPerms = old('permissions', $currentPerms ?? []))
        @php($labels = [
          'ver_agricola'  => 'Agrícola',
          'ver_ganadero'  => 'Ganadero',
          'ver_comercial' => 'Comercial',
        ])

        <div class="mb-3">
          <label class="form-label">Áreas habilitadas</label>

          <div class="d-flex flex-wrap gap-3">
            @foreach(($perms ?? collect()) as $perm)
              <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="permissions[]"
                       id="perm_{{ $perm->name }}"
                       value="{{ $perm->name }}"
                       @checked(in_array($perm->name, $selectedPerms))>

                <label class="form-check-label" for="perm_{{ $perm->name }}">
                  {{ $labels[$perm->name] ?? $perm->name }}
                </label>
              </div>
            @endforeach
          </div>

          @error('permissions') <div class="text-danger small">{{ $message }}</div> @enderror
          <div class="form-text">
            Permisos directos del usuario (syncPermissions). Los permisos heredados por rol también habilitan el menú.
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2"></i> Actualizar
          </button>
        </div>
      </form>

      <hr>


    </div>
  </div>
</div>
@endsection
