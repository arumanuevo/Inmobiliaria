@extends('layouts.panel')

@section('title', 'Configuración del portal')

@section('content_header')
    <h1><i class="fas fa-sliders me-2"></i>Configuración del portal</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(!$tablaExiste)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Todavía no existe la tabla <code>configuracion</code> en la base de datos. Corré <code>database/sql/saas_configuracion.sql</code> (o <code>instalar_todo.sql</code>) en phpMyAdmin para persistir los cambios. Mientras tanto se usan los valores por defecto.
    </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-hand-holding-usd me-2"></i>Comisión por operación cerrada</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Cuando una inmobiliaria <strong>cierra una operación</strong> (venta/alquiler) desde <em>Mis Inmuebles</em>, el sistema registra la comisión del portal sobre el precio declarado.
                Acá definís si está habilitada, el <strong>porcentaje por defecto</strong> (se precarga en el modal de cierre; el usuario puede modificarlo o poner un monto fijo, 0 = sin comisión) y la <strong>aclaración legal</strong> que se muestra en el cierre, en la <a href="{{ route('aviso-legal') }}" target="_blank">página legal</a> y en la <a href="{{ route('ayuda') }}">ayuda</a>.
                La comisión queda <span class="badge badge-warning">pendiente</span> hasta que la marcas <span class="badge badge-success">cobrada</span> en <a href="{{ route('admin.operaciones') }}">Operaciones</a>.
            </div>
            <form action="{{ route('admin.configuracion.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4 form-group">
                        <label class="fw-bold">¿Comisión habilitada?</label>
                        <select name="comision_habilitada" class="form-control" required>
                            <option value="1" {{ $valores['comision_habilitada'] === '1' ? 'selected' : '' }}>Sí — se registra comisión al cerrar</option>
                            <option value="0" {{ $valores['comision_habilitada'] === '0' ? 'selected' : '' }}>No — el cierre no propone comisión</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="fw-bold">Porcentaje por defecto (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="comision_pct_default" class="form-control" value="{{ $valores['comision_pct_default'] }}" required>
                        <small class="text-muted">Ej: 3 = 3% del precio de la operación. Se calcula automáticamente si el usuario deja el monto vacío.</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="fw-bold">Aclaración legal</label>
                        <textarea name="comision_nota" class="form-control" rows="4" maxlength="1000" required>{{ $valores['comision_nota'] }}</textarea>
                        <small class="text-muted">Se muestra textual al cerrar la operación, en la página legal y en la ayuda.</small>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i> Guardar configuración</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
