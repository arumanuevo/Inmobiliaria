@extends('layouts.panel')

@section('title', 'Resultado de la valuación')

@section('content_header')
    <h1><i class="fas fa-chart-line me-2" style="color:var(--accent)"></i>Resultado de la valuación</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  #mapa-comparables { height: 340px; border-radius: var(--radius); border: 1px solid var(--card-border); }
  .val-num { font-size: 26px; font-weight: 800; color: var(--accent-dark); }
  .comp-item { border: 1px solid var(--card-border); border-radius: 10px; padding: 8px 10px; margin-bottom: 8px; background: #fff; font-size: 13px; }
  .comp-item img { width: 64px; height: 48px; object-fit: cover; border-radius: 6px; }
  .rango-bar { height: 10px; border-radius: 999px; background: linear-gradient(90deg, #a5b4fc, #4f46e5, #a5b4fc); }
</style>
@stop

@section('content')
@php
    $compJson = json_encode($res['comparables'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $lat0 = $datos['lat'] ?? null;
    $lng0 = $datos['lng'] ?? null;
    $puntoJson = json_encode(['lat' => $lat0 !== null ? (float) $lat0 : null, 'lng' => $lng0 !== null ? (float) $lng0 : null], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp

@if(!$res['ok'])
<div class="card card-warning mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Sin estimación</h5>
        <p class="mb-2">{{ $res['mensaje'] }}</p>
        <a href="{{ route('valuar') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-arrow-left me-1"></i> Probar otra ubicación</a>
    </div>
</div>
@else

@if($inmueble && $desfase !== null)
<div class="alert {{ $desfase >= -10 && $desfase <= 10 ? 'alert-success' : 'alert-warning' }}">
    <i class="fas fa-tag me-1"></i>Tu propiedad está publicada a <strong>@dinero((float) $inmueble->precio, 2)</strong> y el mercado de la zona estima <strong>@dinero((float) $res['valor_medio'], 0)</strong>:
    tu precio está <strong>{{ $desfase > 0 ? '+' : '' }}{{ $desfase }}%</strong> {{ $desfase > 10 ? 'POR ENCIMA del mercado' : ($desfase < -10 ? 'POR DEBAJO del mercado' : 'alineado con el mercado') }}.
</div>
@endif

<div class="card card-primary mb-4">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Valor estimado de mercado</h3></div>
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-5 text-center">
                <small class="text-muted d-block">Valor estimado (mediana de comparables)</small>
                <span class="val-num">@dinero((float) $res['valor_medio'], 0)</span>
                <div class="rango-bar mt-2"></div>
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>Mín @dinero((float) $res['valor_min'], 0)</span>
                    <span>Máx @dinero((float) $res['valor_max'], 0)</span>
                </div>
            </div>
            <div class="col-md-7">
                <ul class="list-unstyled small mb-0">
                    <li><i class="fas fa-ruler-combined me-1 text-muted"></i> Superficie considerada: <strong>{{ $res['sup'] }} m²</strong></li>
                    <li><i class="fas fa-divide me-1 text-muted"></i> Mediana de precio/m² en la zona: <strong>@dinero((float) $res['pm2_mediana'], 0)</strong>/m²</li>
                    <li><i class="fas fa-home me-1 text-muted"></i> Comparables usados: <strong>{{ $res['n'] }}</strong> propiedades publicadas (radio de {{ \App\Support\Valuador::RADIO_KM }} km)</li>
                    <li><i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $datos['direccion'] ?? '' }} {{ $datos['ciudad'] ? '· ' . $datos['ciudad'] : '' }} — {{ $datos['tipo_operacion'] === 'alquiler' ? 'alquiler' : 'venta' }}</li>
                    @if($esPremium && $zona['avisos'])
                    <li><i class="fas fa-chart-area me-1 text-muted"></i> Índice de la zona: <strong>{{ $zona['avisos'] }}</strong> avisos, mediana <strong>@dinero((float) $zona['pm2_mediana'], 0)/m²</strong>
                        @if($zona['tendencia_pct'] !== null)
                            — tendencia últimos 90 días: <strong class="{{ $zona['tendencia_pct'] >= 0 ? 'text-danger' : 'text-success' }}">{{ $zona['tendencia_pct'] > 0 ? '+' : '' }}{{ $zona['tendencia_pct'] }}%</strong>
                        @endif
                    </li>
                    @endif
                </ul>
                @if($esPremium)
                <div class="d-flex gap-2 flex-wrap mt-2">
                    @if($valuacion)
                    <a href="{{ route('valuar.reporte', $valuacion) }}" class="btn btn-primary btn-sm rounded-pill px-3" target="_blank"><i class="fas fa-file-pdf me-1"></i> Ver reporte (PDF)</a>
                    @endif
                    <a href="{{ route('valuar') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-redo me-1"></i> Nueva valuación</a>
                </div>
                @else
                <div class="alert alert-info py-2 mt-2 mb-0 small">
                    <i class="fas fa-crown me-1 text-warning"></i>Estás en un plan de pago: tenés la valuación básica.
                    Pasate a <strong>Premium</strong> para el reporte PDF, elegir comparables y el índice histórico de la zona.
                    <a href="{{ route('sumate') }}" class="fw-semibold ms-1">Ver planes</a>
                </div>
                @endif
            </div>
        </div>
        <div class="alert alert-light border small text-muted mt-3 mb-0">
            <i class="fas fa-info-circle me-1"></i>Estimación <strong>orientativa</strong> calculada con avisos publicados en este portal (precio por m² de comparables cercanos). No contempla estado de conservación, refacciones ni vicios; <strong>no es una tasación profesional</strong>.
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-map me-2"></i>Comparables en el mapa ({{ $res['n'] }})</h3></div>
            <div class="card-body">
                <div id="mapa-comparables"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="fas fa-list me-2"></i>Comparables</h3>
                @if($esPremium)
                <span class="badge badge-secondary">Premium: tildá y recalculá</span>
                @endif
            </div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                <form action="{{ route('valuar.estimar') }}" method="POST" id="form-comparables">
                    @csrf
                    @foreach(['direccion' => $datos['direccion'] ?? null, 'lat' => $datos['lat'] ?? null, 'lng' => $datos['lng'] ?? null, 'ciudad' => $datos['ciudad'] ?? null, 'tipo_operacion' => $datos['tipo_operacion'], 'tipo_propiedad' => $datos['tipo_propiedad'] ?? null, 'ambientes' => $datos['ambientes'] ?? null, 'sup_cubierta' => $datos['sup_cubierta'], 'inmueble_id' => $datos['inmueble_id'] ?? null] as $k => $v)
                        @if($v !== null && $v !== '')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                    @endforeach
                    @foreach($res['comparables'] as $c)
                    <div class="comp-item d-flex gap-2 align-items-center">
                        @if($esPremium)
                        <input type="checkbox" class="form-check-input mt-0" name="comparables_ids[]" value="{{ $c['id'] }}" checked title="Incluir en el cálculo">
                        @endif
                        @if($c['foto'])<img src="{{ $c['foto'] }}" alt="">@else<span style="width:64px;height:48px;background:#eef0f6;border-radius:6px;display:inline-block;"></span>@endif
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $c['titulo'] }}</div>
                            <div class="text-muted small">{{ $c['tipo_propiedad'] ?? '' }} · {{ $c['ambientes'] ?? '?' }} amb · {{ $c['sup'] }} m² · {{ $c['ciudad'] ?? '' }} @if($c['distancia_km'] !== null) · {{ $c['distancia_km'] }} km @endif</div>
                            <div class="small"><strong>@dinero((float) $c['precio'], 0)</strong> <span class="text-muted">(@dinero((float) $c['pm2'], 0)/m²)</span>
                                @if($c['url'])<a href="{{ $c['url'] }}" target="_blank" class="ms-1">ver</a>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @if($esPremium)
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 w-100 mt-1"><i class="fas fa-calculator me-1"></i> Recalcular con los tildados</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var comps = {!! $compJson !!};
    var punto = {!! $puntoJson !!};
    var map = L.map('mapa-comparables');
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var layer = L.featureGroup().addTo(map);
    comps.forEach(function (c) {
        if (c.lat === null || c.lng === null || c.lat === undefined) { return; }
        var m = L.marker([c.lat, c.lng]).addTo(layer);
        m.bindPopup('<div style="min-width:180px;"><strong>' + (c.titulo || '') + '</strong><br>' +
            (c.precio ? '$' + Number(c.precio).toLocaleString('es-AR') : '') + ' · ' + (c.pm2 ? '$' + Number(c.pm2).toLocaleString('es-AR') + '/m²' : '') +
            '<br><small class="text-muted">' + (c.ciudad || '') + (c.distancia_km ? ' · ' + c.distancia_km + ' km' : '') + '</small>' +
            (c.url ? '<br><a href="' + c.url + '" target="_blank" style="color:#4f46e5;">Ver propiedad</a>' : '') + '</div>');
    });
    if (punto.lat && punto.lng) {
        L.marker([punto.lat, punto.lng], { icon: L.divIcon({ className: '', html: '<div style="width:16px;height:16px;border-radius:50%;background:#d946ef;border:3px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.45);"></div>', iconSize: [16, 16], iconAnchor: [8, 8] }) }).addTo(layer).bindPopup('Ubicación valuada');
    }
    if (layer.getLayers().length > 0) { map.fitBounds(layer.getBounds().pad(0.25)); } else { map.setView([-34.6, -59.4], 11); }
})();
</script>
@stop
