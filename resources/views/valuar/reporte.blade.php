@extends('layouts.panel')

@section('title', 'Reporte de valuación #' . $valuacion->id)

@section('content_header')
    <h1><i class="fas fa-file-pdf me-2"></i>Reporte de valuación</h1>
@stop

@section('css')
<style>
  .reporte { max-width: 760px; margin: 0 auto; background: #fff; border: 1px solid var(--card-border); border-radius: var(--radius); padding: 28px; }
  .reporte h1 { font-size: 20px; }
  .reporte .val-grande { font-size: 34px; font-weight: 800; color: #4338ca; }
  .reporte table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .reporte th, .reporte td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
  .reporte .disclaimer { font-size: 11.5px; color: #6b7280; border-top: 2px solid #e5e7eb; margin-top: 18px; padding-top: 10px; }
  @media print {
    .panel-sidebar, .panel-topbar, .no-print { display: none !important; }
    .panel-main { margin: 0 !important; padding: 0 !important; }
    .panel-content { padding: 0 !important; }
    body { background: #fff !important; }
    .reporte { border: none; box-shadow: none; }
  }
</style>
@stop

@section('content')
<div class="no-print mb-3 d-flex gap-2">
    <button class="btn btn-primary rounded-pill px-4" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimir / Descargar PDF</button>
    <a href="{{ route('valuar') }}" class="btn btn-outline-primary rounded-pill px-4"><i class="fas fa-arrow-left me-1"></i> Volver</a>
</div>

<div class="reporte">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="fw-bold mb-1"><i class="fas fa-chart-line me-2" style="color:#4f46e5;"></i>Reporte de valuación de mercado</h1>
            <div class="text-muted small">#{{ $valuacion->id }} · {{ $valuacion->created_at ? $valuacion->created_at->format('d/m/Y H:i') : '' }} · {{ config('app.name') }}</div>
        </div>
        <img src="{{ asset('images/logoinmo.svg') }}" alt="" width="44" height="44">
    </div>

    <h2 class="h5 fw-bold mt-4">Inmueble valuado</h2>
    <table>
        <tr><th>Ubicación</th><td>{{ $valuacion->direccion ?: '-' }} {{ $valuacion->ciudad ? '· ' . $valuacion->ciudad : '' }}</td></tr>
        <tr><th>Operación</th><td>{{ $valuacion->tipo_operacion === 'alquiler' ? 'Alquiler' : 'Venta' }}</td></tr>
        <tr><th>Tipo</th><td>{{ $valuacion->tipo_propiedad ?: '-' }}</td></tr>
        <tr><th>Ambientes</th><td>{{ $valuacion->ambientes ?? '-' }}</td></tr>
        <tr><th>Superficie cubierta</th><td>{{ $valuacion->sup_cubierta }} m²</td></tr>
    </table>

    <h2 class="h5 fw-bold mt-4">Valor estimado</h2>
    <div class="text-center my-3">
        <div class="text-muted small">VALOR ESTIMADO DE MERCADO (mediana de comparables)</div>
        <div class="val-grande">@dinero((float) $valuacion->valor_medio, 0)</div>
        <div class="text-muted">Rango (percentil 25–75): <strong>@dinero((float) $valuacion->valor_min, 0)</strong> — <strong>@dinero((float) $valuacion->valor_max, 0)</strong></div>
    </div>
    <table>
        <tr><th>Precio/m² mediana de la zona</th><td>@dinero((float) $valuacion->pm2_mediana, 0)/m²</td></tr>
        <tr><th>Comparables usados</th><td>{{ count($valuacion->comparables ?? []) }}</td></tr>
        @if($zona['avisos'])
        <tr><th>Índice de la zona ({{ $valuacion->ciudad ?: 'zona' }})</th><td>{{ $zona['avisos'] }} avisos publicados · mediana @dinero((float) $zona['pm2_mediana'], 0)/m²
            @if($zona['tendencia_pct'] !== null) · tendencia 90 días: {{ $zona['tendencia_pct'] > 0 ? '+' : '' }}{{ $zona['tendencia_pct'] }}%@endif</td></tr>
        @endif
    </table>

    @if(!empty($valuacion->comparables))
    <h2 class="h5 fw-bold mt-4">Comparables ({{ count($valuacion->comparables) }})</h2>
    <table>
        <tr><th>Propiedad</th><th>Tipo</th><th>m²</th><th>Precio</th><th>$/m²</th><th>Km</th></tr>
        @foreach($valuacion->comparables as $c)
        <tr>
            <td>{{ $c['titulo'] ?? ('#' . ($c['id'] ?? '')) }}</td>
            <td>{{ $c['tipo_propiedad'] ?? '-' }}</td>
            <td>{{ $c['sup'] ?? '-' }}</td>
            <td>@dinero((float) ($c['precio'] ?? 0), 0)</td>
            <td>@dinero((float) ($c['pm2'] ?? 0), 0)</td>
            <td>{{ $c['distancia_km'] ?? '-' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <div class="disclaimer">
        <strong>Aviso legal:</strong> este reporte es una <strong>estimación orientativa</strong> calculada automáticamente a partir de avisos publicados en {{ config('app.name') }} (precio por m² de propiedades comparables cercanas, mediana y percentiles 25–75). No contempla estado de conservación, refacciones, orientación ni vicios de la propiedad, y <strong>no constituye una tasación profesional</strong> ni un informe pericial. {{ config('app.name') }} no interviene en las operaciones entre partes.
    </div>
</div>
@stop
