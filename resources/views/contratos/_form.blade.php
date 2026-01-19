{{-- resources/views/contratos/_form.blade.php --}}

@php
  // En create, $contrato puede venir null
  $c = $contrato ?? null;

  // Defaults razonables
  $fechaDefault = old('fecha', optional($c?->fecha)->format('Y-m-d') ?? now()->format('Y-m-d'));

  // Data para el buscador (sin API)
  $orgsForJs = ($organizaciones ?? collect())
    ->map(function($o){
      return [
        'id'     => $o->id,
        'codigo' => $o->codigo,
        'name'   => $o->name,
        'activo' => (bool) $o->activo,
      ];
    })
    ->values();

  // ===== Opciones de selects (claves abreviadas que se guardan en BD) =====
  $optCaracteristica = [
    'PRECIO_HECHO' => 'Precio hecho',
    'A_FIJAR'      => 'A fijar',
    'CONDICIONAL'  => 'Condicional',
  ];

  $optFormacion = [
    'A_COBRAR'     => 'A cobrar',
    'CON_ANTICIPO' => 'Con anticipo',
    'EN_CANJE'     => 'En canje',
    'FORWARD'      => 'Forward',
  ];

  $optCondicionPrecio = [
    'ENTREGA_OBL' => 'Entrega obligatoria',
    'WASHOUT'     => 'Washout',
  ];

  $optCondicionPago = [
    'A_COBRAR'     => 'A cobrar',
    'CON_ANTICIPO' => 'Con anticipo',
    'EN_CANJE'     => 'En canje',
  ];

  $optListaGrano = [
    'ABIERTA' => 'Abierta',
    'CERRADA' => 'Cerrada',
  ];

  $optDestino = [
    'GRANO'      => 'Grano',
    'OTRO_GRANO' => 'Otro grano',
  ];

  $optFormato = [
    'FORWARD'    => 'Forward',
    'DISPONIBLE' => 'Disponible',
  ];

  $optDisponibleTipo = [
    'PRECIO_HECHO' => 'Precio hecho',
    'A_FIJAR'      => 'A fijar',
  ];
@endphp

{{-- ===========================
   DATOS PRINCIPALES
=========================== --}}
<div class="row g-3">

  <div class="col-3 col-md-3">
    <label class="form-label">Nro Contrato *</label>
    <input type="number" name="nro_contrato" class="form-control" required min="1"
           value="{{ old('nro_contrato', $c->nro_contrato ?? '') }}">
  </div>

  <div class="col-3 col-md-3">
    <label class="form-label">Num Forward</label>
    <input type="number" name="num_forward" class="form-control" min="1"
           value="{{ old('num_forward', $c->num_forward ?? '') }}">
  </div>


<div class="col-3 col-md-3">
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

  <div class="col-3 col-md-3">
    <label class="form-label">Cultivo *</label>
    <select name="cultivo_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($cultivos as $cu)
        <option value="{{ $cu->id }}"
          @selected((string)old('cultivo_id', $c->cultivo_id ?? '') === (string)$cu->id)>
          {{ $cu->name }}
        </option>
      @endforeach
    </select>
  </div>


</div>

<div class="row g-3">

  <div class="col-3 col-md-3">
    <label class="form-label">Fecha *</label>
    <input type="date" name="fecha" class="form-control" required value="{{ $fechaDefault }}">
  </div>


 <div class="col-3 col-md-3">
    <label class="form-label">Entrega Inicial</label>
    <input type="date" name="entrega_inicial" class="form-control"
           value="{{ old('entrega_inicial', optional($c?->entrega_inicial)->format('Y-m-d') ?? '') }}">
  </div>

  <div class="col-3 col-md-3">
    <label class="form-label">Entrega Final</label>
    <input type="date" name="entrega_final" class="form-control"
           value="{{ old('entrega_final', optional($c?->entrega_final)->format('Y-m-d') ?? '') }}">
  </div>

  <div class="col-3 col-md-3">
    <label class="form-label">Vendedor</label>
    <input type="text" name="vendedor" class="form-control" maxlength="120"
           value="{{ old('vendedor', $c->vendedor ?? 'Taguay') }}">
  </div>


