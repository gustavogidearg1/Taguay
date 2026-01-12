@extends('layouts.app')
@section('title','Nueva lluvia')

@section('content')
<div class="container">
  <h1 class="mb-3">Nueva lluvia</h1>

  {{-- ===== Overlay (spinner + mensaje) ===== --}}
  <style>
    .submit-overlay {
      position: fixed; inset: 0; z-index: 1050;
      display: none; align-items: center; justify-content: center;
      background: rgba(255,255,255,.85);
      backdrop-filter: blur(1.5px);
    }
    .submit-card {
      background: #fff; border-radius: 14px; padding: 24px 28px; text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,.2);
      max-width: 380px; width: 92%;
    }
    .submit-card .muted { color:#6c757d; font-size: .95rem; }
  </style>

  <div id="submitOverlay" class="submit-overlay" aria-hidden="true">
    <div class="submit-card" role="alert" aria-live="assertive">
      <div class="spinner-border" role="status" style="width:4rem;height:4rem;">
        <span class="visually-hidden">Cargando…</span>
      </div>
      <h5 class="mt-3 mb-1">Procesando lluvia</h5>
      <div class="muted">
        Guardando la información y enviando el email de aviso…<br>
        Por favor, esperá sin cerrar esta ventana.
      </div>
    </div>
  </div>

  <form id="lluvia-form" method="POST" action="{{ route('lluvias.store') }}" enctype="multipart/form-data" class="row g-3">
    @csrf
    @include('abm.lluvias._form')

    <div class="col-12 d-flex justify-content-end">
      <a href="{{ route('lluvias.index') }}" class="btn btn-outline-secondary me-2">Volver</a>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div>

<script>
(function () {
  const overlay = document.getElementById('submitOverlay');
  const form = document.getElementById('lluvia-form');
  if (!form || !overlay) return;

  const restoreButtons = () => {
    form.querySelectorAll('button, input[type="submit"]').forEach(el => {
      el.disabled = false;
      if (el.type === 'submit' && el.dataset._oldText) {
        el.innerHTML = el.dataset._oldText;
        delete el.dataset._oldText;
      }
    });
  };

  form.addEventListener('submit', function (e) {
    // Si el browser detecta inválidos, no muestres overlay
    if (!form.checkValidity()) return;

    // Deshabilita botones y cambia texto del submit
    form.querySelectorAll('button, input[type="submit"]').forEach(el => {
      el.disabled = true;
      if (el.type === 'submit') {
        el.dataset._oldText = el.innerHTML;
        el.innerHTML = 'Enviando…';
      }
    });

    overlay.style.display = 'flex';
    overlay.setAttribute('aria-hidden', 'false');
  });

  // Si Laravel volvió con errores de validación, ocultar overlay y restaurar botones
  @if ($errors->any())
  window.addEventListener('DOMContentLoaded', () => {
    overlay.style.display = 'none';
    overlay.setAttribute('aria-hidden', 'true');
    restoreButtons();
  });
  @endif
})();
</script>
@endsection
