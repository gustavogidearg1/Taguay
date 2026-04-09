@csrf

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Lote <span class="text-danger">*</span></label>
        <select name="lote_id" class="form-select @error('lote_id') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($lotes as $lote)
                <option value="{{ $lote->id }}"
                    {{ old('lote_id', $registro->lote_id ?? '') == $lote->id ? 'selected' : '' }}>
                    {{ $lote->nombre }} - {{ $lote->establecimiento->nombre ?? '-' }} - {{ $lote->campania->name ?? '-' }}
                </option>
            @endforeach
        </select>
        @error('lote_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Estado del cultivo <span class="text-danger">*</span></label>
        <select name="estado_cultivo_id" class="form-select @error('estado_cultivo_id') is-invalid @enderror" required>
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

    <div class="col-12 col-md-4">
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

    <div class="col-12">
        <label class="form-label">Observación</label>
        <textarea name="observacion"
                  rows="4"
                  class="form-control @error('observacion') is-invalid @enderror"
                  placeholder="Detalle adicional del estado, comentarios de campo, etc.">{{ old('observacion', $registro->observacion ?? '') }}</textarea>
        @error('observacion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('lote-estados.index') }}" class="btn btn-outline-secondary btn-mat">Cancelar</a>
            <button type="submit" class="btn btn-primary btn-mat">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                {{ isset($registro) ? 'Actualizar registro' : 'Guardar registro' }}
            </button>
        </div>
    </div>
</div>
