@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Nueva Hacienda</h1>

  <form id="hacienda-form" method="POST" action="{{ route('haciendas.store') }}">
    @csrf

    @include('abm.haciendas._form', [
      'entry' => $entry,
      'categorias' => $categorias,
      'establecimientos' => $establecimientos
    ])
  </form>
</div>

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

<div id="submitOverlay" class="submit-overlay">
  <div class="submit-card">
    {{-- <img src="{{ asset('images/sending-email.gif') }}" alt="" style="max-width:140px;height:auto;margin-bottom:12px;"> --}}
    <div class="spinner-border" role="status" style="width:4rem;height:4rem;"></div>
    <h5 class="mt-3 mb-1">Procesando Hacienda</h5>
    <div class="muted">
      Guardando la información y enviando el email de aviso…<br>
      Por favor, esperá sin cerrar esta ventana.
    </div>
  </div>
</div>

<script>
(function () {
  const overlay = document.getElementById('submitOverlay');
  const form = document.getElementById('hacienda-form') || document.querySelector('form');
  if (!form) return;

  const restoreButtons = () => {
    form.querySelectorAll('button, input[type="submit"]').forEach(el => {
      el.disabled = false;
      if (el.type === 'submit' && el.dataset._oldText) {
        el.innerHTML = el.dataset._oldText;
        delete el.dataset._oldText;
      }
    });
  };

  form.addEventListener('submit', function () {
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
  });

  // Si Laravel vuelve con errores de validación, ocultar overlay y restaurar botones
  @if ($errors->any())
  window.addEventListener('DOMContentLoaded', () => {
    overlay.style.display = 'none';
    restoreButtons();
  });
  @endif
})();
</script>
@endsection
