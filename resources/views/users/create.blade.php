@extends('layouts.app')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">Crear Nuevo Usuario</div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror"
                 name="name" value="{{ old('name') }}" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror"
                 name="email" value="{{ old('email') }}" required>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" class="form-control @error('password') is-invalid @enderror"
                 name="password" required>
          @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Confirmar Contraseña</label>
          <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        {{-- ✅ ROL (Spatie) --}}
        <div class="mb-3">
          <label class="form-label">Rol (base)</label>
          @php $selectedRole = old('roles.0', 'cliente'); @endphp

          <select class="form-select @error('roles') is-invalid @enderror" name="roles[]">
            @foreach($roles as $role)
              <option value="{{ $role->name }}" @selected($selectedRole === $role->name)>
                {{ $role->name }}
              </option>
            @endforeach
          </select>

          @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- ✅ PERMISOS / ÁREAS --}}
        <div class="mb-3">
          <label class="form-label">Áreas habilitadas</label>

          @php
            $selectedPerms = old('permissions', []);
            $labels = [
              'ver_agricola'  => 'Agrícola',
              'ver_ganadero'  => 'Ganadero',
              'ver_comercial' => 'Comercial',
            ];
          @endphp

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
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
      </form>
    </div>
  </div>
</div>
@endsection
