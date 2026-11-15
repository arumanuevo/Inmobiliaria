@extends('layouts.panel')

@section('title', 'Mis búsquedas guardadas')

@section('content_header')
    <h1>Mis búsquedas guardadas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Alertas activas ({{ $busquedas->count() }})</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">
            Cuando se publique una propiedad que coincida con alguna de estas búsquedas, te avisamos por email.
            También podés guardar búsquedas desde la página de <a href="{{ route('propiedades.index') }}">Propiedades</a> con el botón "Avisame".
        </p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Búsqueda</th>
                        <th>Email</th>
                        <th>Guardada el</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($busquedas as $busqueda)
                    <tr>
                        <td><i class="fas fa-bell text-primary mr-1"></i> {{ $busqueda->resumenFiltros() }}</td>
                        <td>{{ $busqueda->email }}</td>
                        <td>{{ $busqueda->created_at ? $busqueda->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            <form action="{{ route('busquedas.destroy', $busqueda) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Eliminar alerta" onclick="return confirm('¿Eliminar esta búsqueda guardada? Ya no recibirás alertas de ella.')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Todavía no guardaste ninguna búsqueda. Buscá en <a href="{{ route('propiedades.index') }}">Propiedades</a> y tocá "Avisame cuando aparezca algo así".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
