@extends('layouts.panel')

@section('title', 'Valuar propiedad')

@section('content_header')
    <h1><i class="fas fa-chart-line me-2" style="color:var(--accent)"></i>Valuar propiedad</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  #mapa-valuar { height: 340px; border-radius: var(--radius); border: 1px solid var(--card-border); }
  .val-hero { background: linear-gradient(135deg, #101828 0%, #1d2939 55%, #312e81 100%); color: #fff; border-radius: var(--radius); padding: 18px 20px; margin-bottom: 16px; }
  .val-hero p { color: #c7d0e0; margin: 0; font-size: 13.5px; }
  .val-hero h1 { color: #fff; font-size: 20px; margin: 0 0 4px; }
</style>
@stop

@section('content')
<div class="val-hero">
    <h1><i class="fas fa-chart-line me-2"></i>¿Cuánto vale esta propiedad?</h1>
    <p>Estimación de valor de mercado basada en los avisos publicados en este portal (comparables cercanos por precio/m²). <strong>Es orientativa: no reemplaza una tasación profesional.</strong>
    @if($esPremium)
        <span class="badge bg-success ms-1">Premium: podés elegir comparables y bajar el reporte</span>
    @else
        <span class="badge bg-warning text-dark ms-1">Plan pago: valuación básica — pasate a Premium para el reporte detallado</span>
    @endif
    </p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('valuar.estimar') }}" method="POST" id="form-valuar">
            @csrf
            @if($prefill)<input type="hidden" name="inmueble_id" value="{{ $prefill['inmueble_id'] }}">@endif
            <div class="row g-3">
                <div class="col-lg-7">
                    <label class="form-label fw-semibold">Dirección <small class="text-muted fw-normal">(se ubica en el mapa para buscar comparables cercanos)</small></label>
                    <div class="input-group">
                        <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Ej: Av. San Martín 450, Chivilcoy" autocomplete="off" value="{{ $prefill['direccion'] ?? '' }}">
                        <button type="button" class="btn btn-outline-primary" id="btn-geocodificar"><i class="fas fa-map-marker-alt"></i> Ubicar en el mapa</button>
                    </div>
                    <input type="hidden" name="lat" id="lat" value="{{ $prefill['lat'] ?? '' }}">
                    <input type="hidden" name="lng" id="lng" value="{{ $prefill['lng'] ?? '' }}">
                    <div class="form-text">Si no la encontrás, mové el pin en el mapa. Sin coordenadas se busca por ciudad.</div>
                    <div id="mapa-valuar" class="mt-2"></div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{ $prefill['ciudad'] ?? '' }}" placeholder="Ej: Chivilcoy">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Operación</label>
                            <select name="tipo_operacion" class="form-select">
                                <option value="venta" {{ ($prefill['tipo_operacion'] ?? 'venta') === 'venta' ? 'selected' : '' }}>Venta</option>
                                <option value="alquiler" {{ ($prefill['tipo_operacion'] ?? '') === 'alquiler' ? 'selected' : '' }}>Alquiler</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipo</label>
                            <select name="tipo_propiedad" class="form-select">
                                <option value="">Cualquiera</option>
                                @foreach($tipos as $val => $lbl)
                                    <option value="{{ $val }}" {{ ($prefill['tipo_propiedad'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ambientes</label>
                            <input type="number" name="ambientes" class="form-control" min="0" max="30" value="{{ $prefill['ambientes'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Superficie cubierta (m²) <span class="text-danger">*</span></label>
                            <input type="number" name="sup_cubierta" class="form-control" min="5" step="0.01" required value="{{ $prefill['sup_cubierta'] ?? '' }}" placeholder="Ej: 65">
                        </div>
                        @if($prefill && !empty($prefill['precio']))
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0 small"><i class="fas fa-tag me-1"></i>Precio publicado de esta propiedad: <strong>@dinero((float) $prefill['precio'], 2)</strong> — la valuación te muestra si está alineado con el mercado.</div>
                        </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill fw-semibold"><i class="fas fa-calculator me-1"></i> Estimar valor de mercado</button>
                    <div class="form-text mt-2"><i class="fas fa-lock me-1"></i>Disponible en planes de pago. El cálculo usa avisos publicados en el portal; no es una tasación profesional.</div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var map = L.map('mapa-valuar').setView([-34.6, -59.4], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var marker = null;

    function setMarker(lat, lng) {
        if (marker) { marker.setLatLng([lat, lng]); } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                var p = marker.getLatLng();
                document.getElementById('lat').value = p.lat.toFixed(6);
                document.getElementById('lng').value = p.lng.toFixed(6);
            });
        }
        map.setView([lat, lng], 15);
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
    }

    document.getElementById('btn-geocodificar').addEventListener('click', function () {
        var q = document.getElementById('direccion').value.trim();
        if (!q) { return; }
        this.disabled = true;
        fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=ar&limit=1&q=' + encodeURIComponent(q), { headers: { 'Accept-Language': 'es' } })
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                if (rows && rows.length) {
                    var lat = parseFloat(rows[0].lat), lng = parseFloat(rows[0].lon);
                    setMarker(lat, lng);
                    if (!document.getElementById('ciudad').value && rows[0].display_name) {
                        var parts = rows[0].display_name.split(',');
                        document.getElementById('ciudad').value = (parts[1] || parts[0] || '').trim();
                    }
                } else {
                    alert('No se encontró esa dirección. Probá con más detalle (calle y número, ciudad) o ubicá el pin a mano.');
                }
            })
            .catch(function () { alert('No se pudo geocodificar ahora. Ubicá el pin a mano en el mapa.'); })
            .finally(function () { document.getElementById('btn-geocodificar').disabled = false; });
    });

    // Si ya hay coordenadas (prefill), mostrar el pin
    var lat0 = parseFloat(document.getElementById('lat').value);
    var lng0 = parseFloat(document.getElementById('lng').value);
    if (!isNaN(lat0) && !isNaN(lng0)) { setMarker(lat0, lng0); }

    // Validación mínima antes de enviar
    document.getElementById('form-valuar').addEventListener('submit', function (e) {
        var lat = document.getElementById('lat').value, lng = document.getElementById('lng').value;
        var ciudad = document.getElementById('ciudad').value.trim();
        if ((!lat || !lng) && !ciudad) {
            e.preventDefault();
            alert('Indicá una dirección (botón "Ubicar en el mapa") o al menos la ciudad.');
        }
    });
})();
</script>
@stop
