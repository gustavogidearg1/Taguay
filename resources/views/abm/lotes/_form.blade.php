@csrf

<div class="row g-3">
    <div class="col-12 col-md-4">
        <label class="form-label">Establecimiento <span class="text-danger">*</span></label>
        <select name="establecimiento_id" class="form-select @error('establecimiento_id') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($establecimientos as $establecimiento)
                <option value="{{ $establecimiento->id }}"
                    {{ old('establecimiento_id', $lote->establecimiento_id ?? '') == $establecimiento->id ? 'selected' : '' }}>
                    {{ $establecimiento->nombre }}
                </option>
            @endforeach
        </select>
        @error('establecimiento_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Campaña <span class="text-danger">*</span></label>
        <select name="campania_id" class="form-select @error('campania_id') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($campanias as $campania)
                <option value="{{ $campania->id }}"
                    {{ old('campania_id', $lote->campania_id ?? '') == $campania->id ? 'selected' : '' }}>
                    {{ $campania->name }}
                </option>
            @endforeach
        </select>
        @error('campania_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Cultivo <span class="text-danger">*</span></label>
        <select name="cultivo_id" class="form-select @error('cultivo_id') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($cultivos as $cultivo)
                <option value="{{ $cultivo->id }}"
                    {{ old('cultivo_id', $lote->cultivo_id ?? '') == $cultivo->id ? 'selected' : '' }}>
                    {{ $cultivo->name }}
                </option>
            @endforeach
        </select>
        @error('cultivo_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Nombre del lote <span class="text-danger">*</span></label>
        <input type="text"
               name="nombre"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $lote->nombre ?? '') }}"
               maxlength="100"
               required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Hectáreas <span class="text-danger">*</span></label>
        <input type="number"
               step="0.01"
               min="0.01"
               name="hectareas"
               class="form-control @error('hectareas') is-invalid @enderror"
               value="{{ old('hectareas', $lote->hectareas ?? '') }}"
               required>
        @error('hectareas')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Ubicación de referencia</label>
        <input type="text"
               name="ubicacion_referencia"
               class="form-control @error('ubicacion_referencia') is-invalid @enderror"
               value="{{ old('ubicacion_referencia', $lote->ubicacion_referencia ?? '') }}"
               maxlength="255"
               placeholder="Ej: ingreso por camino sur, a 2 km del establecimiento">
        @error('ubicacion_referencia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Latitud</label>
        <input type="number"
               step="0.0000001"
               name="latitud"
               id="latitud"
               class="form-control @error('latitud') is-invalid @enderror"
               value="{{ old('latitud', $lote->latitud ?? '') }}"
               placeholder="-32.1234567">
        @error('latitud')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Longitud</label>
        <input type="number"
               step="0.0000001"
               name="longitud"
               id="longitud"
               class="form-control @error('longitud') is-invalid @enderror"
               value="{{ old('longitud', $lote->longitud ?? '') }}"
               placeholder="-62.1234567">
        @error('longitud')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Link Google Maps</label>
        <input type="url"
               name="link_google_maps"
               id="link_google_maps"
               class="form-control @error('link_google_maps') is-invalid @enderror"
               value="{{ old('link_google_maps', $lote->link_google_maps ?? '') }}"
               maxlength="500"
               placeholder="Se completa solo si hay lat/long">
        @error('link_google_maps')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary btn-mat" id="btnGenerarMapa">
                <i class="fa-solid fa-map-location-dot me-1"></i> Generar link mapa
            </button>

            <a href="#" target="_blank" class="btn btn-outline-secondary btn-mat d-none" id="btnVerMapa">
                <i class="fa-solid fa-map me-1"></i> Ver mapa
            </a>

            <a href="#" target="_blank" class="btn btn-success btn-mat d-none" id="btnComoLlegar">
                <i class="fa-solid fa-route me-1"></i> Cómo llegar
            </a>
        </div>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-end gap-2 pt-2">
            <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary btn-mat">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary btn-mat">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                {{ isset($lote) ? 'Actualizar lote' : 'Guardar lote' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitud');
    const lngInput = document.getElementById('longitud');
    const linkInput = document.getElementById('link_google_maps');
    const btnGenerar = document.getElementById('btnGenerarMapa');
    const btnVerMapa = document.getElementById('btnVerMapa');
    const btnComoLlegar = document.getElementById('btnComoLlegar');

    function buildSearchUrl() {
        const lat = latInput?.value?.trim();
        const lng = lngInput?.value?.trim();

        if (!lat || !lng) return '';
        return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    }

    function buildDirectionsUrl() {
        const lat = latInput?.value?.trim();
        const lng = lngInput?.value?.trim();

        if (!lat || !lng) return '';
        return `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    }

    function refreshButtons() {
        const searchUrl = buildSearchUrl();
        const directionsUrl = buildDirectionsUrl();
        const manualUrl = linkInput?.value?.trim();

        if (searchUrl) {
            btnVerMapa.href = searchUrl;
            btnComoLlegar.href = directionsUrl;
            btnVerMapa.classList.remove('d-none');
            btnComoLlegar.classList.remove('d-none');
        } else if (manualUrl) {
            btnVerMapa.href = manualUrl;
            btnVerMapa.classList.remove('d-none');
            btnComoLlegar.classList.add('d-none');
        } else {
            btnVerMapa.classList.add('d-none');
            btnComoLlegar.classList.add('d-none');
        }
    }

    btnGenerar?.addEventListener('click', function () {
        const searchUrl = buildSearchUrl();

        if (!searchUrl) {
            alert('Primero cargá latitud y longitud.');
            return;
        }

        linkInput.value = searchUrl;
        refreshButtons();
    });

    latInput?.addEventListener('input', refreshButtons);
    lngInput?.addEventListener('input', refreshButtons);
    linkInput?.addEventListener('input', refreshButtons);

    refreshButtons();
});
</script>
@endpush
