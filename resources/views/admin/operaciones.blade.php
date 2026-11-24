@extends('layouts.panel')

@section('title', 'Operaciones y comisiones')

@section('content_header')
    <h1>Operaciones concretadas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6 col-lg-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1">{{ $totales['cantidad'] }}</div>
                    <div class="text-muted">operaciones cerradas</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1 text-danger">{{ $totales['pendientes_aprobacion'] }}</div>
                    <div class="text-muted">cierres pendientes de tu aprobación</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1 text-warning">@dinero((float) $totales['pendiente'], 2)</div>
                    <div class="text-muted">comisiones pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 mb-1 text-success">@dinero((float) $totales['cobrada'], 2)</div>
                    <div class="text-muted">comisiones cobradas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info py-2">
        <i class="fas fa-info-circle me-1"></i>
        El usuario registra el cierre, pero <strong>no queda habilitado como operación concretada hasta que VOS lo aprobás acá</strong> (botón "Aprobar cierre"), normalmente después de verificar el pago de la comisión (que después marcás "Cobrada").
    </div>

    @if(auth()->user()->esSuperAdmin())
    <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="fas fa-hand-holding-usd me-1"></i>Comisión del portal: <strong>{{ $comisionHabilitada ? rtrim(rtrim(number_format((float) $comisionPctDefault, 2, ',', '.'), '0'), ',') . '% por defecto sobre el precio al cerrar' : 'DESACTIVADA' }}</strong></span>
        <a href="{{ route('admin.configuracion') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fas fa-sliders me-1"></i> Configurar</a>
    </div>
    @endif

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
                        <th>Aprobación</th>
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
                                @dinero((float) $op->comision_monto, 2)
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
                        <td>
                            @if($cierreColumna)
                                @if($op->cierre_estado === 'aprobado')
                                    <span class="badge badge-success">Aprobado</span>
                                    <small class="text-muted d-block">{{ $op->cierre_aprobado_at ? $op->cierre_aprobado_at->format('d/m/Y H:i') : '' }}</small>
                                @else
                                    <span class="badge badge-danger">Pendiente de aprobación</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $op->cerrada_at ? $op->cerrada_at->format('d/m/Y') : '-' }}</td>
                        <td class="text-center text-nowrap">
                            @if($cierreColumna)
                                @if($op->cierre_estado === 'pendiente')
                                    <form action="{{ route('admin.operaciones.aprobar', $op->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="estado" value="aprobado">
                                        <button class="btn btn-primary btn-sm" onclick="return confirm('¿Aprobar este cierre? Usalo cuando ya hayas verificado el pago de la comisión.')"><i class="fas fa-stamp"></i> Aprobar cierre</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.operaciones.aprobar', $op->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="estado" value="pendiente">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="return confirm('¿Volver a pendiente de aprobación?')"><i class="fas fa-undo"></i> Desaprobar</button>
                                    </form>
                                @endif
                            @endif
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
                        <td colspan="9" class="text-center text-muted py-4">Todavía no hay operaciones cerradas.</td>
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
