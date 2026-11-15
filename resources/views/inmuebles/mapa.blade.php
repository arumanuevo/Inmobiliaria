@extends('layouts.landing')

@section('title', 'Mapa de Propiedades')

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  body { padding-top: 56px; }
  #mapa-busqueda { height: 68vh; border-radius: 12px; border: 1px solid #ccc; }
  .lista-mapa { max-height: 68vh; overflow-y: auto; }
  .item-mapa { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #fff; }
  .item-mapa img { width: 100%; height: 110px; object-fit: cover; border-radius: 8px; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo {{ config('app.name') }}" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('propiedades.mapa') }}">Mapa</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4 mb-5 px-3">
  <h1 class="h4 mb-3">Buscar en el mapa</h1>

  <form id="filtros-mapa" class="row g-2 mb-3">
    <div class="col-md-2">
      <select name="tipo_operacion" class="form-control form-control-sm">
        <option value="">Venta y Alquiler</option>
        <option value="venta">Venta</option>
        <option value="alquiler">Alquiler</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="tipo_propiedad" class="form-control form-control-sm">
        <option value="">Todos los tipos</option>
        @foreach($tipos as $val => $lbl)
          <option value="{{ $val }}">{{ $lbl }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <select name="moneda" class="form-control form-control-sm">
        <option value="">ARS y USD</option>
        <option value="ARS">Pesos (ARS)</option>
        <option value="USD">Dólares (USD)</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="number" name="precio_min" class="form-control form-control-sm" placeholder="Precio mín.">
    </div>
    <div class="col-md-2">
      <input type="number" name="precio_max" class="form-control form-control-sm" placeholder="Precio máx.">
    </div>
    <div class="col-md-2">
      <input type="text" name="ciudad" class="form-control form-control-sm" placeholder="Ciudad">
    </div>
    <div class="col-12">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar por título o descripción...">
    </div>
  </form>

  <div class="card mb-3">
    <div class="card-body py-2">
      <form id="alert-form-mapa" method="POST" action="{{ route('busquedas.guardar') }}" class="row g-2 align-items-center">
        @csrf
        <input type="hidden" name="filtros_json" id="filtros_json_mapa" value="">
        <div class="col-md-6">
          <span class="small text-muted"><i class="fas fa-bell text-primary"></i> Avisame por email cuando aparezca una propiedad con estos filtros</span>
        </div>
        <div class="col-md-3">
          <input type="email" name="email" class="form-control form-control-sm" placeholder="Tu email" required value="{{ auth()->user()->email ?? '' }}">
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-bell"></i> Guardar búsqueda</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div id="mapa-busqueda"></div>
      <small class="text-muted">Arrastrá o hacé zoom: se muestran las propiedades dentro del área visible del mapa.</small>
    </div>
    <div class="col-lg-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong id="contador-mapa">0 resultados</strong>
      </div>
      <div class="lista-mapa" id="lista-mapa"></div>
    </div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
  </div>
</footer>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof L === 'undefined') { return; }
    var map = L.map('mapa-busqueda').setView([-38.5, -64.5], 5);
    var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });
    L.control.layers({ 'Callejera (OSM)': osm, 'Satelital (Esri)': satelite }, null, { position: 'topright' }).addTo(map);

    var capa = L.layerGroup().addTo(map);

    function cargar() {
        var b = map.getBounds();
        var params = new URLSearchParams({
            min_lat: b.getSouth(), max_lat: b.getNorth(),
            min_lng: b.getWest(), max_lng: b.getEast()
        });
        var form = document.getElementById('filtros-mapa');
        new FormData(form).forEach(function (v, k) { if (v !== '') { params.set(k, v); } });

        fetch('/api/propiedades?' + params.toString())
            .then(function (r) { return r.json(); })
            .then(function (items) {
                capa.clearLayers();
                var lista = document.getElementById('lista-mapa');
                lista.innerHTML = '';
                document.getElementById('contador-mapa').textContent = items.length + ' resultado(s) en el área visible';
                items.forEach(function (it) {
                    if (it.lat === null || it.lng === null) { return; }
                    var foto = it.foto ? '<img src="' + it.foto + '" alt="">' : '';
                    var popup = '<div style="min-width:220px;">' + foto +
                        '<strong>' + it.titulo + '</strong><br>' +
                        it.precio_fmt + ' · ' + (it.tipo_operacion || '') + (it.tipo_propiedad ? ' · ' + it.tipo_propiedad : '') + '<br>' +
                        (it.ciudad || '') + ' · ' + (it.inmobiliaria || '') + '<br>' +
                        (it.url ? '<a href="' + it.url + '" target="_blank">Ver en la inmobiliaria</a>' : '') +
                        '</div>';
                    L.marker([it.lat, it.lng]).addTo(capa).bindPopup(popup);

                    var div = document.createElement('div');
                    div.className = 'item-mapa';
                    div.innerHTML = foto +
                        '<div class="fw-bold small mt-1">' + it.titulo + '</div>' +
                        '<div class="text-muted small">' + it.precio_fmt + ' · ' + (it.tipo_operacion || '') + ' · ' + (it.ciudad || '') + '</div>' +
                        '<div class="small">' + (it.inmobiliaria || '') + '</div>' +
                        (it.url ? '<a href="' + it.url + '" target="_blank" class="btn btn-primary btn-sm mt-1">Ver</a>' : '');
                    lista.appendChild(div);
                });
            })
            .catch(function () {});
    }

    map.on('moveend', cargar);
    document.getElementById('filtros-mapa').addEventListener('input', function () {
        clearTimeout(window.__tMapa);
        window.__tMapa = setTimeout(cargar, 400);
    });
    cargar();
});

document.getElementById('alert-form-mapa').addEventListener('submit', function () {
    var filtros = {};
    new FormData(document.getElementById('filtros-mapa')).forEach(function (v, k) { if (v !== '') { filtros[k] = v; } });
    document.getElementById('filtros_json_mapa').value = JSON.stringify(filtros);
});
</script>
@stop
