@extends('layouts.app')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">Editar Usuario</div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.update', $user->id) }}" autocomplete="off">
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
      <small class="text-muted">(solo cambiarla)</small>
    </label>
    <input type="password"
           class="form-control @error('password') is-invalid @enderror"
           name="password"
           autocomplete="new-password">
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="mb-3">
    <label class="form-label">Confirmar Nueva Contraseña</label>
    <input type="password"
           class="form-control"
           name="password_confirmation"
           autocomplete="new-password">
  </div>



        {{-- ROL BASE --}}
        <div class="mb-3">
          <label class="form-label">Rol (base)</label>
          @php
            $selectedRole = old('roles.0', $currentRoles[0] ?? 'cliente');
          @endphp
          <select class="form-select" name="roles[]">
            @foreach($roles as $role)
              <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>
                {{ $role->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- PERMISOS / ÁREAS --}}
        <div class="mb-3">
          <label class="form-label">Áreas habilitadas</label>

          @php
            $selectedPerms = old('permissions', $currentPerms ?? []);
            $labels = [
              'ver_agricola'  => 'Agrícola',
              'ver_ganadero'  => 'Ganadero',
              'ver_comercial' => 'Comercial',
            ];
          @endphp

          <div class="d-flex flex-wrap gap-3">
            @foreach($perms as $perm)
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
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
      </form>
    </div>
  </div>
</div>
@endsection
