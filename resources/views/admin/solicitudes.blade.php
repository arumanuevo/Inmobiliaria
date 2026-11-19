@extends('layouts.panel')

@section('title', 'Solicitudes de carga asistida')

@section('content_header')
    <h1>Solicitudes de carga asistida
        @if($pendientes > 0)<span class="badge badge-warning ms-2">{{ $pendientes }} pendientes</span>@endif
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="card-title mb-0">Cola de solicitudes ({{ $solicitudes->total() }})</h3>
            <form method="GET" action="{{ route('admin.solicitudes') }}" class="d-flex gap-2">
                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach(['pendiente','en_carga','cargada','cancelada'] as $e)
                        <option value="{{ $e }}" {{ ($estado ?? '') === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Inmobiliaria</th>
                        <th>Solicitud</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $s)
                    <tr>
                        <td>#{{ $s->id }}</td>
                        <td>
                            {{ $s->inmobiliaria->nombre_comercial ?? '-' }}
                            <br><small class="text-muted">{{ $s->inmobiliaria->email ?? ($s->inmobiliaria->usuario->email ?? '-') }}</small>
                        </td>
                        <td>
                            <strong>{{ $s->titulo ?: 'Sin título' }}</strong>
                            <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($s->detalle, 140) }}</small>
                        </td>
                        <td>
                            @if($s->estado === 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif($s->estado === 'en_carga')
                                <span class="badge badge-info">En carga</span>
                            @elseif($s->estado === 'cargada')
                                <span class="badge badge-success">Cargada</span>
                            @else
                                <span class="badge badge-secondary">Cancelada</span>
                            @endif
                        </td>
                        <td>{{ $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            @if(in_array($s->estado, ['pendiente', 'en_carga']))
                                <a href="{{ route('admin.carga.form', ['id' => $s->inmobiliaria_id, 'solicitud' => $s->id]) }}" class="btn btn-primary btn-sm rounded-pill" title="Cargar la propiedad ahora (marca la solicitud como cargada)">
                                    <i class="fas fa-hands-helping"></i> Atender
                                </a>
                                @if($s->estado === 'pendiente')
                                <form action="{{ route('admin.solicitudes.estado', $s->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="en_carga">
                                    <button class="btn btn-info btn-sm rounded-pill">En carga</button>
                                </form>
                                @endif
                                <form action="{{ route('admin.solicitudes.estado', $s->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="cancelada">
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="return confirm('¿Cancelar la solicitud #{{ $s->id }}?')">Cancelar</button>
                                </form>
                            @elseif($s->estado === 'cargada' && $s->inmueble)
                                @if($s->inmueble->publicado && $s->inmobiliaria)
                                    <a href="{{ route('propiedad.show', [$s->inmobiliaria->slug, $s->inmueble->id]) }}" target="_blank" class="btn btn-success btn-sm rounded-pill"><i class="fas fa-eye"></i> Ver</a>
                                @else
                                    <span class="text-muted small">cargada</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay solicitudes con ese criterio.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $solicitudes->links() }}</div>
    </div>
</div>
@stop
