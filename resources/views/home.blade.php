@extends('layouts.panel')

@section('title', 'Panel')

@section('content_header')
    <h1>Hola, {{ auth()->user()->name }}</h1>
@stop

@section('content')
@if(!empty($cuenta))
@php $i = $cuenta['inmobiliaria']; @endphp
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">Estado de mi cuenta</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-2">
                <small class="text-muted d-block">Estado</small>
                @if($i->estado === 'activa')
                    <span class="badge badge-success">Activa</span>
                @elseif($i->estado === 'pendiente')
                    <span class="badge badge-warning">Pendiente de aprobación</span>
                @else
                    <span class="badge badge-danger">Suspendida</span>
                @endif
            </div>
            <div class="col-md-3 mb-2">
                <small class="text-muted d-block">Plan</small>
                <strong>{{ $i->plan->nombre ?? 'Sin plan' }}</strong>
                <span class="text-muted">(${{ number_format($i->plan->precio_mensual ?? 0, 0) }}/mes)</span>
            </div>
            <div class="col-md-3 mb-2">
                <small class="text-muted d-block">Vencimiento del plan</small>
                {{ $i->plan_vence_at ? $i->plan_vence_at->format('d/m/Y') : 'Sin vencimiento' }}
            </div>
            <div class="col-md-3 mb-2">
                <small class="text-muted d-block">Boletos pendientes</small>
                <a href="{{ route('boletos.mios') }}">{{ $cuenta['boletos_pendientes'] }} pendiente(s)</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 mb-2">
                <small class="text-muted">Publicaciones vigentes: {{ $cuenta['publicadas'] }} / {{ $cuenta['limite_inmuebles'] }} (de {{ $cuenta['total'] }} creadas)</small>
                @php $pct = min(100, (int) round(100 * $cuenta['publicadas'] / max(1, $cuenta['limite_inmuebles']))) @endphp
                <div class="progress mt-1" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <small class="text-muted">Destacadas: {{ $cuenta['destacadas'] }} / {{ $cuenta['limite_destacados'] }}</small>
                @php $pct2 = min(100, (int) round(100 * $cuenta['destacadas'] / max(1, $cuenta['limite_destacados']))) @endphp
                <div class="progress mt-1" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: {{ $pct2 }}%"></div>
                </div>
            </div>
        </div>
        @if($i->estado === 'pendiente')
            <div class="alert alert-warning mt-3 mb-0">Tu inmobiliaria está pendiente de aprobación por el administrador del portal.</div>
        @elseif($i->estado === 'suspendida')
            <div class="alert alert-danger mt-3 mb-0">Tu cuenta está suspendida. Contactá al administrador del portal.</div>
        @elseif($i->plan_vence_at && $i->plan_vence_at->isPast())
            <div class="alert alert-warning mt-3 mb-0">Tu suscripción está vencida: en el próximo acceso pasás automáticamente al plan Gratis.</div>
        @endif
    </div>
</div>
@endif

<div class="row g-3">
    @if(auth()->user()->hasAnyRole(['admin', 'inmobiliaria']))
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('inmuebles.index') }}" class="dashboard-card">
            <i class="fas fa-key"></i>
            <span class="dc-title">Mis Inmuebles</span>
            <span class="dc-sub">Ver y editar publicaciones</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('inmuebles.create') }}" class="dashboard-card">
            <i class="fas fa-plus"></i>
            <span class="dc-title">Nuevo Inmueble</span>
            <span class="dc-sub">Publicar una propiedad</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('inmobiliaria.perfil') }}" class="dashboard-card">
            <i class="fas fa-store"></i>
            <span class="dc-title">Mi Inmobiliaria</span>
            <span class="dc-sub">Perfil público y datos</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('boletos.mios') }}" class="dashboard-card">
            <i class="fas fa-receipt"></i>
            <span class="dc-title">Mis Boletos</span>
            <span class="dc-sub">Pagos y suscripción</span>
        </a>
    </div>
    @endif
    @if(auth()->user()->hasRole('admin'))
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.inmobiliarias') }}" class="dashboard-card">
            <i class="fas fa-users-cog"></i>
            <span class="dc-title">Inmobiliarias</span>
            <span class="dc-sub">Aprobar y administrar</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('admin.boletos') }}" class="dashboard-card">
            <i class="fas fa-file-invoice-dollar"></i>
            <span class="dc-title">Boletos</span>
            <span class="dc-sub">Crear cobros de suscripción</span>
        </a>
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.dashboard-card {
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: #fff;
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 20px;
    text-decoration: none;
    color: var(--text);
    transition: transform 0.15s, box-shadow 0.15s;
}
.dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,0.10); color: var(--text); }
.dashboard-card i { font-size: 22px; color: var(--accent); margin-bottom: 6px; }
.dc-title { font-weight: 600; font-size: 15px; }
.dc-sub { color: var(--muted); font-size: 12.5px; }
</style>
@stop
