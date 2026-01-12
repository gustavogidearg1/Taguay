@extends('layouts.app')
@section('title','Detalle lluvia')

@section('content')
@php
  // URL pública del archivo (tu fix de public/storage hace que esto funcione)
  $fileUrl = $lluvia->archivo_path ? asset('storage/'.$lluvia->archivo_path) : null;

  // Detectar extensión
  $ext = $lluvia->archivo_path ? strtolower(pathinfo($lluvia->archivo_path, PATHINFO_EXTENSION)) : null;

  // Extensiones consideradas imagen
  $isImage = in_array($ext, ['jpg','jpeg','png','webp']);

  // PDF
  $isPdf = ($ext === 'pdf');
@endphp

<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Detalle de lluvia</h1>
    <a href="{{ route('lluvias.index') }}" class="btn btn-outline-secondary">Volver</a>
  </div>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row g-3">
    {{-- ====== Detalle ====== --}}
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Fecha</dt>
            <dd class="col-sm-8">{{ $lluvia->fecha?->format('d/m/Y') }}</dd>

            <dt class="col-sm-4">Hora</dt>
            <dd class="col-sm-8">{{ $lluvia->hora ? $lluvia->hora->format('H:i') : '—' }}</dd>

            <dt class="col-sm-4">Establecimiento</dt>
            <dd class="col-sm-8">{{ $lluvia->establecimiento->nombre ?? '—' }}</dd>

            <dt class="col-sm-4">Milímetros</dt>
            <dd class="col-sm-8">{{ number_format($lluvia->mm,1,',','.') }}</dd>

            <dt class="col-sm-4 d-none">Fuente</dt>
            <dd class="col-sm-8 d-none">{{ ucfirst($lluvia->fuente) }}</dd>

            @if($lluvia->estacion_nombre)
              <dt class="col-sm-4">Estación</dt>
              <dd class="col-sm-8">{{ $lluvia->estacion_nombre }}</dd>
            @endif

            @if($lluvia->lat || $lluvia->lng)
              <dt class="col-sm-4">Ubicación</dt>
              <dd class="col-sm-8">Lat {{ $lluvia->lat }} / Lng {{ $lluvia->lng }}</dd>
            @endif

            @if($lluvia->observador)
              <dt class="col-sm-4">Observador</dt>
              <dd class="col-sm-8">{{ $lluvia->observador }}</dd>
            @endif

            @if($lluvia->comentario)
              <dt class="col-sm-4">Comentario</dt>
              <dd class="col-sm-8">{{ $lluvia->comentario }}</dd>
            @endif

            {{-- Link general si hay archivo --}}
            <dt class="col-sm-4">Archivo</dt>
            <dd class="col-sm-8">
              @if($fileUrl)
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener">
                  Ver archivo
                </a>
                <small class="text-muted d-block">({{ $ext ?? '—' }})</small>
              @else
                —
              @endif
            </dd>
          </dl>
        </div>
      </div>

      {{-- Reenviar mail (lo dejé igual, pero lo ocultás con d-none) --}}
      <form action="{{ route('lluvias.resendMail', $lluvia) }}" method="POST" class="d-inline mt-3"
            onsubmit="return confirm('¿Reenviar correo de esta lluvia?');">
        @csrf
        <button class="btn btn-outline-primary d-none">
          Reenviar correo
        </button>
      </form>
    </div>

    {{-- ====== Card Imagen / Sin imagen ====== --}}
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
          <strong>Imagen</strong>

          @if($fileUrl)
            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
              Abrir
            </a>
          @endif
        </div>

        <div class="card-body">
          @if(!$fileUrl)
            <div class="text-muted">Sin imagen</div>

          @elseif($isImage)
            <img
              src="{{ $fileUrl }}"
              alt="Imagen lluvia"
              class="img-fluid rounded border"
              style="max-height: 420px; width: 100%; object-fit: contain;"
              loading="lazy"
              onerror="this.closest('.card-body').innerHTML = '<div class=&quot;alert alert-warning mb-0&quot;>No se pudo cargar la imagen.</div>';"
            />
            <small class="text-muted d-block mt-2">
              {{ basename($lluvia->archivo_path) }}
            </small>

          @elseif($isPdf)
            <div class="alert alert-info mb-0">
              El archivo es un PDF. Usá el botón <strong>Abrir</strong> para verlo.
            </div>

          @else
            <div class="alert alert-secondary mb-0">
              Archivo adjunto ({{ $ext ?? '—' }}). Usá <strong>Abrir</strong> para verlo.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
