@extends('layouts.panel')

@section('title', 'Mis boletos')

@section('content_header')
    <h1>Mis boletos</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Boletos y pagos ({{ $boletos->total() }})</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Pagado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletos as $boleto)
                    <tr>
                        <td>{{ $boleto->id }}</td>
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
                            @if($boleto->estado === 'pendiente' && config('services.mercadopago.access_token'))
                                <form action="{{ route('boletos.pagar', $boleto) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Pagar con Mercado Pago</button>
                                </form>
                            @elseif($boleto->estado === 'pendiente')
                                <small class="text-muted">Pago online no disponible</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No tenés boletos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $boletos->links() }}
            <a href="{{ route('inmobiliaria.perfil') }}" class="btn btn-secondary btn-sm float-right">Volver a mi perfil</a>
        </div>
    </div>
</div>
@stop
