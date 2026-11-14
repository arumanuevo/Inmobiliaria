@extends('layouts.panel')

@section('title', 'Listado de Inmuebles')

@section('content_header')
    <h1>Listado de Inmuebles</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resultados</h3>
        </div>
        <div class="card-body">
            <table id="inmueblesTable" class="table table-bordered table-striped">
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
                        <th class="text-center">Destacado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inmuebles as $inmueble)
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
                        <td class="text-center">
                            <input type="checkbox" class="toggle-destacado" data-id="{{ $inmueble->id }}" {{ $inmueble->destacado ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('inmuebles.show', $inmueble) }}" class="btn btn-info btn-sm">Ver</a>
                                <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-warning btn-sm">Editar</a>
                                <a href="{{ route('inmuebles.addPhotos', $inmueble) }}" class="btn btn-primary btn-sm">Agregar Fotos</a>
                                <a href="{{ route('inmuebles.deletePhotos', $inmueble) }}" class="btn btn-danger btn-sm">Borrar Fotos</a>
                                <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inmueblesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json"
                },
                "paging": true,
                "lengthChange": true
            });

            $('.toggle-destacado').change(function() {
                var inmuebleId = $(this).data('id');
                var destacado = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/inmuebles/toggle-destacado/' + inmuebleId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        destacado: destacado
                    },
                    success: function(response) {
                        if (response.success) {
                            //alert('Estado de destacado actualizado con éxito.');
                            location.reload(); // Recargar la página para reflejar los cambios
                        } else {
                            alert('Error al actualizar el estado de destacado.');
                        }
                    },
                    error: function() {
                        alert('Error al actualizar el estado de destacado.');
                    }
                });
            });
        });
    </script>
@stop



