@extends('layouts.panel')

@section('title', 'Panel')

@section('content_header')
    <h1>Hola, {{ auth()->user()->name }}</h1>
@stop

@section('content')
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
