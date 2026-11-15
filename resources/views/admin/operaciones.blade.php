@extends('layouts.panel')

@section('title', 'Operaciones y comisiones')

@section('content_header')
    <h1>Operaciones concretadas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1">{{ $totales['cantidad'] }}</div>
                    <div class="text-muted">operaciones cerradas</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1 text-warning">${{ number_format((float) $totales['pendiente'], 2) }}</div>
                    <div class="text-muted">comisiones pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1 text-success">${{ number_format((float) $totales['cobrada'], 2) }}</div>
                    <div class="text-muted">comisiones cobradas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Propiedades vendidas/alquiladas ({{ $operaciones->total() }})</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Propiedad</th>
                        <th>Inmobiliaria</th>
                        <th>Cierre</th>
                        <th>Precio</th>
                        <th>Comisión</th>
                        <th>Estado comisión</th>
                        <th>Cerrada el</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operaciones as $op)
                    <tr>
                        <td>{{ $op->titulo }}</td>
                        <td>{{ $op->inmobiliaria->nombre_comercial ?? '-' }}</td>
                        <td><span class="badge badge-dark">{{ ucfirst($op->tipo_cierre) }}</span></td>
                        <td>{{ $op->precio_formateado }}</td>
                        <td>
                            @if($op->comision_monto !== null)
                                ${{ number_format((float) $op->comision_monto, 2) }}
                                @if($op->comision_porcentaje !== null)<small class="text-muted">({{ $op->comision_porcentaje }}%)</small>@endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($op->comision_monto !== null)
                                @if($op->comision_estado === 'cobrada')
                                    <span class="badge badge-success">Cobrada</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $op->cerrada_at ? $op->cerrada_at->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            @if($op->comision_monto !== null && $op->comision_estado !== 'cobrada')
                                <form action="{{ route('admin.operaciones.comision', $op->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="cobrada">
                                    <button class="btn btn-success btn-sm" onclick="return confirm('¿Marcar comisión como cobrada?')"><i class="fas fa-check"></i> Cobrada</button>
                                </form>
                            @elseif($op->comision_monto !== null)
                                <form action="{{ route('admin.operaciones.comision', $op->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="pendiente">
                                    <button class="btn btn-warning btn-sm" onclick="return confirm('¿Volver a pendiente?')"><i class="fas fa-undo"></i> Pendiente</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Todavía no hay operaciones cerradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $operaciones->links() }}
        </div>
    </div>
</div>
@stop
