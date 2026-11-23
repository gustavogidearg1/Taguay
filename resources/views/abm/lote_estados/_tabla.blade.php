{{-- Tabla de Avance de Campo (escritorio + tarjetas en móvil). Se usa en lote_estados/index y en el Home.
     Variable: $registros (colección o paginador de LoteEstado). Eliminar solo lo ve el admin. --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Lote</th>
                            <th>Establecimiento</th>
                            <th>Campaña</th>
                            <th>Cultivo</th>
                            <th class="text-end">Cantidad (kg)</th>
                            <th>Propiedad</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $item)
                            <tr>
                                <td>{{ optional($item->fecha)->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $item->lote->nombre ?? '-' }}</td>
                                <td>{{ $item->lote->establecimiento->nombre ?? '-' }}</td>
                                <td>{{ $item->lote->campania->name ?? '-' }}</td>
                                <td>{{ $item->cultivo->name ?? $item->lote->cultivo->name ?? '-' }}</td>
                                <td class="text-end text-nowrap">{{ $item->cantidad_formateada }}</td>
                                <td>
                                    <span class="badge {{ $item->propietario ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $item->propietario ? 'Propio' : 'Socio' }}
                                    </span>
                                </td>
                                <td>{{ $item->estadoCultivo->nombre ?? '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('lote-estados.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('lote-estados.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        @role('admin')
                                        <form action="{{ route('lote-estados.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                            @if($item->observacion)
                                <tr>
                                    <td></td>
                                    <td colspan="9" class="text-muted small">
                                        <strong>Observación:</strong> {{ $item->observacion }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No hay registros cargados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none">
                <div class="row g-3">
                    @forelse($registros as $item)
                        <div class="col-12">
                            <div class="border rounded-4 p-3 bg-white shadow-sm">
                                <div class="fw-bold">{{ $item->lote->nombre ?? '-' }}</div>
                                <div class="small text-muted">{{ $item->lote->establecimiento->nombre ?? '-' }}</div>

                                <div class="mt-2 small">
                                    <div><strong>Fecha:</strong> {{ optional($item->fecha)->format('d/m/Y') }}</div>
                                    <div><strong>Campaña:</strong> {{ $item->lote->campania->name ?? '-' }}</div>
                                    <div><strong>Cultivo:</strong> {{ $item->cultivo->name ?? $item->lote->cultivo->name ?? '-' }}</div>
                                    <div><strong>Cantidad:</strong> {{ $item->cantidad_formateada }} kg</div>
                                    <div><strong>Propiedad:</strong> {{ $item->propietario ? 'Propio' : 'Socio' }}</div>
                                    <div><strong>Estado:</strong> {{ $item->estadoCultivo->nombre ?? '-' }}</div>
                                    <div><strong>Usuario:</strong> {{ $item->user->name ?? '-' }}</div>
                                    @if($item->observacion)
                                        <div><strong>Obs.:</strong> {{ $item->observacion }}</div>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <a href="{{ route('lote-estados.show', $item) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('lote-estados.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                    @role('admin')
                                    <form action="{{ route('lote-estados.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                    @endrole
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border text-center mb-0">
                                No hay registros cargados.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
