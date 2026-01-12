@csrf
<hr>

<div class="row g-3">

    <div class="row">
  <div class="col-md-12">
    <label class="form-label">Cliente *</label>
    <input type="text" name="cliente" value="{{ old('cliente', $entry->cliente) }}" class="form-control" maxlength="200" required placeholder="Cliente">
  </div>

  </div>


<div class="row">

  <div class="col-md-4">
    <label class="form-label">Consignatario</label>
    <input type="text" name="consignatario" value="{{ old('consignatario', $entry->consignatario) }}" class="form-control" maxlength="200">
  </div>

  <div class="col-md-4">
    <label class="form-label">Vendedor</label>
    <input type="text" name="vendedor" value="{{ old('vendedor', $entry->vendedor) }}" class="form-control" maxlength="200">
  </div>


    <div class="col-md-4">
    <label class="form-label">Establecimiento *</label>
    <select name="establecimiento_id" class="form-select" required>
      <option value="">-- Seleccionar --</option>
      @foreach($establecimientos as $e)
        <option value="{{ $e->id }}" @selected(old('establecimiento_id', $entry->establecimiento_id)==$e->id)>{{ $e->nombre }}</option>
      @endforeach
    </select>
  </div>

  </div>

  <div class="row">

  <div class="col-md-4">
    <label class="form-label">Cantidad</label>
    <input type="number" step="0.1" name="cantidad" value="{{ old('cantidad', $entry->cantidad) }}" class="form-control" required>
  </div>

    <div class="col-md-4">
    <label class="form-label">Peso vivo (-8%)</label>
    <input type="number" step="0.1" name="peso_vivo_menos_8" value="{{ old('peso_vivo_menos_8', $entry->peso_vivo_menos_8) }}" class="form-control">
  </div>

    <div class="col-md-4">
    <label class="form-label">Categoría *</label>
    <select name="categoria_id" class="form-select" required>
      <option value="">-- Seleccionar --</option>
      @foreach($categorias as $c)
        <option value="{{ $c->id }}" @selected(old('categoria_id', $entry->categoria_id)==$c->id)>{{ $c->nombre }}</option>
      @endforeach
    </select>
  </div>

  </div>

  <div class="row">
      <div class="col-md-4">
    <label class="form-label">Destino</label>
    <input type="text" name="destino" value="{{ old('destino', $entry->destino) }}" class="form-control" maxlength="200">
  </div>

  <div class="col-md-4">
    <label class="form-label">Transportista</label>
    <input type="text" name="transportista" value="{{ old('transportista', $entry->transportista) }}" class="form-control" maxlength="100">
  </div>

  <div class="col-md-4">
    <label class="form-label">Patente</label>
    <input type="text" name="patente" value="{{ old('patente', $entry->patente) }}" class="form-control" maxlength="50">
  </div>

  </div>


</div>

<hr class="my-4">
<div class="d-flex gap-2">
  <button id="btnGuardarHacienda" type="submit" class="btn btn-primary">
    <i class="bi bi-save"></i> Guardar
  </button>
  <a href="{{ route('haciendas.index') }}" class="btn btn-secondary">Volver</a>
</div>
