@php
  /** @var \App\Models\Organizacion|null $organizacion */
  $o = $organizacion ?? null;

  $fechaDefault  = old('fecha', optional($o?->fecha)->format('Y-m-d') ?? now()->format('Y-m-d'));
  $activoDefault = old('activo', $o?->activo ?? true);
@endphp

<div class="row g-3">
  <div class="col-12 col-md-4">
    <label class="form-label">Código</label>
    <input type="text"
           name="codigo"
           class="form-control @error('codigo') is-invalid @enderror"
           value="{{ old('codigo', $o->codigo ?? '') }}"
           maxlength="50"
           required>
    @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 col-md-8">
    <label class="form-label">Nombre</label>
    <input type="text"
           name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $o->name ?? '') }}"
           maxlength="150"
           required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Fecha</label>
    <input type="date"
           name="fecha"
           class="form-control @error('fecha') is-invalid @enderror"
           value="{{ $fechaDefault }}"
           required>
    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 col-md-8">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion"
              rows="2"
              class="form-control @error('descripcion') is-invalid @enderror"
              placeholder="Opcional...">{{ old('descripcion', $o->descripcion ?? '') }}</textarea>
    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12">
    <div class="form-check form-switch">
      {{-- clave: si está OFF, igual enviamos 0 --}}
      <input type="hidden" name="activo" value="0">
      <input class="form-check-input"
             type="checkbox"
             role="switch"
             id="activo"
             name="activo"
             value="1"
             @checked((bool)$activoDefault)>
      <label class="form-check-label" for="activo">Activo</label>
    </div>
    <div class="form-text text-muted">
      Si lo desactivás, quedará disponible sólo para consulta.
    </div>
  </div>
</div>
