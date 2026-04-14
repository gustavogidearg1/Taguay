@extends('layouts.app')

@section('title', 'Carga rápida de campo')

@section('content')
    <div class="page-wrap quick-wrap">
        <div class="card mat-card quick-card">
            <div class="card-header header-mint py-3">
                <div class="mat-header">
                    <h3 class="mat-title">
                        <i class="fa-solid fa-mobile-screen-button me-2"></i> Carga rápida de campo
                    </h3>

                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat quick-back">
                            Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success py-2">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        Revisá los campos marcados y volvé a intentar.
                    </div>
                @endif

                      <form id="quick-form" action="{{ route('lote-estados.quick-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">Establecimiento <span class="text-danger">*</span></label>
                            <select id="establecimiento_id" name="establecimiento_id"
                                class="form-select form-select-lg @error('establecimiento_id') is-invalid @enderror"
                                required>
                                <option value="">Seleccionar...</option>
                                @foreach ($establecimientos as $establecimiento)
                                    <option value="{{ $establecimiento->id }}"
                                        {{ old('establecimiento_id') == $establecimiento->id ? 'selected' : '' }}>
                                        {{ $establecimiento->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('establecimiento_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Lote <span class="text-danger">*</span></label>
                            <select id="lote_id" name="lote_id"
                                class="form-select form-select-lg @error('lote_id') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                @foreach ($lotes as $lote)
                                    <option value="{{ $lote->id }}"
                                        data-establecimiento="{{ $lote->establecimiento_id }}"
                                        {{ old('lote_id') == $lote->id ? 'selected' : '' }}>
                                        {{ $lote->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lote_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado_cultivo_id" id="estado_cultivo_id"
                                class="form-select form-select-lg @error('estado_cultivo_id') is-invalid @enderror"
                                required>
                                <option value="">Seleccionar...</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}"
                                        {{ old('estado_cultivo_id') == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado_cultivo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha"
                                class="form-control form-control-lg @error('fecha') is-invalid @enderror"
                                value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- GPS --}}
                        <div class="col-12">
                            <div class="p-3 border rounded-4 bg-light">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div class="fw-semibold">
                                        <i class="fa-solid fa-location-dot me-1"></i> Ubicación GPS
                                    </div>

                                    <button type="button" id="btnObtenerUbicacion" class="btn btn-outline-primary btn-sm">
                                        Obtener ubicación actual
                                    </button>
                                </div>

                                <div id="gpsStatus" class="small text-muted mb-3">
                                    Todavía no se obtuvo la ubicación.
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Latitud</label>
                                        <input type="text" name="latitud" id="latitud"
                                            class="form-control @error('latitud') is-invalid @enderror"
                                            value="{{ old('latitud') }}" readonly>
                                        @error('latitud')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Longitud</label>
                                        <input type="text" name="longitud" id="longitud"
                                            class="form-control @error('longitud') is-invalid @enderror"
                                            value="{{ old('longitud') }}" readonly>
                                        @error('longitud')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Link Google Maps</label>
                                        <input type="text" name="link_google_maps" id="link_google_maps"
                                            class="form-control @error('link_google_maps') is-invalid @enderror"
                                            value="{{ old('link_google_maps') }}" readonly>
                                        @error('link_google_maps')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Producción (Tn)</label>
                            <input type="number" step="0.01" min="0" name="produccion_tn" id="produccion_tn"
                                class="form-control form-control-lg @error('produccion_tn') is-invalid @enderror"
                                value="{{ old('produccion_tn') }}">
                            @error('produccion_tn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Superficie (Ha)</label>
                            <input type="number" step="0.01" min="0" name="superficie_ha" id="superficie_ha"
                                class="form-control form-control-lg @error('superficie_ha') is-invalid @enderror"
                                value="{{ old('superficie_ha') }}">
                            @error('superficie_ha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Rinde</label>
                            <input type="number" step="0.01" min="0" name="rinde" id="rinde"
                                class="form-control form-control-lg @error('rinde') is-invalid @enderror"
                                value="{{ old('rinde') }}" placeholder="Se puede calcular automáticamente">
                            @error('rinde')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Si cargás producción y superficie, se calcula solo.</div>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold">Lluvia</label>
                            <input type="number" step="0.01" min="0" name="lluvia"
                                class="form-control form-control-lg @error('lluvia') is-invalid @enderror"
                                value="{{ old('lluvia') }}">
                            @error('lluvia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold">Humedad</label>
                            <input type="number" step="0.01" min="0" max="100" name="humedad"
                                class="form-control form-control-lg @error('humedad') is-invalid @enderror"
                                value="{{ old('humedad') }}">
                            @error('humedad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 quick-extra">
                            <div class="p-3 border rounded-4 bg-light">
                                <div class="fw-semibold mb-2">Datos adicionales</div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Chasis</label>
                                        <input type="text" name="chasis" class="form-control form-control-lg"
                                            value="{{ old('chasis') }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Chofer</label>
                                        <input type="text" name="chofer" class="form-control form-control-lg"
                                            value="{{ old('chofer') }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Número / CTG</label>
                                        <input type="text" name="numero" class="form-control form-control-lg"
                                            value="{{ old('numero') }}">
                                    </div>

                                    <div class="col-6">
                                        <div class="form-check pt-2">
                                            <input class="form-check-input" type="checkbox" name="pertenece_empresa"
                                                value="1" id="pertenece_empresa"
                                                {{ old('pertenece_empresa') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="pertenece_empresa">
                                                <strong>Empresa</strong>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-check pt-2">
                                            <input class="form-check-input" type="checkbox" name="silo"
                                                value="1" id="silo" {{ old('silo') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="silo">
                                                <strong>Silo</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cámara directa --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Imagen 1</label>
<input type="file" name="imagen_1"
    class="form-control form-control-lg @error('imagen_1') is-invalid @enderror"
    accept="image/*" capture="environment">
                            @error('imagen_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">En celular abre la cámara trasera o permite sacar foto directamente.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Imagen 2</label>
<input type="file" name="imagen_2"
    class="form-control form-control-lg @error('imagen_2') is-invalid @enderror"
    accept="image/*" capture="environment">
                            @error('imagen_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observación</label>
                            <textarea name="observacion" rows="3"
                                class="form-control form-control-lg @error('observacion') is-invalid @enderror"
                                placeholder="Comentario breve de campo...">{{ old('observacion') }}</textarea>
                            @error('observacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 pt-2">
                            <button class="btn btn-primary btn-lg btn-mat w-100 quick-save">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar carga
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="loading-overlay d-none">
    <div class="loading-box shadow">
        <div class="spinner-border text-primary mb-3"></div>
        <div class="fw-semibold">Guardando datos...</div>
        <div class="text-muted small">Por favor esperá unos segundos</div>
    </div>
</div>

@endsection

@push('styles')
    <style>
        .quick-wrap {
            max-width: 760px;
        }

        .quick-card {
            overflow: hidden;
        }

        .quick-save {
            min-height: 54px;
            font-weight: 700;
        }

        .quick-extra .form-check-input {
            transform: scale(1.2);
        }

        @media (max-width: 768px) {
            .quick-wrap {
                padding-left: .35rem;
                padding-right: .35rem;
            }

            .quick-back {
                padding-left: .6rem;
                padding-right: .6rem;
            }

            .form-control-lg,
            .form-select-lg {
                font-size: 1rem;
                min-height: 52px;
                border-radius: 14px;
            }

            textarea.form-control-lg {
                min-height: 110px;
            }

            .card-body {
                padding: 1rem;
            }

            .mat-title {
                font-size: 1rem;
            }
        }

.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.85);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-box {
    background: white;
    padding: 20px 25px;
    border-radius: 15px;
    text-align: center;
}

.loading-overlay.d-none {
    display: none !important;
}

    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const establecimientoSelect = document.getElementById('establecimiento_id');
    const loteSelect = document.getElementById('lote_id');
    const produccionInput = document.getElementById('produccion_tn');
    const superficieInput = document.getElementById('superficie_ha');
    const rindeInput = document.getElementById('rinde');

    const btnUbicacion = document.getElementById('btnObtenerUbicacion');
    const latitudInput = document.getElementById('latitud');
    const longitudInput = document.getElementById('longitud');
    const mapsInput = document.getElementById('link_google_maps');
    const gpsStatus = document.getElementById('gpsStatus');

    // 🔥 NUEVO
    const form = document.getElementById('quick-form');
    const submitBtn = document.querySelector('.quick-save');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // =========================
    // FILTRO LOTES
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
                if (!option.value) return;

                const estId = option.dataset.establecimiento;

                if (!establecimientoId || String(estId) === String(establecimientoId)) {
                    loteSelect.appendChild(option.cloneNode(true));
                }
            });

            if ([...loteSelect.options].some(opt => opt.value === loteActual)) {
                loteSelect.value = loteActual;
            }
        }

        establecimientoSelect.addEventListener('change', function() {
            loteSelect.value = '';
            filtrarLotes();
        });

        filtrarLotes();
    }

    // =========================
    // RINDE
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
    // GPS
    // =========================
    function setGpsStatus(message, type = 'muted') {
        gpsStatus.className = 'small mb-3';

        if (type === 'success') gpsStatus.classList.add('text-success');
        else if (type === 'danger') gpsStatus.classList.add('text-danger');
        else if (type === 'warning') gpsStatus.classList.add('text-warning');
        else gpsStatus.classList.add('text-muted');

        gpsStatus.textContent = message;
    }

    function obtenerUbicacion() {
        if (!navigator.geolocation) {
            setGpsStatus('Este dispositivo no soporta geolocalización.', 'danger');
            return;
        }

        setGpsStatus('Obteniendo ubicación...', 'warning');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                latitudInput.value = lat.toFixed(7);
                longitudInput.value = lng.toFixed(7);
                mapsInput.value = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

                setGpsStatus('Ubicación OK', 'success');
            },
            function() {
                setGpsStatus('No se pudo obtener ubicación', 'danger');
            },
            {
                enableHighAccuracy: true,
                timeout: 15000
            }
        );
    }

    btnUbicacion?.addEventListener('click', obtenerUbicacion);

    // AUTO GPS
    obtenerUbicacion();

    // =========================
    // 🔥 LOADING + BLOQUEO
    // =========================
    if (form) {
        form.addEventListener('submit', function(e) {

            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }

            if (!form.checkValidity()) {
                return;
            }

            form.dataset.submitting = '1';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
            }

            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
            }
        });
    }

});
</script>
@endpush
