@php
  $c = $compra ?? null;

  $fechaDefault = old('fecha', optional($c?->fecha)->format('Y-m-d') ?? now()->format('Y-m-d'));
  $fechaEntregaDefault = old('fecha_entrega', optional($c?->fecha_entrega)->format('Y-m-d') ?? '');

  // Defaults en create
  $defaultMonedaId = $defaultMonedaId ?? null;

  // Orgs para modal (sin API)
  $orgsForJs = ($organizaciones ?? collect())->map(function($o){
    return [
      'id' => $o->id,
      'codigo' => $o->codigo,
      'name' => $o->name,
      'activo' => (bool)$o->activo,
    ];
  })->values();

  // Productos para modal (sin API)
  $prodsForJs = ($productos ?? collect())->map(function($p){
    return [
      'id' => $p->id,
      'codigo' => $p->codigo,
      'name' => $p->name,
      'unidad_id' => $p->unidad_id, // para autocompletar unidad
    ];
  })->values();

  $unidadesForJs = ($unidades ?? collect())->map(function($u){
    return [
      'id' => $u->id,
      'name' => $u->name,
      'codigo' => $u->codigo,
      'corta' => $u->corta,
    ];
  })->values();

  $monedasForJs = ($monedas ?? collect())->map(function($m){
    return [
      'id' => $m->id,
      'name' => $m->name,
      'codigo' => $m->codigo ?? null,
    ];
  })->values();

  // Subcompras desde old() o relación (edit)
  $subs = old(
    'sub_compras',
    isset($compra) ? ($compra->subCompras?->toArray() ?? []) : []
  );
@endphp

