{{-- MODAL CARGANDO (backdrop estático) --}}
<div class="modal fade" id="modalCargando" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body d-flex align-items-center gap-3">
        <div class="spinner-border" role="status" aria-hidden="true"></div>
        <div>
          <div class="fw-semibold">Enviando datos…</div>
          <div class="text-muted small">Guardando y enviando el correo.</div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contrato-form');
  if (!form) return;

  form.addEventListener('submit', () => {
    const modalEl = document.getElementById('modalCargando');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>
@endpush
