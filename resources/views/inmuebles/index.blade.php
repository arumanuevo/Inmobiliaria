@extends('layouts.panel')

@section('title', 'Mis Inmuebles')

@section('content_header')
    <h1>Mis Inmuebles</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Resultados ({{ $inmuebles->count() }})</h3>
        <a href="{{ route('inmuebles.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
    </div>
    <div class="card-body table-responsive">
        <table id="inmueblesTable" class="table table-bordered table-striped align-middle" style="width: 100%;">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Inmobiliaria</th>
                    <th>Precio</th>
                    <th>Superficie</th>
                    <th>Propietario</th>
                    <th>DNI</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Publicado</th>
                    <th>Visitas</th>
                    <th>Consultas</th>
                    <th>Comisión</th>
                    <th class="text-center">Destacado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inmuebles as $inmueble)
                <tr>
                    <td>
                        {{ $inmueble->titulo }}
                        @if($inmueble->carga_asistida)
                            <span class="badge badge-info" title="Cargada por el administrador (carga asistida)"><i class="fas fa-hands-helping"></i> carga asistida</span>
                        @endif
                        @if($inmueble->puntaje_entorno !== null)
                            <span class="badge badge-success" title="Puntaje de entorno calculado con OpenStreetMap (arteria, transporte, comercios, servicios)"><i class="fas fa-chart-line"></i> {{ $inmueble->puntaje_entorno }}/100</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($inmueble->tipo_operacion) }}</td>
                    <td>{{ $inmueble->inmobiliaria->nombre_comercial ?? '-' }}</td>
                    <td>@dinero($inmueble->precio, 2)</td>
                    <td>{{ $inmueble->superficie_cubierta }} m²</td>
                    <td>{{ $inmueble->propnombre }}</td>
                    <td>{{ $inmueble->propdni }}</td>
                    <td>{{ $inmueble->propemail }}</td>
                    <td>{{ $inmueble->proptel }}</td>
                    <td>
                        @if($inmueble->cerrada)
                            <span class="badge badge-dark">Cerrada ({{ $inmueble->tipo_cierre }})</span>
                        @elseif($inmueble->publicado)
                            <span class="badge badge-success">Visible</span>
                        @else
                            <span class="badge badge-secondary">Borrador</span>
                        @endif
                    </td>
                    <td>{{ $inmueble->visitas }}</td>
                    <td>{{ $inmueble->contactos_count }}</td>
                    <td>
                        @if($inmueble->comision_monto !== null)
                            @dinero((float) $inmueble->comision_monto, 2)
                            @if($inmueble->comision_estado === 'cobrada')
                                <span class="badge badge-success">cobrada</span>
                            @else
                                <span class="badge badge-warning">pendiente</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="toggle-destacado form-check-input" data-id="{{ $inmueble->id }}" {{ $inmueble->destacado ? 'checked' : '' }}>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('inmuebles.show', $inmueble) }}" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-pen"></i></a>
                            <a href="{{ route('inmuebles.addPhotos', $inmueble) }}" class="btn btn-primary btn-sm" title="Agregar fotos"><i class="fas fa-images"></i></a>
                            <a href="{{ route('inmuebles.deletePhotos', $inmueble) }}" class="btn btn-secondary btn-sm" title="Borrar fotos"><i class="fas fa-image"></i></a>
                            @if(!$inmueble->cerrada)
                            <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#cerrarModal{{ $inmueble->id }}" title="Marcar como {{ $inmueble->tipo_operacion }}da"><i class="fas fa-check"></i></button>
                            @endif
                            <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Seguro de eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>

                        @if(!$inmueble->cerrada)
                        <!-- Modal de cierre de operación (vendida/alquilada) con comisión opcional -->
                        <div class="modal fade" id="cerrarModal{{ $inmueble->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('inmuebles.cerrar', $inmueble) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cerrar operación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p class="text-muted small">La propiedad dejará de verse en el portal y sumará a tu contador de operaciones concretadas.</p>
                                            <div class="form-group mb-3">
                                                <label>Tipo de cierre</label>
                                                <select name="tipo_cierre" class="form-control" required>
                                                    <option value="venta" {{ $inmueble->tipo_operacion === 'venta' ? 'selected' : '' }}>Vendida</option>
                                                    <option value="alquiler" {{ $inmueble->tipo_operacion === 'alquiler' ? 'selected' : '' }}>Alquilada</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-6 form-group">
                                                    <label>Comisión % (opcional)</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" class="form-control" placeholder="Ej: 3">
                                                </div>
                                                <div class="col-6 form-group">
                                                    <label>Comisión $ (opcional)</label>
                                                    <input type="number" step="0.01" min="0" name="comision_monto" class="form-control" placeholder="Monto fijo">
                                                </div>
                                            </div>
                                            <small class="text-muted">Si dejás el % y no el monto, se calcula sobre el precio de la propiedad.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-dark"><i class="fas fa-check"></i> Confirmar cierre</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="text-center text-muted py-4">
                        Todavía no tenés inmuebles. <a href="{{ route('inmuebles.create') }}">Crear el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#inmueblesTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, searchable: false, targets: [13, 14] }
        ]
    });

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
