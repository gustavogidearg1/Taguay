@php
  // helper para inputs numéricos (deja el valor como "1.234,56" en pantalla)
  $fmt = function($v){
    return $v === null || $v === '' ? '' : number_format((float)$v, 2, ',', '.');
  };
@endphp

<div class="row g-3">

{{-- Tipo de crédito --}}
<div class="col-12 col-md-3">
  <label class="form-label">Tipo de crédito</label>

  <select name="tipo_credito" class="form-select">
    @php $tc = old('tipo_credito', $item->tipo_credito ?? ''); @endphp

    <option value="" {{ $tc === '' ? 'selected' : '' }}>Sin tipo</option>
    <option value="Banco"  {{ $tc === 'Banco' ? 'selected' : '' }}>Banco</option>
    <option value="Tarjeta"{{ $tc === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
    <option value="SGR"    {{ $tc === 'SGR' ? 'selected' : '' }}>SGR</option>
    <option value="Linea Bancaria" {{ $tc === 'Linea Bancaria' ? 'selected' : '' }}>Línea bancaria</option>
    <option value="Prestamo" {{ $tc === 'Prestamo' ? 'selected' : '' }}>Préstamo</option>
    <option value="Acuerdo Cta Cte" {{ $tc === 'Acuerdo Cta Cte' ? 'selected' : '' }}>Acuerdo Cta. Cte.</option>
  </select>

</div>


  <div class="col-12 col-md-5">
    <label class="form-label">Proveedor financiero *</label>
    <select name="financiera_id" class="form-select" required>
      <option value="">Seleccionar...</option>
      @foreach($financieras as $f)
        <option value="{{ $f->id }}"
          {{ (string)old('financiera_id', $item->financiera_id ?? '') === (string)$f->id ? 'selected' : '' }}>
          {{ $f->name }} @if($f->tipo) ({{ $f->tipo }}) @endif
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-6 col-md-2">
    <label class="form-label">Fecha</label>
    <input type="date" name="fecha" class="form-control"
           value="{{ old('fecha', $item->fecha ?? '') }}">
  </div>

  <div class="col-6 col-md-2">
    <label class="form-label">Vencimiento</label>
    <input type="date" name="vencimiento" class="form-control"
           value="{{ old('vencimiento', $item->vencimiento ?? '') }}">
  </div>

  <hr class="my-2">

  {{-- Totales --}}
  <div class="col-12"><div class="fw-semibold text-muted">Totales</div></div>

  <div class="col-12 col-md-4">
    <label class="form-label">Calificación total $</label>
    <input type="text" name="calif_total_pesos" class="form-control"
           value="{{ old('calif_total_pesos', $fmt($item->calif_total_pesos ?? null)) }}"
           placeholder="Ej: 2.500.000.000,00">
  </div>
  <div class="col-12 col-md-4">
    <label class="form-label">Calificación total USD</label>
    <input type="text" name="calif_total_usd" class="form-control"
           value="{{ old('calif_total_usd', $fmt($item->calif_total_usd ?? null)) }}"
           placeholder="Ej: 2.500.000.000,00">
  </div>
  <div class="col-12 col-md-4">
    <label class="form-label">Sola firma</label>
    <input type="text" name="sola_firma" class="form-control"
           value="{{ old('sola_firma', $fmt($item->sola_firma ?? null)) }}">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Usado total $</label>
    <input type="text" name="usado_total_pesos" class="form-control"
           value="{{ old('usado_total_pesos', $fmt($item->usado_total_pesos ?? null)) }}">
  </div>
  <div class="col-12 col-md-4">
    <label class="form-label">Usado total USD</label>
    <input type="text" name="usado_total_usd" class="form-control"
           value="{{ old('usado_total_usd', $fmt($item->usado_total_usd ?? null)) }}">
  </div>
  <div class="col-12 col-md-4">
    <label class="form-label">Obs de firma</label>
    <input type="text" name="obs_firma" class="form-control" maxlength="100"
           value="{{ old('obs_firma', $item->obs_firma ?? '') }}"
           placeholder="Ej: Requiere aval / firma conjunta...">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Disponible total $</label>
    <input type="text" name="disp_total_pesos" class="form-control"
           value="{{ old('disp_total_pesos', $fmt($item->disp_total_pesos ?? null)) }}">
  </div>
  <div class="col-12 col-md-4">
    <label class="form-label">Disponible total USD</label>
    <input type="text" name="disp_total_usd" class="form-control"
           value="{{ old('disp_total_usd', $fmt($item->disp_total_usd ?? null)) }}">
  </div>

  <hr class="my-2">

  {{-- Líneas principales (bloque compacto) --}}
  <div class="col-12"><div class="fw-semibold text-muted">Líneas / Productos</div></div>

  @php
    $pairs = [
      ['Préstamo Inmediato Pesos (Largo plazo)', 'prest_inm_pesos_lp_usado', 'prest_inm_pesos_lp_disp'],
      ['Préstamo Inmediato Pesos (Corto plazo)', 'prest_inm_pesos_cp_usado', 'prest_inm_pesos_cp_disp'],
      ['Acuerdo descubierto Cta. Cte.', 'acuerdo_descubierto_ctacte_usado', 'acuerdo_descubierto_ctacte_disp'],
      ['Préstamo Inmediato USD (Largo plazo)', 'prest_inm_usd_lp_usado', 'prest_inm_usd_lp_disp'],
      ['Préstamo Inmediato USD (Corto plazo)', 'prest_inm_usd_cp_usado', 'prest_inm_usd_cp_disp'],
      ['Financiaciones Galicia Nera USD (Corto plazo)', 'fin_galicia_nera_usd_cp_usado', 'fin_galicia_nera_usd_cp_disp'],
      ['Financiaciones Galicia Nera $ (Corto plazo)', 'fin_galicia_nera_pesos_cp_usado', 'fin_galicia_nera_pesos_cp_disp'],
      ['Prendarios $', 'prendarios_pesos_usado', 'prendarios_pesos_disp'],
      ['Prendarios USD', 'prendarios_usd_usado', 'prendarios_usd_disp'],
      ['Garantizados SGR $', 'garant_sgr_pesos_usado', 'garant_sgr_pesos_disp'],
      ['Garantizados SGR USD', 'garant_sgr_usd_usado', 'garant_sgr_usd_disp'],
    ];
  @endphp

  @foreach($pairs as [$label, $used, $avail])
    <div class="col-12">
      <div class="card border-0 bg-light">
        <div class="card-body py-2">
          <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4 fw-semibold">{{ $label }}</div>
            <div class="col-6 col-md-4">
              <label class="form-label mb-1">Usado</label>
              <input type="text" name="{{ $used }}" class="form-control"
                     value="{{ old($used, $fmt(data_get($item ?? null, $used))) }}"
                     placeholder="Ej: 81.250.000,00">
            </div>
            <div class="col-6 col-md-4">
              <label class="form-label mb-1">Disponible</label>
              <input type="text" name="{{ $avail }}" class="form-control"
                     value="{{ old($avail, $fmt(data_get($item ?? null, $avail))) }}"
                     placeholder="Ej: 81.250.000,00">
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach

  <hr class="my-2">

  <div class="col-12 col-md-6">
    <label class="form-label">Imagen</label>
    <input type="file" name="imagen" class="form-control" accept="image/*">
    @if(!empty($item?->imagen))
      <div class="mt-2">
        <a href="{{ asset('storage/'.$item->imagen) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-image me-1"></i> Ver imagen actual
        </a>
      </div>
    @endif
  </div>

  <div class="col-12">
    <label class="form-label">Observación</label>
    <textarea name="observacion" class="form-control" rows="3"
              placeholder="Notas generales, criterios del banco, condiciones, etc.">{{ old('observacion', $item->observacion ?? '') }}</textarea>
  </div>

</div>
