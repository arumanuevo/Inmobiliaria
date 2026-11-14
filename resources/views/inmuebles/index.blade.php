@extends('layouts.panel')

@section('title', 'Mis Inmuebles')

@section('content_header')
    <h1>Mis Inmuebles</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Resultados ({{ $inmuebles->total() }})</h3>
        <a href="{{ route('inmuebles.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
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
                        @if($inmueble->publicado)
                            <span class="badge badge-success">Visible</span>
                        @else
                            <span class="badge badge-secondary">Borrador</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="toggle-destacado form-check-input" data-id="{{ $inmueble->id }}" {{ $inmueble->destacado ? 'checked' : '' }}>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('inmuebles.show', $inmueble) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-warning btn-sm">Editar</a>
                            <a href="{{ route('inmuebles.addPhotos', $inmueble) }}" class="btn btn-primary btn-sm">Fotos</a>
                            <a href="{{ route('inmuebles.deletePhotos', $inmueble) }}" class="btn btn-secondary btn-sm">Borrar fotos</a>
                            <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted py-4">
                        Todavía no tenés inmuebles. <a href="{{ route('inmuebles.create') }}">Crear el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $inmuebles->links() }}
    </div>
</div>
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
