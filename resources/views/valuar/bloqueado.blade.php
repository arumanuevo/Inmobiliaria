@extends('layouts.panel')

@section('title', 'Valuar propiedad')

@section('content_header')
    <h1><i class="fas fa-chart-line me-2" style="color:var(--accent)"></i>Valuar propiedad</h1>
@stop

@section('content')
<div class="card card-warning">
    <div class="card-body text-center py-5">
        <i class="fas fa-lock fa-3x mb-3 text-warning"></i>
        <h3 class="fw-bold">La valuación de mercado es para planes de pago</h3>
        <p class="text-muted mx-auto" style="max-width: 560px;">
            Con un plan de pago (Inicial, Básico, Profesional) obtenés la <strong>valuación básica</strong>:
            rango estimado de valor según los comparables publicados en el portal.
            Con <strong>Premium</strong> además tenés el <strong>reporte detallado</strong> (PDF imprimible,
            comparables elegibles que ajustan el cálculo, índice de mercado de la zona) y el botón
            <strong>"Valor de mercado"</strong> en cada uno de tus inmuebles.
        </p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('sumate') }}" class="btn btn-primary rounded-pill px-4"><i class="fas fa-layer-group me-1"></i> Ver planes</a>
            <a href="{{ route('boletos.mios') }}" class="btn btn-outline-primary rounded-pill px-4"><i class="fas fa-receipt me-1"></i> Mis boletos</a>
        </div>
        <p class="text-muted small mt-3 mb-0">La valuación es una <em>estimación orientativa</em> basada en avisos publicados; no constituye una tasación profesional.</p>
    </div>
</div>
@stop
