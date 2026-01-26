@extends('layouts.app')

@section('title','Nueva organización')

@push('styles')
<style>
  .card.mat-card{ border:0; border-radius:16px; box-shadow:0 10px 30px rgba(15,23,42,.08); }
  .mat-header{ display:flex; align-items:center; gap:.75rem; padding:.9rem 1.1rem; border-bottom:1px solid rgba(15,23,42,.06); }
  .mat-title{ margin:0; font-weight:700; font-size:1.05rem; }
  .mat-header-actions{ margin-left:auto; display:flex; gap:.5rem; align-items:center; }
  .btn-mat{ border-radius:10px; padding:.45rem .75rem; }
</style>
@endpush

@section('content')

{{-- Header simple (sin x-page-header) --}}
<div class="container mb-3">
  <div class="d-flex align-items-center gap-2">
    <i class="bi bi-plus-circle fs-4 text-primary"></i>
    <h1 class="h4 mb-0">Nueva organización</h1>
  </div>
</div>

{{-- Flash / Errores (sin x-flash) --}}
<div class="container">
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
      <i class="bi bi-check-circle"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="bi bi-x-circle"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Revisá los campos:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif
</div>

<div class="container">
  <div class="card mat-card">
    <div class="mat-header">
      <h3 class="mat-title">Crear</h3>

      <div class="mat-header-actions ms-auto">
        <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary btn-mat">
          <i class="bi bi-arrow-left"></i> Volver
        </a>
      </div>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('organizaciones.store') }}">
        @csrf


        @include('abm.organizaciones._form', ['organizacion' => null])

        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary btn-mat">Cancelar</a>
          <button class="btn btn-primary btn-mat" type="submit">
            <i class="bi bi-check2"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