</div>

<div class="row g-3">

  {{-- ===========================
   ORGANIZACIÓN (tabla local + buscador)
=========================== --}}
<div class="row g-3">
  <div class="row g-3">
  <div class="col-12 col-md-8">
    <label class="form-label">Organización <span class="text-danger">*</span></label>

    <div class="input-group">
      <select id="organizacion_select" name="organizacion_id" class="form-select" required>
        <option value="">— Seleccionar organización —</option>
        @foreach($organizaciones as $o)
          <option value="{{ $o->id }}"
            @selected((string)old('organizacion_id', $c->organizacion_id ?? '') === (string)$o->id)>
          {{ $o->name }}
          </option>
        @endforeach
      </select>

      <button type="button"
              class="btn btn-success btn-mat"
              data-bs-toggle="modal"
              data-bs-target="#organizacionModal">
        <i class="bi bi-search"></i>
        <span class="d-none d-sm-inline">Buscar</span>
      </button>

      <button type="button"
              id="clearOrganizacionBtn"
              class="btn btn-outline-danger btn-mat">
        <i class="bi bi-x-lg"></i>
        <span class="d-none d-sm-inline">Quitar</span>
      </button>
    </div>

    <div class="form-text text-muted">
      Elegí del listado o usá <strong>Buscar</strong> para encontrar por código o nombre.
    </div>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Moneda <span class="text-danger">*</span></label>
    <select name="moneda_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($monedas as $m)
        <option value="{{ $m->id }}"
          @selected((string)old('moneda_id', $c->moneda_id ?? ($defaultMonedaId ?? '')) === (string)$m->id)>
          {{ $m->name }}
        </option>
      @endforeach
    </select>
  </div>
</div>

{{-- MODAL BUSCADOR ORGANIZACIONES --}}
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

{{-- ===========================
   MODAL BUSCADOR ORGANIZACIONES
=========================== --}}
<div class="modal fade" id="organizacionModal" tabindex="-1" aria-labelledby="organizacionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content mat-card">
      <div class="modal-header mat-header">
        <h5 class="modal-title mat-title" id="organizacionModalLabel">
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


