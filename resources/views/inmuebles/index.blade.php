@extends('layouts.panel')
@section('title', 'Mis Inmuebles')

@section('content_header')
    <h1>Mis Inmuebles</h1>
@stop

@section('content')
@if(!empty($avisoLimite))
<div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <i class="fas fa-exclamation-triangle me-1"></i>
        Usaste <strong>{{ $avisoLimite['usadas'] }}</strong> de <strong>{{ $avisoLimite['lim'] }}</strong> publicaciones de tu plan.
        Te quedan <strong>{{ $avisoLimite['restan'] }}</strong>.
    </div>
    <a href="{{ route('sumate') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold">
        <i class="fas fa-layer-group me-1"></i> Ver planes superadores
    </a>
</div>
@endif
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="fw-bold fs-5">{{ $inmuebles->total() }}</span>
        <span class="text-muted">{{ Str::plural('propiedad', $inmuebles->total()) }}</span>
        @if(request()->hasAny(['q','estado','tipo_operacion','tipo_propiedad','destacado','precio_min','precio_max']))
            <span class="badge text-bg-info ms-1">filtrado</span>
        @endif
    </div>
    <a href="{{ route('inmuebles.create') }}" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-plus me-1"></i> Nuevo</a>
</div>

{{-- Barra de filtros granular --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('inmuebles.index') }}" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label small fw-bold mb-1">Buscar</label>
                <input type="text" name="q" class="form-control form-control-sm" style="width: 220px;" value="{{ request('q') }}" placeholder="Título, dirección o ciudad...">
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="publicado" {{ request('estado') === 'publicado' ? 'selected' : '' }}>Visible</option>
                    <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="cerrada" {{ request('estado') === 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Operación</label>
                <select name="tipo_operacion" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="venta" {{ request('tipo_operacion') === 'venta' ? 'selected' : '' }}>Venta</option>
                    <option value="alquiler" {{ request('tipo_operacion') === 'alquiler' ? 'selected' : '' }}>Alquiler</option>
                </select>
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Tipo</label>
                <select name="tipo_propiedad" class="form-select form-select-sm" style="min-width: 170px;">
                    <option value="">Todos</option>
                    @foreach(\App\Models\Inmueble::gruposTipos() as $grupo => $opciones)
                        <optgroup label="{{ $grupo }}">
                            @foreach($opciones as $val => $lbl)
                                <option value="{{ $val }}" {{ request('tipo_propiedad') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Destacado</label>
                <select name="destacado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="1" {{ request('destacado') === '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ request('destacado') === '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Precio mín.</label>
                <input type="number" name="precio_min" class="form-control form-control-sm" style="width: 110px;" value="{{ request('precio_min') }}" placeholder="$" min="0">
            </div>
            <div>
                <label class="form-label small fw-bold mb-1">Precio máx.</label>
                <input type="number" name="precio_max" class="form-control form-control-sm" style="width: 110px;" value="{{ request('precio_max') }}" placeholder="$" min="0">
            </div>
            <div class="d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-search me-1"></i> Buscar</button>
                <a href="{{ route('inmuebles.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Limpiar filtros"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table id="inmueblesTable" class="table table-sm align-middle mb-0 tabla-compacta" style="width: 100%;">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <th class="d-none d-lg-table-cell">Operación</th>
                    <th class="d-none d-lg-table-cell">Tipo</th>
                    <th>Precio</th>
                    <th class="d-none d-lg-table-cell">m²</th>
                    <th>Estado</th>
                    <th class="text-center d-none d-lg-table-cell" title="Visitas / consultas">Vis.·Cons.</th>
                    <th class="d-none d-lg-table-cell">Comisión</th>
                    <th class="text-center d-none d-lg-table-cell" title="Destacado">Dest.</th>
                    <th class="text-center acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inmuebles as $inmueble)
                <tr>
                    <td class="celda-prop">
                        <div class="fw-semibold">{{ $inmueble->titulo }}</div>
                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $inmueble->ciudad ?: $inmueble->direccion }}</small>
                        @if($inmueble->carga_asistida)
                            <span class="badge text-bg-info ms-1" title="Cargada por el administrador (carga asistida)"><i class="fas fa-hands-helping"></i> asistida</span>
                        @endif
                        @if($inmueble->propnombre)
                            <small class="d-block text-muted" title="{{ $inmueble->propemail }} · {{ $inmueble->proptel }}">Prop.: {{ $inmueble->propnombre }} · DNI {{ $inmueble->propdni }}</small>
                        @endif
                    </td>
                    <td class="d-none d-lg-table-cell">{{ ucfirst($inmueble->tipo_operacion) }}</td>
                    <td class="d-none d-lg-table-cell">{{ $inmueble->tipo_propiedad ? (\App\Models\Inmueble::tiposPropiedad()[$inmueble->tipo_propiedad] ?? ucfirst($inmueble->tipo_propiedad)) : '-' }}</td>
                    <td class="text-nowrap fw-semibold" style="color:#4f46e5;">@dinero($inmueble->precio, 2)</td>
                    <td class="text-nowrap d-none d-lg-table-cell">{{ $inmueble->superficie_cubierta }} m²</td>
                    <td>
                        @if($inmueble->cerrada)
                            <span class="badge text-bg-dark">Cerrada ({{ $inmueble->tipo_cierre }})</span>
                            @if($cierreColumna)
                                @if($inmueble->cierre_estado === 'aprobado')
                                    <span class="badge text-bg-success ms-1" title="Aprobado por el administrador">cierre aprobado</span>
                                @else
                                    <span class="badge text-bg-danger ms-1" title="El administrador lo aprueba cuando verifique el pago de la comisión">pendiente de aprobación</span>
                                @endif
                            @endif
                        @elseif($inmueble->publicado)
                            <span class="badge text-bg-success">Visible</span>
                        @else
                            <span class="badge text-bg-secondary">Borrador</span>
                        @endif
                    </td>
                    <td class="text-center text-nowrap small text-muted d-none d-lg-table-cell" title="Visitas / consultas recibidas">
                        <i class="fas fa-eye"></i> {{ $inmueble->visitas }} · <i class="fas fa-envelope"></i> {{ $inmueble->contactos_count }}
                    </td>
                    <td class="text-nowrap small d-none d-lg-table-cell">
                        @if($inmueble->comision_monto !== null)
                            @dinero((float) $inmueble->comision_monto, 2)
                            @if($inmueble->comision_estado === 'cobrada')
                                <span class="badge text-bg-success">cobrada</span>
                            @else
                                <span class="badge text-bg-warning">pendiente</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center d-none d-lg-table-cell">
                        <input type="checkbox" class="toggle-destacado form-check-input" data-id="{{ $inmueble->id }}" {{ $inmueble->destacado ? 'checked' : '' }} title="Destacar en el portal">
                    </td>
                    <td class="text-center acciones">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('inmuebles.show', $inmueble) }}" class="btn btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-outline-warning" title="Editar"><i class="fas fa-pen"></i></a>
                            <a href="{{ route('inmuebles.addPhotos', $inmueble) }}" class="btn btn-outline-primary" title="Agregar fotos"><i class="fas fa-images"></i></a>
                            <a href="{{ route('inmuebles.deletePhotos', $inmueble) }}" class="btn btn-outline-secondary" title="Quitar fotos"><i class="fas fa-image"></i></a>
                            @if(!$inmueble->cerrada)
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#cerrarModal{{ $inmueble->id }}" title="Cerrar operación ({{ $inmueble->tipo_operacion }})"><i class="fas fa-check"></i></button>
                            @endif
                            <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" title="Eliminar" onclick="return confirm('¿Seguro de eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        @if(request()->hasAny(['q','estado','tipo_operacion','tipo_propiedad','destacado','precio_min','precio_max']))
                            No hay propiedades con esos filtros. <a href="{{ route('inmuebles.index') }}">Limpiar filtros</a>
                        @else
                            Todavía no tenés inmuebles. <a href="{{ route('inmuebles.create') }}">Crear el primero</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Modales de cierre FUERA de la tabla: dentro de la celda sticky .acciones el modal queda
         atrapado en un stacking context bajo y el backdrop lo tapa (se ve gris y no recibe toques) --}}
    @foreach($inmuebles as $inmueble)
                        @if(!$inmueble->cerrada)
                        <!-- Modal de cierre de operación (vendida/alquilada) con comisión opcional -->
                        <div class="modal fade" id="cerrarModal{{ $inmueble->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <form action="{{ route('inmuebles.cerrar', $inmueble) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cerrar operación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p class="text-muted small">La propiedad dejará de verse en el portal. El cierre queda <strong>pendiente de aprobación del administrador</strong> (lo aprueba cuando verifique el pago de la comisión) y recién ahí suma a tu contador de operaciones concretadas.</p>
                                            <div class="form-group mb-3">
                                                <label>Tipo de cierre</label>
                                                <select name="tipo_cierre" class="form-control" required>
                                                    <option value="venta" {{ $inmueble->tipo_operacion === 'venta' ? 'selected' : '' }}>Vendida</option>
                                                    <option value="alquiler" {{ $inmueble->tipo_operacion === 'alquiler' ? 'selected' : '' }}>Alquilada</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 form-group">
                                                    <label>Comisión % @if($comisionHabilitada)(por defecto del portal: {{ rtrim(rtrim(number_format((float) $comisionPctDefault, 2, ',', '.'), '0'), ',') }}%)@else(opcional)@endif</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" class="form-control" placeholder="Ej: 3" value="{{ $comisionHabilitada ? number_format((float) $comisionPctDefault, 2, '.', '') : '' }}">
                                                </div>
                                                <div class="col-6 form-group">
                                                    <label>Comisión $ (opcional)</label>
                                                    <input type="number" step="0.01" min="0" name="comision_monto" class="form-control" placeholder="Monto fijo">
                                                </div>
                                            </div>
                                            @if($comisionHabilitada)
                                            <small class="text-muted d-block">Al confirmar el cierre se registra la <strong>comisión del portal</strong>: por defecto <strong>{{ rtrim(rtrim(number_format((float) $comisionPctDefault, 2, ',', '.'), '0'), ',') }}%</strong> sobre el precio de la propiedad ({{ $inmueble->precio_formateado }}). Podés cambiar el % o poner un monto fijo; <strong>0 = sin comisión</strong>. Queda <strong>pendiente de cobro</strong> hasta que el administrador la marque como cobrada.</small>
                                            @else
                                            <small class="text-muted d-block">Si dejás el % y no el monto, se calcula sobre el precio de la propiedad.</small>
                                            @endif
                                            <small class="text-muted d-block mt-1"><i class="fas fa-scale-balanced me-1"></i>{{ $comisionNota }}</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Confirmar cierre</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
    @endforeach


    @if($inmuebles->hasPages())
    <div class="card-footer py-2 d-flex justify-content-center">
        {{ $inmuebles->links() }}
    </div>
    @endif
</div>
@stop

@section('css')
<style>
    .tabla-compacta { font-size: .85rem; }
    .tabla-compacta th, .tabla-compacta td { padding: .45rem .55rem; vertical-align: middle; }
    .tabla-compacta thead th {
        background: #f8fafc; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em;
        color: #667085; border-bottom: 2px solid #e5e7eb; white-space: nowrap;
    }
    /* Acciones siempre visibles, aunque la tabla scrollee horizontalmente */
    .tabla-compacta .acciones {
        position: sticky; right: 0; background: #fff; white-space: nowrap;
        box-shadow: -8px 0 10px -8px rgba(16,24,40,.25);
    }
    .tabla-compacta tbody tr:hover .acciones { background: #f8fafc; }
    .tabla-compacta .badge { font-size: .68rem; }
    .tabla-compacta .celda-prop { min-width: 240px; }
    /* Mobile: solo Propiedad + Precio + Estado + Acciones; título con ellipsis y acciones compactas */
    @media (max-width: 900px) {
        .tabla-compacta th, .tabla-compacta td { padding: .4rem .45rem; }
        .tabla-compacta .celda-prop { min-width: 150px; max-width: 220px; }
        .tabla-compacta .celda-prop .fw-semibold {
            display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .tabla-compacta .acciones { box-shadow: -6px 0 8px -6px rgba(16,24,40,.3); }
        .tabla-compacta .acciones .btn { padding: .22rem .38rem; font-size: .72rem; }
    }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-destacado').forEach(function (el) {
        el.addEventListener('change', function () {
            var id = this.dataset.id;
            var destacado = this.checked ? 1 : 0;
            fetch('/inmuebles/toggle-destacado/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ destacado: destacado })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) {
                        alert(data.message || 'No se pudo actualizar el destacado.');
                    }
                    location.reload();
                })
                .catch(function () {
                    alert('Error al actualizar el estado de destacado.');
                });
        });
    });
});
</script>
@stop
