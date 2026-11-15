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
                    <th class="text-center">Destacado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inmuebles as $inmueble)
                <tr>
                    <td>{{ $inmueble->titulo }}</td>
                    <td>{{ ucfirst($inmueble->tipo_operacion) }}</td>
                    <td>{{ $inmueble->inmobiliaria->nombre_comercial ?? '-' }}</td>
                    <td>${{ number_format($inmueble->precio, 2) }}</td>
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
                            <form action="{{ route('inmuebles.cerrar', $inmueble) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="tipo_cierre" value="{{ $inmueble->tipo_operacion }}">
                                <button class="btn btn-dark btn-sm" title="Marcar como {{ $inmueble->tipo_operacion }}da" onclick="return confirm('¿Marcar esta propiedad como {{ $inmueble->tipo_operacion }}da? Dejará de verse en el portal.')"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Seguro de eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center text-muted py-4">
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
            { orderable: false, searchable: false, targets: [12, 13] }
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
