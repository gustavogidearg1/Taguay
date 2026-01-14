@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<div class="container">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Lista de Usuarios</h5>
      <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Crear Usuario
      </a>
    </div>

    <div class="card-body">
      @php
        function sortIcon($field, $sort, $order) {
          if ($field === $sort) {
            return $order === 'asc' ? 'fa fa-sort-alpha-down' : 'fa fa-sort-alpha-up';
          }
          return 'fa fa-sort';
        }
      @endphp

      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>
              <a href="{{ route('users.index', ['sort' => 'name', 'order' => $sort === 'name' && $order === 'asc' ? 'desc' : 'asc']) }}"
                 class="text-decoration-none text-dark">
                Nombre <i class="{{ sortIcon('name', $sort, $order) }}"></i>
              </a>
            </th>

            <th>
              <a href="{{ route('users.index', ['sort' => 'email', 'order' => $sort === 'email' && $order === 'asc' ? 'desc' : 'asc']) }}"
                 class="text-decoration-none text-dark">
                Email <i class="{{ sortIcon('email', $sort, $order) }}"></i>
              </a>
            </th>

            <th>
              <a href="{{ route('users.index', ['sort' => 'role', 'order' => $sort === 'role' && $order === 'asc' ? 'desc' : 'asc']) }}"
                 class="text-decoration-none text-dark">
                Rol <i class="{{ sortIcon('role', $sort, $order) }}"></i>
              </a>
            </th>

            <th style="width: 220px;">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @foreach($users as $user)
            @php
              $roleNames = $user->roles?->pluck('name')->values() ?? collect();
              $primaryRole = $roleNames->first() ?? '—';
            @endphp

            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                <span class="badge bg-secondary">{{ $primaryRole }}</span>
                @if($roleNames->count() > 1)
                  <span class="text-muted small ms-1">(+{{ $roleNames->count()-1 }})</span>
                @endif
              </td>
              <td>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Editar</a>

                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="btn btn-sm btn-danger"
                          onclick="return confirm('¿Eliminar este usuario?')">
                    Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