<div class="row g-3">

  {{-- ===========================
     SELECTS DE NEGOCIO
  =========================== --}}
  <div class="col-3">
    <label class="form-label">Característica de Precio *</label>
    <select name="caracteristica_precio" class="form-select" required>
      @foreach($optCaracteristica as $k => $label)
        <option value="{{ $k }}"
          @selected(old('caracteristica_precio', $c->caracteristica_precio ?? 'PRECIO_HECHO') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-3">
    <label class="form-label">Formación de Precio *</label>
    <select name="formacion_precio" class="form-select" required>
      @foreach($optFormacion as $k => $label)
        <option value="{{ $k }}"
          @selected(old('formacion_precio', $c->formacion_precio ?? 'A_COBRAR') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-3">
    <label class="form-label">Condición de Precio *</label>
    <select name="condicion_precio" class="form-select" required>
      @foreach($optCondicionPrecio as $k => $label)
        <option value="{{ $k }}"
          @selected(old('condicion_precio', $c->condicion_precio ?? 'ENTREGA_OBL') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

    <div class="col-3">
    <label class="form-label">Condición de Pago *</label>
    <select name="condicion_pago" class="form-select" required>
      @foreach($optCondicionPago as $k => $label)
        <option value="{{ $k }}"
          @selected(old('condicion_pago', $c->condicion_pago ?? 'A_COBRAR') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>


</div>

<div class="row g-3">

 <div class="col-3">
    <label class="form-label">Lista de Grano *</label>
    <select name="lista_grano" class="form-select" required>
      @foreach($optListaGrano as $k => $label)
        <option value="{{ $k }}"
          @selected(old('lista_grano', $c->lista_grano ?? 'ABIERTA') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-3">
    <label class="form-label">Destino *</label>
    <select name="destino" class="form-select" required>
      @foreach($optDestino as $k => $label)
        <option value="{{ $k }}"
          @selected(old('destino', $c->destino ?? 'GRANO') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-3">
    <label class="form-label">Formato *</label>
    <select name="formato" class="form-select" required>
      @foreach($optFormato as $k => $label)
        <option value="{{ $k }}"
          @selected(old('formato', $c->formato ?? 'FORWARD') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-3">
    <label class="form-label">Disponible *</label>
    <select name="disponible_tipo" class="form-select" required>
      @foreach($optDisponibleTipo as $k => $label)
        <option value="{{ $k }}"
          @selected(old('disponible_tipo', $c->disponible_tipo ?? 'PRECIO_HECHO') === $k)>
          {{ $label }}
        </option>
      @endforeach
    </select>
  </div>


</div>


  {{-- ===========================
     CANTIDAD / PRECIOS
  =========================== --}}


<div class="row">

  <div class="col-4 col-md-4">
  <label class="form-label">Cantidad / Tn <span class="text-danger">*</span></label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="cantidad_tn"
    id="cantidad_tn"
    class="form-control"
    required
    value="{{ old('cantidad_tn', $c->cantidad_tn ?? 0) }}">
</div>


<div class="col-4 col-md-4">
  <label class="form-label">Precio <span class="text-danger">*</span></label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="precio"
    id="precio"
    class="form-control"
    required
    value="{{ old('precio', $c->precio ?? 0) }}">
</div>

<div class="col-4 col-md-4">
  <label class="form-label">Precio Fijado</label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="precio_fijado"
    id="precio_fijado"
    class="form-control"
    value="{{ old('precio_fijado', $c->precio_fijado ?? 0) }}">
</div>


</div>


  {{-- ===========================
     TEXTOS
  =========================== --}}
  <div class="col-12">
    <label class="form-label">Definición</label>
    <textarea name="definicion" class="form-control">{{ old('definicion', $c->definicion ?? '') }}</textarea>
  </div>

  <div class="row g-3">

 <div class="col-4">
  <label class="form-label">Comisión</label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="comision"
    id="comision"
    class="form-control"
    value="{{ old('comision', $c->comision ?? 0) }}">
</div>

<div class="col-4">
  <label class="form-label">Paritaria</label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="paritaria"
    id="paritaria"
    class="form-control"
    value="{{ old('paritaria', $c->paritaria ?? 0) }}">
</div>

<div class="col-4">
  <label class="form-label">Volatil</label>
  <input
    type="number"
    step="0.01"
    min="0"
    name="volatil"
    id="volatil"
    class="form-control"
    value="{{ old('volatil', $c->volatil ?? 0) }}">
</div>

  </div>




<hr class="my-4">

{{-- ===========================
   SUB-CONTRATOS (HISTORIAL)
=========================== --}}
<div class="card mat-card">
  <div class="card-header mat-header d-flex align-items-center">
    <h5 class="mb-0 mat-title">
      <i class="fa-solid fa-tag me-2"></i> Precio fijación (historial)
    </h5>
    <button type="button" class="btn btn-success btn-mat ms-auto" id="btnAddSubContrato">
      <i class="fa-solid fa-plus me-1"></i> Agregar
    </button>
  </div>

  <div class="card-body">
    <div id="sub-contratos-container">
      @php
        // Para create => vacío | para edit => trae relación | para errores => old()
        $subs = old(
          'sub_contratos',
          isset($contrato) && $contrato?->relationLoaded('subContratos')
            ? $contrato->subContratos->toArray()
            : (isset($contrato) ? $contrato->subContratos->toArray() : [])
        );
      @endphp

      @forelse($subs as $i => $sc)
        <div class="subcontrato-item border rounded p-3 mb-3 position-relative">
          <button type="button"
                  class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btnRemoveSubContrato">
            <i class="fa-solid fa-trash"></i>
          </button>

          {{-- id hidden (solo edit) --}}
          <input type="hidden" name="sub_contratos[{{ $i }}][id]" value="{{ $sc['id'] ?? '' }}">

          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Fecha *</label>
              <input type="date" class="form-control"
                     name="sub_contratos[{{ $i }}][fecha]"
                     value="{{ old("sub_contratos.$i.fecha", isset($sc['fecha']) ? \Carbon\Carbon::parse($sc['fecha'])->format('Y-m-d') : now()->format('Y-m-d')) }}"
                     required>
            </div>

            <div class="col-md-3">
              <label class="form-label">Toneladas</label>
              <input type="number" step="0.01" min="0" class="form-control"
                     name="sub_contratos[{{ $i }}][toneladas]"
                     value="{{ old("sub_contratos.$i.toneladas", $sc['toneladas'] ?? '0.00') }}">
            </div>

            <div class="col-md-3">
              <label class="form-label">Nuevo precio fijación</label>
              <input type="number" step="0.01" min="0" class="form-control"
                     name="sub_contratos[{{ $i }}][nuevo_precio_fijacion]"
                     value="{{ old("sub_contratos.$i.nuevo_precio_fijacion", $sc['nuevo_precio_fijacion'] ?? '0.00') }}">
            </div>

            <div class="col-md-3">
              <label class="form-label">Observación (100)</label>
              <input type="text" maxlength="100" class="form-control"
                     name="sub_contratos[{{ $i }}][observacion]"
                     value="{{ old("sub_contratos.$i.observacion", $sc['observacion'] ?? '') }}">
            </div>
          </div>
        </div>
      @empty
        <div class="text-muted">
          Todavía no hay registros de fijación. Tocá <strong>Agregar</strong> para cargar uno.
        </div>
      @endforelse
    </div>
  </div>
</div>

<hr class="my-4">

<div class="row g-3">
  <div class="col-12">
    <label class="form-label">Obs </label>
    <input type="text" name="obs" class="form-control" maxlength="200"
           value="{{ old('obs', $c->obs ?? '') }}">
  </div>

  </div>

  <div class="row g-3">
  <div class="col-12">
    <label class="form-label">Importante </label>
    <input type="text" name="importante" class="form-control" maxlength="200"
           value="{{ old('importante', $c->importante ?? '') }}">
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-4 ms-auto">
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-0">
      <span><strong>Subtotal</strong> (Precio × Cantidad/Tn)</span>
      <span class="fw-bold" id="subtotal_ui">0.00</span>
    </div>
  </div>
</div>

</div>
{{-- ===========================
   TEMPLATE SUB-CONTRATO
=========================== --}}
<template id="subcontrato-template">
  <div class="subcontrato-item border rounded p-3 mb-3 position-relative">
    <button type="button"
            class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btnRemoveSubContrato">
      <i class="fa-solid fa-trash"></i>
    </button>

    <input type="hidden" name="sub_contratos[__INDEX__][id]" value="">

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Fecha *</label>
        <input type="date" class="form-control" name="sub_contratos[__INDEX__][fecha]" value="{{ now()->format('Y-m-d') }}" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Toneladas</label>
        <input type="number" step="0.01" min="0" class="form-control" name="sub_contratos[__INDEX__][toneladas]" value="0.00">
      </div>

      <div class="col-md-3">
        <label class="form-label">Nuevo precio fijación</label>
        <input type="number" step="0.01" min="0" class="form-control" name="sub_contratos[__INDEX__][nuevo_precio_fijacion]" value="0.00">
      </div>

      <div class="col-md-3">
        <label class="form-label">Observación (100)</label>
        <input type="text" maxlength="100" class="form-control" name="sub_contratos[__INDEX__][observacion]" value="">
      </div>
    </div>
  </div>
</template>

{{-- ===========================
   MODAL BUSCADOR CLIENTES
=========================== --}}
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content mat-card">
      <div class="modal-header mat-header">
        <h5 class="modal-title mat-title" id="clienteModalLabel">
          <i class="bi bi-person-search"></i> Buscar cliente
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  // ===================== ORGANIZACION (modal + select) =====================
  const sel = document.getElementById('organizacion_select');
  const clearBtn = document.getElementById('clearOrganizacionBtn');

  if (sel && clearBtn) {
    clearBtn.addEventListener('click', () => {
      sel.value = '';
      sel.dispatchEvent(new Event('change', { bubbles:true }));
    });
  }

  const modalEl = document.getElementById('organizacionModal');
  const inputEl = document.getElementById('organizacionSearchInput');
  const listEl  = document.getElementById('organizacionResults');
  const emptyEl = document.getElementById('organizacionEmpty');

  // Data desde PHP (sin arrow fn con arrays para evitar ParseError)
  const allOrgs = @json($orgsForJs ?? []);

  if (modalEl && inputEl && listEl && emptyEl && sel) {
    const debounce = (fn, ms=200) => {
      let t;
      return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

function render(rows) {
  if (!rows.length) {
    listEl.innerHTML = '';
    emptyEl.style.display = 'block';
    emptyEl.innerHTML = '<i class="bi bi-inbox"></i> Sin resultados';
    return;
  }

  emptyEl.style.display = 'none';

  listEl.innerHTML = rows.map(o => `
    <button type="button" class="list-group-item list-group-item-action" data-id="${o.id}">
      <div class="fw-semibold">${o.name || ''}</div>
    </button>
  `).join('');
}

    function doSearch(q) {
      const term = (q || '').trim().toLowerCase();
      if (!term) {
        listEl.innerHTML = '';
        emptyEl.style.display = 'block';
        emptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
        return;
      }

      const rows = allOrgs
        .filter(o =>
          String(o.codigo || '').toLowerCase().includes(term) ||
          String(o.name || '').toLowerCase().includes(term)
        )
        .slice(0, 200);

      render(rows);
    }

    const debounced = debounce(doSearch, 200);

    modalEl.addEventListener('show.bs.modal', () => {
      inputEl.value = '';
      listEl.innerHTML = '';
      emptyEl.style.display = 'block';
      emptyEl.innerHTML = '<i class="bi bi-inbox"></i> Escribí para buscar';
      setTimeout(() => inputEl.focus(), 250);
    });

    inputEl.addEventListener('input', (e) => debounced(e.target.value));

    listEl.addEventListener('click', (e) => {
      const btn = e.target.closest('button.list-group-item');
      if (!btn) return;

      const id = String(btn.dataset.id || '');
      const opt = Array.from(sel.options).find(o => String(o.value) === id);
      if (opt) {
        sel.value = id;
        sel.dispatchEvent(new Event('change', { bubbles:true }));
      }

      bootstrap.Modal.getInstance(modalEl)?.hide();
    });
  }

  // ===================== SUB-CONTRATOS (HISTORIAL) =====================
  const container = document.getElementById('sub-contratos-container');
  const btnAdd = document.getElementById('btnAddSubContrato');
  const tpl = document.getElementById('subcontrato-template');

  if (container && btnAdd && tpl) {
    let index = container.querySelectorAll('.subcontrato-item').length;

    btnAdd.addEventListener('click', () => {
      const html = tpl.innerHTML.replaceAll('__INDEX__', String(index));
      const hint = container.querySelector('.text-muted');
      if (hint) hint.remove();

      container.insertAdjacentHTML('beforeend', html);
      index++;
    });

    container.addEventListener('click', (e) => {
      const btn = e.target.closest('.btnRemoveSubContrato');
      if (!btn) return;
      btn.closest('.subcontrato-item')?.remove();
    });
  } else {
    // si querés ver qué falta:
    // console.log({ container, btnAdd, tpl });
  }

  // ===================== SUBTOTAL UI =====================
  const $precio = document.getElementById('precio');
  const $cant   = document.getElementById('cantidad_tn');
  const $ui     = document.getElementById('subtotal_ui');

  if ($precio && $cant && $ui) {
    const num = (v) => {
      const n = parseFloat(String(v ?? '').replace(',', '.'));
      return isNaN(n) ? 0 : n;
    };
    const money = (n) => (Number(n || 0)).toFixed(2);

    function calc(){
      $ui.textContent = money(num($precio.value) * num($cant.value));
    }

    $precio.addEventListener('input', calc);
    $cant.addEventListener('input', calc);
    calc();
  }
});
</script>
@endpush



