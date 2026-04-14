@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Lote <span class="text-danger">*</span></label>
        <select name="lote_id" class="form-select @error('lote_id') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($lotes as $lote)
                <option value="{{ $lote->id }}"
                    {{ old('lote_id', $rinde->lote_id ?? '') == $lote->id ? 'selected' : '' }}>
                    {{ $lote->nombre }} - {{ $lote->establecimiento->nombre ?? '-' }} - {{ $lote->campania->name ?? '-' }} - {{ $lote->cultivo->name ?? '-' }}
                </option>
            @endforeach
        </select>
        @error('lote_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label">Fecha <span class="text-danger">*</span></label>
        <input type="date"
               name="fecha"
               class="form-control @error('fecha') is-invalid @enderror"
               value="{{ old('fecha', isset($rinde) && $rinde->fecha ? $rinde->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required>
        @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label">Rinde (qq/ha) <span class="text-danger">*</span></label>
        <input type="number"
               step="0.01"
               min="0"
               name="rinde"
               class="form-control @error('rinde') is-invalid @enderror"
               value="{{ old('rinde', $rinde->rinde ?? '') }}"
               required>
        @error('rinde')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Humedad (%)</label>
        <input type="number"
               step="0.01"
               min="0"
               max="100"
               name="humedad"
               class="form-control @error('humedad') is-invalid @enderror"
               value="{{ old('humedad', $rinde->humedad ?? '') }}">
        @error('humedad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Superficie cosechada (ha)</label>
        <input type="number"
               step="0.01"
               min="0"
               name="superficie_cosechada"
               class="form-control @error('superficie_cosechada') is-invalid @enderror"
               value="{{ old('superficie_cosechada', $rinde->superficie_cosechada ?? '') }}">
        @error('superficie_cosechada')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Observación</label>
        <textarea name="observacion"
                  rows="4"
                  class="form-control @error('observacion') is-invalid @enderror"
                  placeholder="Comentarios, condiciones de cosecha, observaciones del lote...">{{ old('observacion', $rinde->observacion ?? '') }}</textarea>
        @error('observacion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('rindes.index') }}" class="btn btn-outline-secondary btn-mat">Cancelar</a>
            <button type="submit" class="btn btn-primary btn-mat">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                {{ isset($rinde) ? 'Actualizar rinde' : 'Guardar rinde' }}
            </button>
        </div>
    </div>
</div>
