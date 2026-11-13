@extends('adminlte::page')

@section('title', 'Inmobiliarias registradas')

@section('content_header')
    <h1>Inmobiliarias registradas</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Todas las inmobiliarias ({{ $inmobiliarias->total() }})</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Inmobiliaria</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Plan</th>
                        <th>Propiedades</th>
                        <th>Perfil público</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inmobiliarias as $inmobiliaria)
                    <tr>
                        <td>{{ $inmobiliaria->id }}</td>
                        <td>
                            {{ $inmobiliaria->nombre_comercial }}
                            <br><small class="text-muted">/{{ $inmobiliaria->slug }}</small>
                        </td>
                        <td>
                            {{ $inmobiliaria->usuario->name ?? '-' }}
                            <br><small class="text-muted">{{ $inmobiliaria->usuario->email ?? '-' }}</small>
                        </td>
                        <td>
                            @if($inmobiliaria->estado === 'activa')
                                <span class="badge badge-success">Activa</span>
                            @elseif($inmobiliaria->estado === 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @else
                                <span class="badge badge-danger">Suspendida</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.inmobiliarias.plan', $inmobiliaria->id) }}" method="POST" class="form-inline">
                                @csrf
                                <select name="plan_id" class="form-control form-control-sm mr-1">
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}" {{ $inmobiliaria->plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->nombre }} (${{ number_format($plan->precio_mensual, 0) }}/mes)
                                        </option>
                                    @endforeach
                                </select>
                                <input type="date" name="plan_vence_at" class="form-control form-control-sm mr-1"
                                       value="{{ $inmobiliaria->plan_vence_at ? $inmobiliaria->plan_vence_at->format('Y-m-d') : '' }}">
                                <button type="submit" class="btn btn-sm btn-primary">Guardar plan</button>
                            </form>
                        </td>
                        <td>{{ $inmobiliaria->inmuebles()->count() }}</td>
                        <td>
                            @if($inmobiliaria->estado === 'activa')
                                <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" target="_blank" class="btn btn-sm btn-info">Ver</a>
                            @else
                                <small class="text-muted">No visible</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.inmobiliarias.estado', $inmobiliaria->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @if($inmobiliaria->estado !== 'activa')
                                    <input type="hidden" name="estado" value="activa">
                                    <button class="btn btn-success btn-sm" onclick="return confirm('¿Activar esta inmobiliaria?')">Aprobar</button>
                                @endif
                                @if($inmobiliaria->estado !== 'suspendida')
                                    <input type="hidden" name="estado" value="suspendida">
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Suspender esta inmobiliaria?')">Suspender</button>
                                @endif
                                @if($inmobiliaria->estado !== 'pendiente')
                                    <input type="hidden" name="estado" value="pendiente">
                                    <button class="btn btn-warning btn-sm" onclick="return confirm('¿Volver a pendiente?')">Pendiente</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $inmobiliarias->links() }}
        </div>
    </div>
</div>
@stop
