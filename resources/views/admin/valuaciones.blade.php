@extends('layouts.panel')

@section('title', 'Valuaciones de mercado')

@section('content_header')
    <h1><i class="fas fa-chart-line me-2"></i>Valuaciones de mercado</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(!$tablaExiste)
    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-1"></i>Todavía no existe la tabla <code>valuaciones</code> en la base: corré <code>database/sql/saas_valuaciones.sql</code> (o <code>instalar_todo.sql</code>) en phpMyAdmin.</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-center"><div class="card-body">
                <div class="h3 mb-1">{{ $total }}</div>
                <div class="text-muted">valuaciones realizadas</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card text-center"><div class="card-body">
                <div class="h3 mb-1 text-primary">{{ $hoy }}</div>
                <div class="text-muted">hoy</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card text-center"><div class="card-body">
                <div class="h3 mb-1 text-success">{{ $detalladas }}</div>
                <div class="text-muted">detalladas (Premium)</div>
            </div></div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Índice de mercado por zona (sobre avisos publicados, venta)</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Zona</th><th>Avisos</th><th>Mediana $/m²</th><th>Promedio $/m²</th><th>Tendencia 90d</th></tr></thead>
                        <tbody>
                        @forelse($indice as $z)
                            <tr>
                                <td class="text-capitalize">{{ $z['ciudad'] }}</td>
                                <td>{{ $z['avisos'] }}</td>
                                <td>@dinero((float) $z['pm2_mediana'], 0)</td>
                                <td>@dinero((float) $z['pm2_promedio'], 0)</td>
                                <td>
                                    @if($z['tendencia_pct'] !== null)
                                        <span class="badge {{ $z['tendencia_pct'] >= 0 ? 'badge-danger' : 'badge-success' }}">{{ $z['tendencia_pct'] > 0 ? '+' : '' }}{{ $z['tendencia_pct'] }}%</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Sin datos todavía.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Valuaciones por zona</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Zona</th><th>Cant.</th><th>Valor medio prom.</th><th>$/m² prom.</th></tr></thead>
                        <tbody>
                        @forelse($porCiudad as $c)
                            <tr>
                                <td class="text-capitalize">{{ $c->ciudad }}</td>
                                <td>{{ $c->n }}</td>
                                <td>@dinero((float) $c->prom, 0)</td>
                                <td>@dinero((float) $c->pm2, 0)</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin valuaciones todavía.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Últimas valuaciones</h3></div>
        <div class="card-body table-responsive">
            <table class="table table-sm align-middle">
                <thead><tr><th>#</th><th>Fecha</th><th>Inmobiliaria</th><th>Ubicación</th><th>Sup.</th><th>Valor est.</th><th>Rango</th><th>Tipo</th></tr></thead>
                <tbody>
                @forelse($ultimas as $v)
                    <tr>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->created_at ? $v->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $v->inmobiliaria->nombre_comercial ?? '-' }}</td>
                        <td>{{ $v->direccion ?: ($v->ciudad ?: '-') }}</td>
                        <td>{{ $v->sup_cubierta }} m²</td>
                        <td class="fw-semibold">@dinero((float) $v->valor_medio, 0)</td>
                        <td class="text-muted small">@dinero((float) $v->valor_min, 0) – @dinero((float) $v->valor_max, 0)</td>
                        <td>
                            @if($v->detallada)<span class="badge badge-success">Premium</span>
                            @else<span class="badge badge-secondary">básica</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Sin valuaciones todavía.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
