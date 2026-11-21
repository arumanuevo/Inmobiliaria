@extends('layouts.panel')

@section('title', 'Boletos de suscripción')

@section('content_header')
    <h1>Boletos de suscripción</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
            <h3 class="card-title">Crear boleto</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.boletos.store') }}" method="POST" class="row">
                @csrf
                <div class="col-md-3 form-group">
                    <label>Inmobiliaria</label>
                    <select name="inmobiliaria_id" class="form-control" required>
                        @foreach($inmobiliarias as $inm)
                            <option value="{{ $inm->id }}">{{ $inm->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Concepto</label>
                    <input type="text" name="concepto" class="form-control" placeholder="Ej: Suscripción Premium - Marzo" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Monto (ARS)</label>
                    <input type="number" step="0.01" min="0" name="monto" class="form-control" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Vence</label>
                    <input type="date" name="vence_at" class="form-control">
                </div>
                <div class="col-md-2 form-group">
                    <label>Plan a asignar (opcional)</label>
                    <select name="plan_id" class="form-control">
                        <option value="">Ninguno</option>
                        @foreach($planes as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">Crear boleto</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Todos los boletos ({{ $boletos->total() }})</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Inmobiliaria</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Pagado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boletos as $boleto)
                    <tr>
                        <td>{{ $boleto->id }}</td>
                        <td>{{ $boleto->inmobiliaria->nombre_comercial ?? '-' }}</td>
                        <td>{{ $boleto->concepto }}</td>
                        <td>@dinero($boleto->monto, 2)</td>
                        <td>{{ $boleto->plan->nombre ?? '-' }}</td>
                        <td>{{ $boleto->vence_at ? $boleto->vence_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($boleto->estado === 'pagado')
                                <span class="badge badge-success">Pagado</span>
                            @elseif($boleto->estado === 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif($boleto->estado === 'vencido')
                                <span class="badge badge-danger">Vencido</span>
                            @else
                                <span class="badge badge-secondary">Cancelado</span>
                            @endif
                        </td>
                        <td>{{ $boleto->pagado_at ? $boleto->pagado_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            @if($boleto->estado === 'pendiente')
                                <form action="{{ route('admin.boletos.estado', $boleto->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="pagado">
                                    <button class="btn btn-success btn-sm" onclick="return confirm('¿Marcar como PAGADO (ej. pago en efectivo)?')">Pagado</button>
                                </form>
                                <form action="{{ route('admin.boletos.estado', $boleto->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="cancelado">
                                    <button class="btn btn-secondary btn-sm" onclick="return confirm('¿Cancelar el boleto?')">Cancelar</button>
                                </form>
                            @elseif($boleto->estado === 'cancelado' || $boleto->estado === 'vencido')
                                <form action="{{ route('admin.boletos.estado', $boleto->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="estado" value="pendiente">
                                    <button class="btn btn-warning btn-sm">Reabrir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $boletos->links() }}
        </div>
    </div>
</div>
@stop
