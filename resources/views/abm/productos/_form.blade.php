@php
  $p = $producto ?? null;

  $name   = old('name', $p->name ?? '');
  $codigo = old('codigo', $p->codigo ?? '');

  $unidadId = old('unidad_id', $p->unidad_id ?? '');
  $tipoId   = old('tipo_producto_id', $p->tipo_producto_id ?? '');

  $minimo = old('minimo', $p->minimo ?? '');
  $maximo = old('maximo', $p->maximo ?? '');
  $obser  = old('obser', $p->obser ?? '');

  $activo = old('activo', $p ? (bool)$p->activo : true);
  $stock  = old('stock',  $p ? (bool)$p->stock  : true);
  $vende  = old('vende',  $p ? (bool)$p->vende  : true);
@endphp

<div class="row g-3">

  <div class="col-12 col-md-6">
    <label class="form-label">Nombre *</label>
    <input type="text" name="name" class="form-control" maxlength="100" required
           value="{{ $name }}" placeholder="Ej: Semilla Trigo">
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Código *</label>
    <input type="text" name="codigo" class="form-control" maxlength="50" required
           value="{{ $codigo }}" placeholder="Ej: TRIGO-001">
  </div>

  <div class="col-12 col-md-3">
    <label class="form-label">Unidad *</label>
    <select name="unidad_id" class="form-select" required>
      <option value="">Seleccionar...</option>
      @foreach($unidades as $u)
        <option value="{{ $u->id }}" @selected((string)$unidadId === (string)$u->id)>
          {{ $u->name }} ({{ $u->corta ?? $u->codigo }})
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Tipo *</label>
    <select name="tipo_producto_id" class="form-select" required>
      <option value="">Seleccionar...</option>
      @foreach($tipos as $t)
        <option value="{{ $t->id }}" @selected((string)$tipoId === (string)$t->id)>
          {{ $t->name }} ({{ $t->codigo }})
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Mínimo</label>
    <input type="number" step="0.01" min="0" name="minimo" class="form-control"
           value="{{ $minimo }}" placeholder="0.00">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Máximo</label>
    <input type="number" step="0.01" min="0" name="maximo" class="form-control"
           value="{{ $maximo }}" placeholder="0.00">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Observación</label>
    <input type="text" name="obser" class="form-control" maxlength="200"
           value="{{ $obser }}" placeholder="Hasta 200 caracteres">
  </div>

  <div class="col-12">
    <div class="d-flex flex-wrap gap-4 mt-1">

      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" @checked($activo)>
        <label class="form-check-label" for="activo">Activo</label>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="stock" name="stock" value="1" @checked($stock)>
        <label class="form-check-label" for="stock">Stock</label>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="vende" name="vende" value="1" @checked($vende)>
        <label class="form-check-label" for="vende">Vende</label>
      </div>

    </div>
  </div>

</div>
