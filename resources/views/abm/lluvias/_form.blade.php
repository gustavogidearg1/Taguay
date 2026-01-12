@php($edit = isset($lluvia))
<div class="row g-3">
    <div class="row">
  <div class="col-md-4">
    <label class="form-label">Establecimiento *</label>
    <select name="establecimiento_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      @foreach($establecimientos as $e)
        <option value="{{ $e->id }}" @selected(old('establecimiento_id', $lluvia->establecimiento_id ?? '')==$e->id)>{{ $e->nombre }}</option>
      @endforeach
    </select>
    @error('establecimiento_id')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3">
    <label class="form-label">Fecha *</label>
    <input type="date" name="fecha" value="{{ old('fecha', isset($lluvia)?$lluvia->fecha->format('Y-m-d'):date('Y-m-d')) }}" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Hora</label>
    <input type="time" name="hora"
  value="{{ old('hora', isset($lluvia) ? ($lluvia->hora ? $lluvia->hora->format('H:i') : null) : null) }}"
  class="form-control">
  </div>

  <div class="col-md-2">
    <label class="form-label">Milímetros (mm) *</label>
    <input type="number" step="0.1" min="0" max="1000" name="mm" value="{{ old('mm', $lluvia->mm ?? null) }}" class="form-control" required>
  </div>

<div class="row">
  <div class="col-md-8">
    <label class="form-label">Observador</label>
    <input type="text" name="observador" value="{{ old('observador', $lluvia->observador ?? null) }}" class="form-control">
  </div>

  <div class="col-md-4">
    <label class="form-label">Archivo (foto/pdf)</label>
    <input type="file" name="archivo" class="form-control">
    @if(isset($lluvia) && $lluvia->archivo_path)
      <small class="text-muted">Actual: <a href="{{ Storage::disk('public')->url($lluvia->archivo_path) }}" target="_blank">ver</a></small>
    @endif
  </div>

  </div>

  </div>


  {{-- Div ocultos --}}
<div class="d-none">

  <div class="col-md-3">
    <label class="form-label">Fuente *</label>
    @php($f = old('fuente', $lluvia->fuente ?? 'manual'))
    <select name="fuente" class="form-select" required>
      <option value="manual" @selected($f==='manual')>Manual</option>
      <option value="automatico" @selected($f==='automatico')>Automático</option>
    </select>
  </div>

<div class="col-md-6">
    <label class="form-label">Comentario</label>
    <input type="text" name="comentario" value="{{ old('comentario', $lluvia->comentario ?? null) }}" class="form-control">
  </div>

  <div class="col-md-4">
    <label class="form-label">Estación</label>
    <input type="text" name="estacion_nombre" value="{{ old('estacion_nombre', $lluvia->estacion_nombre ?? null) }}" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Lat</label>
    <input type="number" step="0.0000001" name="lat" value="{{ old('lat', $lluvia->lat ?? null) }}" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Lng</label>
    <input type="number" step="0.0000001" name="lng" value="{{ old('lng', $lluvia->lng ?? null) }}" class="form-control">
  </div>

</div>


</div>
