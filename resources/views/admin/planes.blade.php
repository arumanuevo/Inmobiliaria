@extends('layouts.panel')

@section('title', 'Paquetes de suscripción')

@section('content_header')
    <h1>Paquetes de suscripción</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Crear paquete</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.planes.store') }}" method="POST" class="row">
                @csrf
                <div class="col-md-2 form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Starter" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" class="form-control" placeholder="ej: starter" required pattern="[a-z0-9-]+">
                </div>
                <div class="col-md-2 form-group">
                    <label>Precio mensual (ARS)</label>
                    <input type="number" step="0.01" min="0" name="precio_mensual" class="form-control" value="0" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Límite publicaciones</label>
                    <input type="number" min="0" name="limite_inmuebles" class="form-control" value="5" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Límite destacados</label>
                    <input type="number" min="0" name="limite_destacados" class="form-control" value="1" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Límite fotos por publicación</label>
                    <input type="number" min="0" name="limite_fotos" class="form-control" value="10" required>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">Crear paquete</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Paquetes existentes ({{ $planes->count() }})</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Precio/mes</th>
                        <th>Publicaciones</th>
                        <th>Destacados</th>
                        <th>Fotos/pub.</th>
                        <th>Inmobiliarias</th>
                        <th>Activo</th>
                        <th class="text-center">Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($planes as $plan)
                    <tr>
                        <form action="{{ route('admin.planes.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <td>
                            <input type="text" name="nombre" class="form-control form-control-sm" value="{{ $plan->nombre }}" required>
                            <small class="text-muted">{{ $plan->slug }}</small>
                        </td>
                        <td><small>{{ $plan->slug }}</small></td>
                        <td>
                            <input type="number" step="0.01" min="0" name="precio_mensual" class="form-control form-control-sm" value="{{ $plan->precio_mensual }}" required>
                        </td>
                        <td>
                            <input type="number" min="0" name="limite_inmuebles" class="form-control form-control-sm" value="{{ $plan->limite_inmuebles }}" required>
                        </td>
                        <td>
                            <input type="number" min="0" name="limite_destacados" class="form-control form-control-sm" value="{{ $plan->limite_destacados }}" required>
                        </td>
                        <td>
                            <input type="number" min="0" name="limite_fotos" class="form-control form-control-sm" value="{{ $plan->limite_fotos }}" required>
                        </td>
                        <td>{{ $plan->inmobiliarias_count }}</td>
                        <td>
                            @if($plan->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                        </form>
                        <form action="{{ route('admin.planes.toggle', $plan->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="activo" value="{{ $plan->activo ? '0' : '1' }}">
                            <button type="submit" class="btn btn-{{ $plan->activo ? 'secondary' : 'success' }} btn-sm">
                                {{ $plan->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Degradación automática:</strong> cuando una inmobiliaria baja de paquete (o vence su suscripción y pasa al plan Gratis),
        las publicaciones que excedan el nuevo límite pasan a <em>borrador</em> (no se borran) y los destacados excedentes se quitan.
        Al volver a subir de paquete, la inmobiliaria puede republicar manualmente desde su panel.
    </div>
</div>
@stop