{{-- =========================
  CABECERA
========================= --}}
<div class="row g-3">

  <div class="col-12 col-md-2">
    <label class="form-label">Fecha *</label>
    <input type="date" name="fecha" class="form-control" required value="{{ $fechaDefault }}">
  </div>

  <div class="col-12 col-md-2">
    <label class="form-label">Fecha entrega</label>
    <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control"
           value="{{ $fechaEntregaDefault }}">
    <div class="form-text text-muted">Se usa como vencimiento por defecto en líneas.</div>
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Campaña *</label>
    <select name="campania_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($campanias as $ca)
        <option value="{{ $ca->id }}"
          @selected((string)old('campania_id', $c->campania_id ?? '') === (string)$ca->id)>
          {{ $ca->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Condición de pago *</label>
    <select name="condicion_pago_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($condiciones as $cp)
        <option value="{{ $cp->id }}"
          @selected((string)old('condicion_pago_id', $c->condicion_pago_id ?? '') === (string)$cp->id)>
          {{ $cp->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-2">
    <label class="form-label">Momento pago</label>
    <input type="date" name="momento_pago" class="form-control"
           value="{{ old('momento_pago', optional($c?->momento_pago)->format('Y-m-d') ?? '') }}">
  </div>

</div>

<hr class="my-4">

{{-- =========================
  ORGANIZACIÓN + CÓDIGO
========================= --}}
<div class="row g-3">

  <div class="col-12 col-lg-7">
    <label class="form-label">Organización <span class="text-danger">*</span></label>

    <div class="d-flex flex-column flex-md-row gap-2">
      <select id="organizacion_select" name="organizacion_id" class="form-select" required>
        <option value="">— Seleccionar organización —</option>
        @foreach($organizaciones as $o)
          <option value="{{ $o->id }}"
            @selected((string)old('organizacion_id', $c->organizacion_id ?? '') === (string)$o->id)>
            {{ $o->name }}
          </option>
        @endforeach
      </select>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-mat"
                data-bs-toggle="modal" data-bs-target="#organizacionModal">
          <i class="bi bi-search"></i>
          <span class="d-none d-sm-inline">Buscar</span>
        </button>

        <button type="button" id="clearOrganizacionBtn" class="btn btn-outline-danger btn-mat">
          <i class="bi bi-x-lg"></i>
          <span class="d-none d-sm-inline">Quitar</span>
        </button>
      </div>
    </div>

    <div class="form-text text-muted">
      Elegí del listado o usá <strong>Buscar</strong> por código o nombre.
    </div>
  </div>

  <div class="col-12 col-md-3 col-lg-3 d-none">
    <label class="form-label">Código *</label>
    <input type="text" name="codigo" class="form-control" maxlength="50" value="{{ old('codigo', $c->codigo ?? '') }}"
           placeholder="Ej: COMP-2026-0001">
  </div>

  <div class="col-12 col-md-2 col-lg-2">
    <label class="form-label d-block">Activo</label>
    <div class="form-check mt-2">
      <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
             @checked(old('activo', $c ? (bool)$c->activo : true))>
      <label class="form-check-label" for="activo">Sí</label>
    </div>
  </div>

</div>

<hr class="my-4">

{{-- =========================
  MONEDAS + FINANCIACIÓN
========================= --}}
<div class="row g-3">
  <div class="col-12 col-md-3">
    <label class="form-label">Moneda * (cabecera)</label>
    <select name="moneda_id" id="moneda_header" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($monedas as $m)
        <option value="{{ $m->id }}"
          @selected((string)old('moneda_id', $c->moneda_id ?? ($defaultMonedaId ?? '')) === (string)$m->id)>
          {{ $m->name }}
        </option>
      @endforeach
    </select>
    <div class="form-text text-muted">Se usa como moneda por defecto en líneas.</div>
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Moneda financiamiento</label>
    <select name="moneda_fin_id" class="form-select">
      <option value="">—</option>
      @foreach($monedas as $m)
        <option value="{{ $m->id }}"
          @selected((string)old('moneda_fin_id', $c->moneda_fin_id ?? '') === (string)$m->id)>
          {{ $m->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-2">
    <label class="form-label">Tasa financ.</label>
    <input type="number" step="0.000001" min="0" name="tasa_financ" class="form-control"
           value="{{ old('tasa_financ', $c->tasa_financ ?? '') }}"
           placeholder="Ej: 0.0005">
    <div class="form-text text-muted">0.05% = 0.0005</div>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Lugar entrega</label>
    <input type="text" name="lugar_entrega" class="form-control" maxlength="100"
           value="{{ old('lugar_entrega', $c->lugar_entrega ?? '') }}">
  </div>


</div>

<hr class="my-4">

{{-- =========================
  SUB COMPRAS
========================= --}}
<div class="card mat-card">
  <div class="card-header mat-header d-flex align-items-center flex-wrap gap-2">
    <h5 class="mat-title mb-0">
      <i class="fa-solid fa-list me-2"></i> Detalle (Sub compras)
    </h5>
    <button type="button" class="btn btn-success btn-mat ms-auto" id="btnAddSubCompra">
      <i class="fa-solid fa-plus me-1"></i> Agregar línea
    </button>
  </div>

  <div class="card-body">

    <div id="subcompras-container">

      @forelse($subs as $i => $s)
        @php
          $sid = $s['id'] ?? '';
          $prodId = old("sub_compras.$i.producto_id", $s['producto_id'] ?? '');
          $cant = old("sub_compras.$i.cantidad", $s['cantidad'] ?? '0');
          $unidadId = old("sub_compras.$i.unidad_id", $s['unidad_id'] ?? '');
          $precio = old("sub_compras.$i.precio", $s['precio'] ?? '0');
          $monedaId = old("sub_compras.$i.moneda_id", $s['moneda_id'] ?? old('moneda_id', $c->moneda_id ?? ($defaultMonedaId ?? '')));
          $fechaV = old("sub_compras.$i.fecha_venc", isset($s['fecha_venc']) ? \Carbon\Carbon::parse($s['fecha_venc'])->format('Y-m-d') : (old('fecha_entrega', optional($c?->fecha_entrega)->format('Y-m-d') ?? '') ));
          $b1 = old("sub_compras.$i.bonificacion_1", $s['bonificacion_1'] ?? 0);
          $b2 = old("sub_compras.$i.bonificacion_2", $s['bonificacion_2'] ?? 0);
          $b3 = old("sub_compras.$i.bonificacion_3", $s['bonificacion_3'] ?? 0);
          $subt = old("sub_compras.$i.sub_total", $s['sub_total'] ?? 0);
        @endphp

        <div class="subcompra-item border rounded p-3 mb-3 position-relative" data-index="{{ $i }}">
          <button type="button" class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btnRemoveSubCompra">
            <i class="fa-solid fa-trash"></i>
          </button>

          <input type="hidden" name="sub_compras[{{ $i }}][id]" value="{{ $sid }}">

          <div class="row g-3">

            {{-- Producto --}}
            <div class="col-12 col-lg-5">
              <label class="form-label">Producto *</label>
              <div class="d-flex flex-column flex-md-row gap-2">
                <input type="hidden" name="sub_compras[{{ $i }}][producto_id]" class="producto_id" value="{{ $prodId }}" required>

                <input type="text" class="form-control producto_label" readonly
                       value="@php
                         $p = ($productos ?? collect())->firstWhere('id', (int)$prodId);
                         echo $p ? ($p->name.' ('.$p->codigo.')') : '';
                       @endphp"
                       placeholder="Seleccionar producto...">

                <button type="button" class="btn btn-success btn-mat btnBuscarProducto">
                  <i class="bi bi-search"></i> Buscar
                </button>

                <button type="button" class="btn btn-outline-danger btn-mat btnClearProducto">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
              <div class="form-text text-muted">Usá el buscador. Autocompleta unidad si el producto la tiene.</div>
            </div>

            <div class="col-6 col-lg-2">
              <label class="form-label">Cantidad *</label>
              <input type="number" step="0.01" min="0" name="sub_compras[{{ $i }}][cantidad]"
                     class="form-control cantidad" value="{{ $cant }}" required>
            </div>

            <div class="col-6 col-lg-2">
              <label class="form-label">Unidad *</label>
              <select name="sub_compras[{{ $i }}][unidad_id]" class="form-select unidad_id" required>
                <option value="">Seleccionar…</option>
                @foreach($unidades as $u)
                  <option value="{{ $u->id }}" @selected((string)$unidadId === (string)$u->id)>
                    {{ $u->name }} ({{ $u->corta ?? $u->codigo }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-6 col-lg-2">
              <label class="form-label">Precio *</label>
              <input type="number" step="0.01" min="0" name="sub_compras[{{ $i }}][precio]"
                     class="form-control precio" value="{{ $precio }}" required>
            </div>

            <div class="col-6 col-lg-1">
              <label class="form-label">Moneda *</label>
              <select name="sub_compras[{{ $i }}][moneda_id]" class="form-select moneda_id" required>
                <option value="">—</option>
                @foreach($monedas as $m)
                  <option value="{{ $m->id }}" @selected((string)$monedaId === (string)$m->id)>
                    {{ $m->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-lg-2">
              <label class="form-label">Fecha venc.</label>
              <input type="date" name="sub_compras[{{ $i }}][fecha_venc]" class="form-control fecha_venc"
                     value="{{ $fechaV }}">
            </div>

            <div class="col-4 col-lg-2">
              <label class="form-label">Bonif. 1</label>
              <input type="number" step="0.0001" min="0" name="sub_compras[{{ $i }}][bonificacion_1]"
                     class="form-control bon1" value="{{ $b1 }}">
            </div>

            <div class="col-4 col-lg-2">
              <label class="form-label">Bonif. 2</label>
              <input type="number" step="0.0001" min="0" name="sub_compras[{{ $i }}][bonificacion_2]"
                     class="form-control bon2" value="{{ $b2 }}">
            </div>

            <div class="col-4 col-lg-2">
              <label class="form-label">Bonif. 3</label>
              <input type="number" step="0.0001" min="0" name="sub_compras[{{ $i }}][bonificacion_3]"
                     class="form-control bon3" value="{{ $b3 }}">
            </div>

            <div class="col-12 col-lg-3 ms-auto">
              <label class="form-label">Sub Total</label>
              <input type="text" class="form-control sub_total_ui" readonly value="{{ number_format((float)$subt, 2, '.', '') }}">
              <input type="hidden" name="sub_compras[{{ $i }}][sub_total]" class="sub_total" value="{{ $subt }}">
            </div>

          </div>
        </div>

      @empty
        <div class="text-muted" id="emptySubHint">
          No hay líneas cargadas. Tocá <strong>Agregar línea</strong>.
        </div>
      @endforelse

    </div>

    <hr>
  <div class="col-12">
    <label class="form-label">Obs</label>
    <input type="text" name="obs" class="form-control" maxlength="200"
           value="{{ old('obs', $c->obs ?? '') }}">
  </div>
    <div class="mt-3 d-flex justify-content-end">
      <div class="alert alert-light border mb-0" style="min-width:260px;">
        <div class="d-flex justify-content-between align-items-center">
          <span><strong>Total</strong></span>
          <span class="fw-bold" id="total_ui">0.00</span>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- =========================
  TEMPLATE SUBCOMPRA
========================= --}}
<template id="subcompra-template">
  <div class="subcompra-item border rounded p-3 mb-3 position-relative" data-index="__INDEX__">
    <button type="button" class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btnRemoveSubCompra">
      <i class="fa-solid fa-trash"></i>
    </button>

    <input type="hidden" name="sub_compras[__INDEX__][id]" value="">

    <div class="row g-3">

      <div class="col-12 col-lg-5">
        <label class="form-label">Producto *</label>
        <div class="d-flex flex-column flex-md-row gap-2">
          <input type="hidden" name="sub_compras[__INDEX__][producto_id]" class="producto_id" value="" required>
          <input type="text" class="form-control producto_label" readonly value="" placeholder="Seleccionar producto...">
          <button type="button" class="btn btn-success btn-mat btnBuscarProducto">
            <i class="bi bi-search"></i> Buscar
          </button>
          <button type="button" class="btn btn-outline-danger btn-mat btnClearProducto">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <label class="form-label">Cantidad *</label>
        <input type="number" step="0.01" min="0" name="sub_compras[__INDEX__][cantidad]"
               class="form-control cantidad" value="0" required>
      </div>

      <div class="col-6 col-lg-2">
        <label class="form-label">Unidad *</label>
        <select name="sub_compras[__INDEX__][unidad_id]" class="form-select unidad_id" required>
          <option value="">Seleccionar…</option>
          @foreach($unidades as $u)
            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->corta ?? $u->codigo }})</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-lg-2">
        <label class="form-label">Precio *</label>
        <input type="number" step="0.01" min="0" name="sub_compras[__INDEX__][precio]"
               class="form-control precio" value="0" required>
      </div>

      <div class="col-6 col-lg-1">
        <label class="form-label">Moneda *</label>
        <select name="sub_compras[__INDEX__][moneda_id]" class="form-select moneda_id" required>
          <option value="">—</option>
          @foreach($monedas as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-lg-2">
        <label class="form-label">Fecha venc.</label>
        <input type="date" name="sub_compras[__INDEX__][fecha_venc]" class="form-control fecha_venc" value="">
      </div>

      <div class="col-4 col-lg-2">
        <label class="form-label">Bonif. 1</label>
        <input type="number" step="0.0001" min="0" name="sub_compras[__INDEX__][bonificacion_1]"
               class="form-control bon1" value="0">
      </div>

      <div class="col-4 col-lg-2">
        <label class="form-label">Bonif. 2</label>
        <input type="number" step="0.0001" min="0" name="sub_compras[__INDEX__][bonificacion_2]"
               class="form-control bon2" value="0">
      </div>

      <div class="col-4 col-lg-2">
        <label class="form-label">Bonif. 3</label>
        <input type="number" step="0.0001" min="0" name="sub_compras[__INDEX__][bonificacion_3]"
               class="form-control bon3" value="0">
      </div>

      <div class="col-12 col-lg-3 ms-auto">
        <label class="form-label">Sub Total</label>
        <input type="text" class="form-control sub_total_ui" readonly value="0.00">
        <input type="hidden" name="sub_compras[__INDEX__][sub_total]" class="sub_total" value="0">
      </div>

    </div>
  </div>
</template>

{{-- =========================
  MODAL BUSCAR ORGANIZACION
========================= --}}
<div class="modal fade" id="organizacionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content mat-card">
      <div class="modal-header mat-header">
        <h5 class="modal-title mat-title">
          <i class="bi bi-building"></i> Buscar organización
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="organizacionSearchInput" type="text" class="form-control" placeholder="Código o nombre…">
        </div>

        <div id="organizacionResults" class="list-group"></div>

        <div id="organizacionEmpty" class="text-center py-4 text-muted">
          <i class="bi bi-inbox"></i> Escribí para buscar
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

{{-- =========================
  MODAL BUSCAR PRODUCTO
========================= --}}
<div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content mat-card">
      <div class="modal-header mat-header">
        <h5 class="modal-title mat-title">
          <i class="bi bi-box-seam"></i> Buscar producto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="productoSearchInput" type="text" class="form-control" placeholder="Código o nombre…">
        </div>

        <div id="productoResults" class="list-group"></div>

        <div id="productoEmpty" class="text-center py-4 text-muted">
          <i class="bi bi-inbox"></i> Escribí para buscar
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  // ===================== DATA =====================
  const allOrgs = @json($orgsForJs ?? []);
  const allProds = @json($prodsForJs ?? []);
  const allUnidades = @json($unidadesForJs ?? []);
  const allMonedas = @json($monedasForJs ?? []);

  // ===================== ORGANIZACION =====================
  const orgSel = document.getElementById('organizacion_select');
  const clearOrgBtn = document.getElementById('clearOrganizacionBtn');

  if (orgSel && clearOrgBtn) {
    clearOrgBtn.addEventListener('click', () => {
      orgSel.value = '';
      orgSel.dispatchEvent(new Event('change', { bubbles:true }));
    });
  }

  const orgModalEl = document.getElementById('organizacionModal');
  const orgInputEl = document.getElementById('organizacionSearchInput');
  const orgListEl  = document.getElementById('organizacionResults');
  const orgEmptyEl = document.getElementById('organizacionEmpty');

  function debounce(fn, ms=200){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), ms); }; }

  function renderOrg(rows){
    if (!rows.length) {
      orgListEl.innerHTML = '';
      orgEmptyEl.style.display = 'block';
      orgEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Sin resultados';
      return;
    }
    orgEmptyEl.style.display = 'none';
    orgListEl.innerHTML = rows.map(o => `
      <button type="button" class="list-group-item list-group-item-action" data-id="${o.id}">
        <div class="fw-semibold">${o.name || ''}</div>
        <div class="small text-muted">${o.codigo || ''}</div>
      </button>
    `).join('');
  }

  function doOrgSearch(q){
    const term = (q||'').trim().toLowerCase();
    if (!term) {
      orgListEl.innerHTML = '';
      orgEmptyEl.style.display = 'block';
      orgEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
      return;
    }
    const rows = allOrgs.filter(o =>
      String(o.codigo||'').toLowerCase().includes(term) ||
      String(o.name||'').toLowerCase().includes(term)
    ).slice(0, 200);

    renderOrg(rows);
  }

  if (orgModalEl && orgInputEl && orgListEl && orgEmptyEl && orgSel) {
    const deb = debounce(doOrgSearch, 200);

    orgModalEl.addEventListener('show.bs.modal', () => {
      orgInputEl.value = '';
      orgListEl.innerHTML = '';
      orgEmptyEl.style.display = 'block';
      orgEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
      setTimeout(()=>orgInputEl.focus(), 250);
    });

    orgInputEl.addEventListener('input', e => deb(e.target.value));

    orgListEl.addEventListener('click', (e) => {
      const btn = e.target.closest('button.list-group-item');
      if (!btn) return;
      const id = String(btn.dataset.id || '');
      const opt = Array.from(orgSel.options).find(o => String(o.value) === id);
      if (opt) orgSel.value = id;
      bootstrap.Modal.getInstance(orgModalEl)?.hide();
    });
  }

  // ===================== SUBCOMPRAS =====================
  const cont = document.getElementById('subcompras-container');
  const btnAdd = document.getElementById('btnAddSubCompra');
  const tpl = document.getElementById('subcompra-template');
  const emptyHint = document.getElementById('emptySubHint');

  const headerMoneda = document.getElementById('moneda_header');
  const headerFechaEntrega = document.getElementById('fecha_entrega');

  let currentRowEl = null; // fila sobre la que se selecciona producto

  function num(v){
    const n = parseFloat(String(v ?? '').replace(',', '.'));
    return isNaN(n) ? 0 : n;
  }

  function money(n){
    return (Number(n || 0)).toFixed(2);
  }

  function calcRow(row){
    const cant = num(row.querySelector('.cantidad')?.value);
    const precio = num(row.querySelector('.precio')?.value);
    const b1 = num(row.querySelector('.bon1')?.value);
    const b2 = num(row.querySelector('.bon2')?.value);
    const b3 = num(row.querySelector('.bon3')?.value);

    const factor = (1 - b1) * (1 - b2) * (1 - b3);
    const st = (cant * precio * factor);

    row.querySelector('.sub_total_ui').value = money(st);
    row.querySelector('.sub_total').value = money(st);
  }

  function calcTotal(){
    const rows = cont.querySelectorAll('.subcompra-item');
    let total = 0;
    rows.forEach(r => { total += num(r.querySelector('.sub_total')?.value); });
    document.getElementById('total_ui').textContent = money(total);
  }

  function calcAll(){
    cont.querySelectorAll('.subcompra-item').forEach(calcRow);
    calcTotal();
  }

  function applyDefaultsToRow(row){
    // moneda por defecto = header moneda
    if (headerMoneda) {
      const mid = headerMoneda.value;
      const sel = row.querySelector('.moneda_id');
      if (sel && mid && !sel.value) sel.value = mid;
    }

    // venc por defecto = fecha_entrega
    if (headerFechaEntrega) {
      const fv = headerFechaEntrega.value;
      const inp = row.querySelector('.fecha_venc');
      if (inp && fv && !inp.value) inp.value = fv;
    }
  }

  function bindRow(row){
    applyDefaultsToRow(row);

    row.addEventListener('input', (e) => {
      if (
        e.target.matches('.cantidad') ||
        e.target.matches('.precio') ||
        e.target.matches('.bon1') ||
        e.target.matches('.bon2') ||
        e.target.matches('.bon3')
      ) {
        calcRow(row);
        calcTotal();
      }
    });

    row.querySelector('.btnRemoveSubCompra')?.addEventListener('click', () => {
      row.remove();
      calcTotal();
    });

    row.querySelector('.btnBuscarProducto')?.addEventListener('click', () => {
      currentRowEl = row;
      new bootstrap.Modal(document.getElementById('productoModal')).show();
    });

    row.querySelector('.btnClearProducto')?.addEventListener('click', () => {
      row.querySelector('.producto_id').value = '';
      row.querySelector('.producto_label').value = '';
    });

    // calc inicial
    calcRow(row);
  }

  if (cont && btnAdd && tpl) {
    // bind rows existentes
    cont.querySelectorAll('.subcompra-item').forEach(bindRow);

    let index = cont.querySelectorAll('.subcompra-item').length;

    btnAdd.addEventListener('click', () => {
      if (emptyHint) emptyHint.remove();
      const html = tpl.innerHTML.replaceAll('__INDEX__', String(index));
      cont.insertAdjacentHTML('beforeend', html);

      const row = cont.querySelector('.subcompra-item[data-index="'+index+'"]');
      bindRow(row);

      index++;
      calcTotal();
    });
  }

  // defaults masivos si cambian en cabecera
  if (headerMoneda) {
    headerMoneda.addEventListener('change', () => {
      const mid = headerMoneda.value;
      cont.querySelectorAll('.subcompra-item .moneda_id').forEach(sel => {
        if (!sel.value) sel.value = mid; // solo si estaba vacío
      });
    });
  }

  if (headerFechaEntrega) {
    headerFechaEntrega.addEventListener('change', () => {
      const fv = headerFechaEntrega.value;
      cont.querySelectorAll('.subcompra-item .fecha_venc').forEach(inp => {
        if (!inp.value) inp.value = fv; // solo si estaba vacío
      });
    });
  }

  // ===================== MODAL PRODUCTO =====================
  const prodModalEl = document.getElementById('productoModal');
  const prodInputEl = document.getElementById('productoSearchInput');
  const prodListEl  = document.getElementById('productoResults');
  const prodEmptyEl = document.getElementById('productoEmpty');

  function renderProds(rows){
    if (!rows.length) {
      prodListEl.innerHTML = '';
      prodEmptyEl.style.display = 'block';
      prodEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Sin resultados';
      return;
    }
    prodEmptyEl.style.display = 'none';
    prodListEl.innerHTML = rows.map(p => `
      <button type="button" class="list-group-item list-group-item-action" data-id="${p.id}">
        <div class="fw-semibold">${p.name || ''}</div>
        <div class="small text-muted">${p.codigo || ''}</div>
      </button>
    `).join('');
  }

  function doProdSearch(q){
    const term = (q||'').trim().toLowerCase();
    if (!term) {
      prodListEl.innerHTML = '';
      prodEmptyEl.style.display = 'block';
      prodEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
      return;
    }
    const rows = allProds.filter(p =>
      String(p.codigo||'').toLowerCase().includes(term) ||
      String(p.name||'').toLowerCase().includes(term)
    ).slice(0, 300);
    renderProds(rows);
  }

  if (prodModalEl && prodInputEl && prodListEl && prodEmptyEl) {
    const deb2 = debounce(doProdSearch, 200);

    prodModalEl.addEventListener('show.bs.modal', () => {
      prodInputEl.value = '';
      prodListEl.innerHTML = '';
      prodEmptyEl.style.display = 'block';
      prodEmptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
      setTimeout(()=>prodInputEl.focus(), 250);
    });

    prodInputEl.addEventListener('input', e => deb2(e.target.value));

    prodListEl.addEventListener('click', (e) => {
      const btn = e.target.closest('button.list-group-item');
      if (!btn || !currentRowEl) return;

      const id = String(btn.dataset.id || '');
      const p = allProds.find(x => String(x.id) === id);
      if (!p) return;

      // set producto
      currentRowEl.querySelector('.producto_id').value = p.id;
      currentRowEl.querySelector('.producto_label').value = `${p.name} (${p.codigo})`;

      // autocompletar unidad si viene en producto
      if (p.unidad_id) {
        const selU = currentRowEl.querySelector('.unidad_id');
        if (selU) selU.value = String(p.unidad_id);
      }

      bootstrap.Modal.getInstance(prodModalEl)?.hide();
    });
  }

  // total inicial
  calcAll();
});
</script>
@endpush
