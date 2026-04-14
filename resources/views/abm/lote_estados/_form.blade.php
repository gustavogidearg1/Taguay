@csrf

@php
    $selectedEstablecimientoId = old(
        'establecimiento_id',
        isset($registro) && $registro->lote ? $registro->lote->establecimiento_id : ''
    );
@endphp

<div class="row g-3">

    {{-- ESTABLECIMIENTO --}}
    <div class="col-12 col-md-4">
        <label class="form-label">Establecimiento <span class="text-danger">*</span></label>
        <select id="establecimiento_id"
                name="establecimiento_id"
                class="form-select @error('establecimiento_id') is-invalid @enderror"
                required>
            <option value="">Seleccionar...</option>
            @foreach($establecimientos as $establecimiento)
                <option value="{{ $establecimiento->id }}"
                    {{ (string)$selectedEstablecimientoId === (string)$establecimiento->id ? 'selected' : '' }}>
                    {{ $establecimiento->nombre }}
                </option>
            @endforeach
        </select>
        @error('establecimiento_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- LOTE --}}
    <div class="col-12 col-md-5">
        <label class="form-label">Lote <span class="text-danger">*</span></label>
        <select id="lote_id"
                name="lote_id"
                class="form-select @error('lote_id') is-invalid @enderror"
                required>
            <option value="">Seleccionar...</option>
            @foreach($lotes as $lote)
                <option value="{{ $lote->id }}"
                        data-establecimiento="{{ $lote->establecimiento_id }}"
                        data-campania="{{ $lote->campania->name ?? '' }}"
                        data-cultivo="{{ $lote->cultivo->name ?? '' }}"
                    {{ old('lote_id', $registro->lote_id ?? '') == $lote->id ? 'selected' : '' }}>
                    {{ $lote->nombre }} - {{ $lote->campania->name ?? '-' }} - {{ $lote->cultivo->name ?? '-' }}
                </option>
            @endforeach
        </select>
        @error('lote_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- FECHA --}}
    <div class="col-12 col-md-3">
        <label class="form-label">Fecha <span class="text-danger">*</span></label>
        <input type="date"
               name="fecha"
               class="form-control @error('fecha') is-invalid @enderror"
               value="{{ old('fecha', isset($registro) && $registro->fecha ? $registro->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required>
        @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ESTADO --}}
    <div class="col-12 col-md-4">
        <label class="form-label">Estado del cultivo <span class="text-danger">*</span></label>
        <select name="estado_cultivo_id"
                class="form-select @error('estado_cultivo_id') is-invalid @enderror"
                required>
            <option value="">Seleccionar...</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id }}"
                    {{ old('estado_cultivo_id', $registro->estado_cultivo_id ?? '') == $estado->id ? 'selected' : '' }}>
                    {{ $estado->nombre }}
                </option>
            @endforeach
        </select>
        @error('estado_cultivo_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- OBSERVACION --}}
    <div class="col-12 col-md-8">
        <label class="form-label">Observacion</label>
        <textarea name="observacion"
                  rows="2"
                  class="form-control @error('observacion') is-invalid @enderror"
                  placeholder="Detalle adicional del estado, comentarios de campo...">{{ old('observacion', $registro->observacion ?? '') }}</textarea>
        @error('observacion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- DATOS PRODUCTIVOS --}}
    <div class="col-12"><hr></div>

    <div class="col-12 col-md-4">
        <label class="form-label">Produccion (Tn)</label>
        <input type="number"
               step="0.01"
               min="0"
               name="produccion_tn"
               id="produccion_tn"
               class="form-control @error('produccion_tn') is-invalid @enderror"
               value="{{ old('produccion_tn', $registro->produccion_tn ?? '') }}">
        @error('produccion_tn')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Superficie (Ha)</label>
        <input type="number"
               step="0.01"
               min="0"
               name="superficie_ha"
               id="superficie_ha"
               class="form-control @error('superficie_ha') is-invalid @enderror"
               value="{{ old('superficie_ha', $registro->superficie_ha ?? '') }}">
        @error('superficie_ha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Rinde</label>
        <input type="number"
               step="0.01"
               min="0"
               name="rinde"
               id="rinde"
               class="form-control @error('rinde') is-invalid @enderror"
               value="{{ old('rinde', $registro->rinde ?? '') }}">
        @error('rinde')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- IMÁGENES --}}
    <div class="col-12"><hr></div>

    <div class="col-12 col-md-6">
        <label class="form-label">Imagen 1</label>
        <input type="file"
               name="imagen_1"
               class="form-control @error('imagen_1') is-invalid @enderror"
               accept="image/*">
        @error('imagen_1')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if(!empty($registro?->imagen_1))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $registro->imagen_1) }}"
                     class="img-fluid rounded border"
                     style="max-height: 180px;">
            </div>
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Imagen 2</label>
        <input type="file"
               name="imagen_2"
               class="form-control @error('imagen_2') is-invalid @enderror"
               accept="image/*">
        @error('imagen_2')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if(!empty($registro?->imagen_2))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $registro->imagen_2) }}"
                     class="img-fluid rounded border"
                     style="max-height: 180px;">
            </div>
        @endif
    </div>

    {{-- DATOS CAMIÓN --}}
    <div class="col-12"><hr></div>

    <div class="col-12 col-md-4">
        <label class="form-label">Chasis</label>
        <input type="text"
               name="chasis"
               class="form-control @error('chasis') is-invalid @enderror"
               value="{{ old('chasis', $registro->chasis ?? '') }}">
        @error('chasis')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Chofer</label>
        <input type="text"
               name="chofer"
               class="form-control @error('chofer') is-invalid @enderror"
               value="{{ old('chofer', $registro->chofer ?? '') }}">
        @error('chofer')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Numero / CTG</label>
        <input type="text"
               name="numero"
               class="form-control @error('numero') is-invalid @enderror"
               value="{{ old('numero', $registro->numero ?? '') }}">
        @error('numero')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- DATOS AMBIENTALES --}}
    <div class="col-12"><hr></div>

    <div class="col-12 col-md-4">
        <label class="form-label">Lluvia</label>
        <input type="number"
               step="0.01"
               name="lluvia"
               class="form-control @error('lluvia') is-invalid @enderror"
               value="{{ old('lluvia', $registro->lluvia ?? '') }}">
        @error('lluvia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Humedad</label>
        <input type="number"
               step="0.01"
               max="100"
               name="humedad"
               class="form-control @error('humedad') is-invalid @enderror"
               value="{{ old('humedad', $registro->humedad ?? '') }}">
        @error('humedad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-2 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox"
                   name="pertenece_empresa"
                   value="1"
                   class="form-check-input"
                   id="pertenece_empresa"
                   {{ old('pertenece_empresa', $registro->pertenece_empresa ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="pertenece_empresa">Empresa</label>
        </div>
    </div>

    <div class="col-12 col-md-2 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox"
                   name="silo"
                   value="1"
                   class="form-check-input"
                   id="silo"
                   {{ old('silo', $registro->silo ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="silo">Silo</label>
        </div>
    </div>

    {{-- BOTONES --}}
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2 pt-2">
            <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat">
                Cancelar
            </a>

<button type="submit" class="btn btn-primary btn-mat" id="btn-submit-lote-estado">
    <i class="fa-solid fa-floppy-disk me-1"></i>
    {{ isset($registro) ? 'Actualizar registro' : 'Guardar registro' }}
</button>
        </div>
    </div>

</div>

{{-- MODAL CARGANDO --}}
<div class="modal fade"
     id="modalCargando"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body d-flex align-items-center gap-3">
                <div class="spinner-border" role="status" aria-hidden="true"></div>
                <div>
                    <div class="fw-semibold">Guardando registro…</div>
                    <div class="text-muted small">Por favor esperá un momento.</div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- LOADING / BLOQUEO DE ENVÍO --}}
<div id="loadingOverlay" class="loading-overlay d-none">
    <div class="loading-box shadow">
        <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
        <div class="fw-semibold">Guardando datos...</div>
        <div class="text-muted small">Por favor esperá unos segundos</div>
    </div>
</div>

@push('styles')
<style>
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.82);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .loading-box {
        background: #fff;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        min-width: 260px;
        text-align: center;
        border: 1px solid #e5e7eb;
    }

    .loading-overlay.d-none {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const establecimientoSelect = document.getElementById('establecimiento_id');
    const loteSelect = document.getElementById('lote_id');
    const produccionInput = document.getElementById('produccion_tn');
    const superficieInput = document.getElementById('superficie_ha');
    const rindeInput = document.getElementById('rinde');

    const form = document.getElementById('lote-estado-form');
    const submitBtn = document.getElementById('btn-submit-lote-estado');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // =========================
    // FILTRO DE LOTES
    // =========================
    if (establecimientoSelect && loteSelect) {
        const allOptions = Array.from(loteSelect.querySelectorAll('option'));

        function filtrarLotes() {
            const establecimientoId = establecimientoSelect.value;
            const loteActual = loteSelect.value;

            loteSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Seleccionar...';
            loteSelect.appendChild(placeholder);

            allOptions.forEach(option => {
                if (option.value === '') return;

                const optionEstablecimiento = option.getAttribute('data-establecimiento');

                if (!establecimientoId || String(optionEstablecimiento) === String(establecimientoId)) {
                    loteSelect.appendChild(option.cloneNode(true));
                }
            });

            if ([...loteSelect.options].some(opt => opt.value === loteActual)) {
                loteSelect.value = loteActual;
            }
        }

        establecimientoSelect.addEventListener('change', function () {
            loteSelect.value = '';
            filtrarLotes();
        });

        filtrarLotes();
    }

    // =========================
    // CÁLCULO DE RINDE
    // =========================
    function calcularRinde() {
        if (!produccionInput || !superficieInput || !rindeInput) return;

        const produccion = parseFloat(produccionInput.value);
        const superficie = parseFloat(superficieInput.value);

        if (!isNaN(produccion) && !isNaN(superficie) && superficie > 0) {
            const valor = produccion / superficie;
            rindeInput.value = valor.toFixed(2);
        }
    }

    produccionInput?.addEventListener('input', calcularRinde);
    superficieInput?.addEventListener('input', calcularRinde);

    // =========================
    // BLOQUEO DOBLE SUBMIT
    // =========================
    if (form) {
        form.addEventListener('submit', function (e) {
            // si ya se envió una vez, frenamos
            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }

            // validar HTML5 antes de bloquear
            if (!form.checkValidity()) {
                return;
            }

            form.dataset.submitting = '1';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
            }

            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
            }
        });
    }
});
</script>
@endpush
