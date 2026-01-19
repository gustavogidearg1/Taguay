<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content mat-card">
      <div class="modal-header mat-header">
        <h5 class="modal-title mat-title" id="clienteModalLabel">
          <i class="bi bi-building"></i> Buscar cliente (Organización)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="clienteSearchInput" type="text" class="form-control" placeholder="Código o nombre…">
        </div>

        <div id="clienteResults" class="list-group"></div>

        <div id="clienteLoading" class="text-center py-3" style="display:none;">
          <div class="spinner-border" role="status"></div>
          <div class="small text-muted mt-2">Buscando…</div>
        </div>

        <div id="clienteEmpty" class="text-center py-4 text-muted" style="display:none;">
          <i class="bi bi-inbox"></i> Sin resultados
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
