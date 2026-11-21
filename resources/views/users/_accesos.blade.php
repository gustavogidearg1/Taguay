{{--
    Parcial compartido por users/create y users/edit.
    Variables: $roles, $rolesDescripcion, $reportes, $selectedRoles, $selectedReportes
--}}

{{-- ROLES (varios por usuario) --}}
<div class="mb-4">
    <label class="form-label fw-semibold mb-1">Roles</label>
    <div class="form-text mt-0 mb-2">Definen qué puede operar el usuario. Puede tener más de uno.</div>

    <div class="row g-2">
        @foreach ($roles as $clave => $label)
            <div class="col-12 col-md-6">
                <label class="acceso-opcion d-flex gap-2 p-2 border rounded-3 h-100" for="rol_{{ $clave }}">
                    <input class="form-check-input mt-1 flex-shrink-0" type="checkbox" name="roles[]"
                        id="rol_{{ $clave }}" value="{{ $clave }}"
                        @checked(in_array($clave, $selectedRoles, true))>
                    <span>
                        <span class="fw-semibold d-block">{{ $label }}</span>
                        <span class="small text-muted">{{ $rolesDescripcion[$clave] ?? '' }}</span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
    @error('roles') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    @error('roles.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

{{-- REPORTES DEL HOME --}}
<div class="mb-4">
    <label class="form-label fw-semibold mb-1">Reportes en el Home</label>
    <div class="form-text mt-0 mb-2">
        Tarjetas que el usuario ve en la pantalla de inicio. El Administrador ve todos sin necesidad de tildarlos.
    </div>

    <div class="row g-2">
        @foreach ($reportes as $clave => $reporte)
            <div class="col-12 col-md-4">
                <label class="acceso-opcion d-flex gap-2 p-2 border rounded-3 h-100" for="rep_{{ $clave }}">
                    <input class="form-check-input mt-1 flex-shrink-0" type="checkbox" name="reportes[]"
                        id="rep_{{ $clave }}" value="{{ $clave }}"
                        @checked(in_array($clave, $selectedReportes, true))>
                    <span>
                        <span class="fw-semibold d-block">{{ $reporte['label'] }}</span>
                        <span class="small text-muted">{{ $reporte['detalle'] }}</span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
    @error('reportes.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

@once
    <style>
        .acceso-opcion { cursor: pointer; transition: border-color .15s, background-color .15s; }
        .acceso-opcion:hover { border-color: #198754 !important; }
        .acceso-opcion:has(input:checked) { border-color: #198754 !important; background-color: #e9f5ee; }
        .acceso-opcion .form-check-input:checked { background-color: #198754; border-color: #198754; }
    </style>
@endonce
