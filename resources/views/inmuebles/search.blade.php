@extends('layouts.landing')

@section('title', 'Buscar Propiedades')

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  body { padding-top: 56px; background: linear-gradient(180deg, #f6f7fb 0%, #eceff7 100%); min-height: 100vh; }
  .search-hero { background: linear-gradient(135deg, #101828 0%, #1d2939 55%, #312e81 100%); color: #fff; padding: 44px 0 100px; }
  .search-hero h1 { font-weight: 800; }
  .search-hero .lead { color: #c7d0e0; }
  .search-card { border: none; border-radius: 20px; box-shadow: 0 24px 60px rgba(16,24,40,.14); }
  .search-card .card-body { padding: 24px; }
  .search-card .form-label { font-weight: 600; font-size: .85rem; color: #344054; margin-bottom: .35rem; }
  .search-card .form-control, .search-card .form-select {
    border-radius: 12px; padding: .6rem .95rem; border: 1.5px solid #e2e8f0; background: #f8fafc; font-size: .92rem;
  }
  .search-card .form-control:focus, .search-card .form-select:focus {
    border-color: #8b5cf6; background: #fff; box-shadow: 0 0 0 4px rgba(139,92,246,.15);
  }
  .geo-card { background: #eef2ff; border: 1px solid #c7d2fe !important; border-radius: 16px; }
  .geo-card .input-group-text { background: #fff; border: 1.5px solid #c7d2fe; border-right: none; border-radius: 12px 0 0 12px; color: #6366f1; }
  .geo-card .form-control { border-left: none; border-radius: 0 12px 12px 0; background: #fff; }
  .geo-card .form-select, .geo-card .btn { border-radius: 12px; }
  .btn-chip-ciudad { border-radius: 999px !important; }
  .btn-buscar {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    border: none; color: #fff; font-weight: 700; border-radius: 999px; padding: .65rem 2.2rem;
    box-shadow: 0 10px 24px rgba(124,92,246,.4); transition: filter .18s ease, transform .18s ease;
  }
  .btn-buscar:hover { filter: brightness(1.12); transform: translateY(-1px); color: #fff; }
  .btn-limpiar { border-radius: 999px; font-weight: 600; }
  .button-container { display: flex; justify-content: center; gap: .6rem; margin-top: 20px; }
  .alert-card { border: 1px solid #c7d2fe !important; border-radius: 16px; background: #eef2ff; }
  .alert-card .btn { border-radius: 999px; font-weight: 600; border-color: #6366f1; color: #4f46e5; }
  .alert-card .btn:hover { background: #6366f1; border-color: #6366f1; color: #fff; }
  .view-toggle .btn { font-weight: 600; }
  .view-toggle .btn:first-child { border-radius: 999px 0 0 999px; }
  .view-toggle .btn:last-child { border-radius: 0 999px 999px 0; }
  .view-toggle .btn.active { background: linear-gradient(135deg, #6366f1, #8b5cf6); border-color: #6366f1; color: #fff; }
  .property-card { margin-bottom: 20px; }
  .property-card .card { border-radius: 18px; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
  .property-card .card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(16,24,40,.16) !important; }
  .property-card img { height: 210px; object-fit: cover; }
  @media (min-width: 992px) {
    .mapa-sticky-wrapper { position: sticky; top: 72px; height: calc(100vh - 90px); min-height: 520px; }
    #mapa-search { height: 100% !important; min-height: 520px; }
  }
  @media (max-width: 991.98px) {
    .mapa-sticky-wrapper { height: 420px; margin-bottom: 20px; }
    #mapa-search { height: 420px !important; }
  }
  .pagination { justify-content: center; }
  .pagination .page-link { border-radius: 10px; margin: 0 2px; border-color: #e2e8f0; color: #4f46e5; }
  .pagination .page-item.active .page-link { background: linear-gradient(135deg, #6366f1, #8b5cf6); border-color: transparent; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; margin-top: 40px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<!-- Navbar fijo -->
<nav class="navbar navbar-expand-lg navbar-dark portal-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo {{ config('app.name') }}" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link text-success fw-bold" href="{{ route('register', ['tipo' => 'particular']) }}"><i class="fas fa-plus-circle me-1"></i> Publicar gratis</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="search-hero">
  <div class="container">
    <h1 class="h2 mb-2 text-white">Buscar propiedades</h1>
    <p class="lead mb-0">Filtrá por zona, precio, ambientes y más. Hay de inmobiliarias verificadas y dueños directos.</p>
  </div>
</div>

<div class="container pb-5" style="margin-top: -68px; position: relative; z-index: 2;">
  <div class="card search-card mb-4">
    <div class="card-body">
    <form id="search-form">
      <input type="hidden" name="geo_lat" id="geo_lat" value="">
      <input type="hidden" name="geo_lng" id="geo_lng" value="">

      <!-- Búsqueda Geográfica GIS con Geocoder y radio -->
      <div class="card geo-card mb-3">
        <div class="card-body py-3">
          <label class="form-label fw-bold text-primary mb-1">
            <i class="fas fa-map-marked-alt me-1"></i> Búsqueda Geográfica (Ciudad, pueblo, barrio o dirección)
          </label>
          <div class="row g-2 align-items-center">
            <div class="col-md-7 position-relative">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search-location text-primary"></i></span>
                <input type="text" id="geo_input" class="form-control" placeholder="Escribí una ciudad, pueblo, barrio o dirección (ej: Chivilcoy, Alberti, Suipacha, Palermo)..." autocomplete="off">
                <button type="button" id="btn-limpiar-geo" class="btn btn-outline-secondary" style="display:none;" title="Quitar búsqueda geográfica"><i class="fas fa-times"></i></button>
              </div>
              <div id="geo_sugerencias" class="list-group position-absolute w-100 shadow" style="z-index: 1050; max-height: 260px; overflow-y: auto; display: none;"></div>
            </div>
            <div class="col-md-3">
              <select name="radio_km" id="radio_km" class="form-select">
                <option value="2">Radio: 2 km</option>
                <option value="5">Radio: 5 km</option>
                <option value="10" selected>Radio: 10 km (Recomendado)</option>
                <option value="20">Radio: 20 km</option>
                <option value="35">Radio: 35 km</option>
                <option value="50">Radio: 50 km</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="button" id="btn-gps" class="btn btn-outline-primary w-100" title="Buscar cerca de mi ubicación actual">
                <i class="fas fa-crosshairs me-1"></i> Cerca de mí
              </button>
            </div>
          </div>

          <div id="geo_seleccionado_info" class="mt-2 small text-success fw-bold" style="display:none;">
            <i class="fas fa-check-circle me-1"></i> Filtrando propiedades a <span id="geo_radio_lbl">10</span> km de: <span id="geo_lugar_lbl" class="text-dark"></span>
            <a href="#" id="link-quitar-geo" class="text-danger ms-2 text-decoration-none"><i class="fas fa-times-circle"></i> Quitar filtro geográfico</a>
          </div>

          @if(isset($ciudades) && $ciudades->count())
            <div class="mt-2 pt-2 border-top">
              <small class="text-muted d-block mb-1"><i class="fas fa-city me-1"></i> Ciudades y pueblos con propiedades:</small>
              <div class="d-flex flex-wrap gap-1">
                @foreach($ciudades as $c)
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-chip-ciudad" data-ciudad="{{ $c }}" style="font-size: 11px;">
                    {{ $c }}
                  </button>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="tipo_operacion">Tipo de Operación</label>
            <select class="form-select" id="tipo_operacion" name="tipo_operacion">
              <option value="">Todos</option>
              <option value="venta">Venta</option>
              <option value="alquiler">Alquiler</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="tipo_anunciante">Tipo de Anunciante</label>
            <select class="form-select" id="tipo_anunciante" name="tipo_anunciante">
              <option value="">Todos (Inmobiliarias y Dueños)</option>
              <option value="inmobiliaria">Solo Inmobiliarias</option>
              <option value="particular">Solo Dueño Directo (Particular)</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="tipo_propiedad">Tipo de propiedad</label>
            <select class="form-select" id="tipo_propiedad" name="tipo_propiedad">
              <option value="">Todos los tipos</option>
              @foreach(\App\Models\Inmueble::gruposTipos() as $grupo => $opciones)
                <optgroup label="{{ $grupo }}">
                  @foreach($opciones as $val => $lbl)
                    <option value="{{ $val }}" {{ request('tipo_propiedad') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
            <div class="form-check mt-1">
              <input class="form-check-input" type="checkbox" name="uso_comercial" id="uso_comercial" value="1" {{ request('uso_comercial') ? 'checked' : '' }}>
              <label class="form-check-label small" for="uso_comercial"><i class="fas fa-store me-1"></i>Solo uso comercial (local, oficina, galpón)</label>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="inmobiliaria">Anunciante específico</label>
            <select class="form-select" id="inmobiliaria" name="inmobiliaria">
              <option value="">Todos</option>
              @foreach($inmobiliarias as $inmobiliaria)
                <option value="{{ $inmobiliaria->slug }}">{{ $inmobiliaria->nombre_comercial }}{{ $inmobiliaria->usuario?->hasRole('particular') ? ' (Dueño directo)' : '' }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="moneda">Moneda</label>
            <select class="form-select" id="moneda" name="moneda">
              <option value="">ARS y USD</option>
              <option value="ARS">Pesos (ARS)</option>
              <option value="USD">Dólares (USD)</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="precio_min">Precio Mínimo</label>
            <input type="number" class="form-control" id="precio_min" name="precio_min" placeholder="Precio mínimo">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="precio_max">Precio Máximo</label>
            <input type="number" class="form-control" id="precio_max" name="precio_max" placeholder="Precio máximo">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="ciudad">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="habitaciones">Habitaciones</label>
            <select class="form-select" id="habitaciones" name="habitaciones">
              <option value="">Cualquiera</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5+</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="banos">Baños</label>
            <select class="form-select" id="banos" name="banos">
              <option value="">Cualquiera</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5+</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="ambientes_min">Ambientes mín.</label>
            <input type="number" class="form-control" id="ambientes_min" name="ambientes_min" min="0" placeholder="Ej: 3">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="cocheras_min">Cocheras mín.</label>
            <input type="number" class="form-control" id="cocheras_min" name="cocheras_min" min="0" placeholder="Ej: 1">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="superficie_min">Superficie Mínima (m²)</label>
            <input type="number" class="form-control" id="superficie_min" name="superficie_min" placeholder="Superficie mínima">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label" for="superficie_max">Superficie Máxima (m²)</label>
            <input type="number" class="form-control" id="superficie_max" name="superficie_max" placeholder="Superficie máxima">
          </div>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="btn btn-buscar" id="search-button"><i class="fas fa-search me-1"></i> Buscar</button>
        <button type="button" class="btn btn-outline-secondary btn-limpiar" id="btn-limpiar-filtros"><i class="fas fa-undo me-1"></i> Limpiar</button>
      </div>
      <hr>
    </form>
    </div>
  </div>

  <div class="card alert-card mb-4">
    <div class="card-body">
      <h5 class="card-title mb-1 fw-bold" style="color:#3730a3;"><i class="fas fa-bell me-1" style="color:#6366f1;"></i> Avisame cuando aparezca algo así</h5>
      <p class="text-muted small mb-3">Guardamos esta búsqueda con los filtros actuales y te enviamos un email cuando publiquen una propiedad que coincida.</p>
      <form id="alert-form" method="POST" action="{{ route('busquedas.guardar') }}">
        @csrf
        <input type="hidden" name="filtros_json" id="filtros_json" value="">
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre (opcional)" value="{{ auth()->user()->name ?? '' }}">
          </div>
          <div class="col-md-4">
            <input type="email" name="email" class="form-control" placeholder="Tu email" required value="{{ auth()->user()->email ?? '' }}">
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-bell"></i> Guardar búsqueda y avisarme</button>
          </div>
        </div>
        @if(config('services.recaptcha.site_key'))
          <div class="mt-2"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div></div>
        @endif
      </form>
    </div>
  </div>

  <!-- Barra de control y visualización de resultados (Split View / Solo Lista / Solo Mapa) -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 pt-2 border-top">
    <div>
      <h4 class="mb-0 d-inline me-2 fw-bold text-dark">Propiedades</h4>
      <span class="text-muted small" id="contador-resultados-texto">Cargando...</span>
    </div>
    <div class="btn-group view-toggle shadow-sm" role="group" aria-label="Modo de visualización">
      <button type="button" class="btn btn-outline-primary active btn-sm" id="btn-vista-split" title="Lista y Mapa lateral simultáneo">
        <i class="fas fa-columns me-1"></i> Lista + Mapa
      </button>
      <button type="button" class="btn btn-outline-primary btn-sm" id="btn-vista-lista" title="Ver solo listado completo">
        <i class="fas fa-th-large me-1"></i> Solo Lista
      </button>
      <button type="button" class="btn btn-outline-primary btn-sm" id="btn-vista-mapa" title="Ver solo mapa grande">
        <i class="fas fa-map-marked-alt me-1"></i> Solo Mapa
      </button>
    </div>
  </div>

  <!-- Layout Principal de Resultados (Split-View en Desktop) -->
  <div class="row g-3" id="contenedor-principal-resultados">
    <!-- Columna Izquierda: Listado de Propiedades -->
    <div class="col-lg-7 col-xl-7" id="columna-lista">
      <div class="results-container" id="property-results">
        <!-- Los resultados se cargarán aquí por AJAX -->
      </div>
    </div>

    <!-- Columna Derecha: Mapa Lateral Interactivo (Sticky) -->
    <div class="col-lg-5 col-xl-5" id="columna-mapa">
      <div class="mapa-sticky-wrapper">
        <div class="card shadow-sm border-0 h-100 overflow-hidden">
          <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center border-bottom">
            <span class="small fw-bold text-primary"><i class="fas fa-map-marker-alt me-1"></i> Ubicación en el mapa</span>
            <span class="badge bg-light text-dark border small" id="badge-mapa-count">Explorá la zona</span>
          </div>
          <div class="card-body p-0 position-relative h-100">
            <div id="mapa-search" style="border-radius: 0 0 8px 8px;"></div>
            <div class="position-absolute bottom-0 start-0 m-2 bg-white py-1 px-2 rounded shadow-sm small border" style="z-index: 1000; font-size: 11px;">
              <i class="fas fa-info-circle text-primary me-1"></i> Hacé clic en los pines para ver detalles
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
</div>

 <!-- Footer -->
 <footer class="footer">
    <div class="container">
      <p>© {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
      <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
    </div>
  </footer>
@stop

@section('js')
<!-- jQuery (necesario para el JS de esta página). Bootstrap ya viene del layout (5.3.0) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  $(document).ready(function() {
    var vistaActual = 'lista';
    var mapInstancia = null;
    var markersLayer = null;
    var markersById = {};

    function initMapa() {
      if (mapInstancia) return;
      mapInstancia = L.map('mapa-search').setView([-38.5, -64.5], 5);
      var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(mapInstancia);
      var satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        maxNativeZoom: 18,
        attribution: 'Tiles &copy; Esri'
      });
      L.control.layers({ 'Callejera (OSM)': osm, 'Satelital (Esri)': satelite }, null, { position: 'topright' }).addTo(mapInstancia);
      markersLayer = L.featureGroup().addTo(mapInstancia);
    }

    function cargarMapaProperties() {
      initMapa();
      setTimeout(function() {
        if (mapInstancia) { mapInstancia.invalidateSize(); }
      }, 200);

      var formData = $('#search-form').serialize();
      $.ajax({
        url: '/api/propiedades',
        method: 'GET',
        data: formData,
        success: function(items) {
          markersLayer.clearLayers();
          markersById = {};
          if (!items || !items.length) {
            $('#contador-resultados-texto').text('(0 propiedades en mapa)');
            return;
          }
          $('#contador-resultados-texto').text('(' + items.length + ' propiedades encontradas en mapa)');
          items.forEach(function(it) {
            if (it.lat === null || it.lng === null) return;
            var foto = it.foto ? '<img src="' + it.foto + '" style="width:100%; height:120px; object-fit:cover; border-radius:4px; margin-bottom:6px;">' : '';
            var badge = it.es_particular
              ? '<span class="badge bg-success mb-1" style="font-size:10px;">Dueño Directo</span>'
              : '<span class="badge bg-primary mb-1" style="font-size:10px;">Inmobiliaria</span>';
            var popup = '<div style="min-width:210px; max-width:260px;">' +
              foto +
              '<div>' + badge + '</div>' +
              '<div class="fw-bold mb-1">' + it.titulo + '</div>' +
              (it.tipo_lbl ? '<div class="small text-muted mb-1"><i class="fas fa-tag me-1"></i>' + it.tipo_lbl + '</div>' : '') +
              '<div class="text-primary fw-bold">' + it.precio_fmt + '</div>' +
              '<div class="small text-muted mb-1">' + (it.ciudad || '') + ' · ' + (it.inmobiliaria || '') + '</div>' +
              (it.url ? '<a href="' + it.url + '" class="btn btn-sm w-100 py-1" style="font-size:13px; font-weight:700; background:#4f46e5; border-color:#4f46e5; color:#fff !important;">Ver propiedad</a>' : '') +
              '</div>';
            var marker = L.marker([it.lat, it.lng]).bindPopup(popup);
            markersById[it.id] = marker;
            markersLayer.addLayer(marker);
          });

          if (markersLayer.getLayers().length > 0) {
            mapInstancia.fitBounds(markersLayer.getBounds().pad(0.15));
          }

          // Foco desde "En el mapa" (?prop=ID): zoom al marcador y abrir su popup
          var propFocus = (typeof urlParams !== 'undefined') ? urlParams.get('prop') : null;
          if (propFocus && markersById[propFocus]) {
            var mk = markersById[propFocus];
            mapInstancia.setView(mk.getLatLng(), 17);
            mk.openPopup();
          }
        }
      });
    }

    // Cierra modales abiertos y limpia backdrops huérfanos antes de reemplazar el HTML
    // (si el modal estaba abierto cuando se recargan los resultados, el backdrop puede
    // quedar pegado y bloquear toda la página)
    function cerrarModalesHuerfanos() {
      document.querySelectorAll('.modal.show').forEach(function(el) {
        if (window.bootstrap && bootstrap.Modal.getInstance(el)) {
          bootstrap.Modal.getInstance(el).hide();
        }
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
      });
      document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('padding-right');
      document.body.style.removeProperty('overflow');
    }

    function loadProperties(page = 1) {
      var formData = $('#search-form').serialize();
      $.ajax({
        url: '/buscar-propiedades?page=' + page,
        method: 'GET',
        data: formData,
        success: function(data) {
          cerrarModalesHuerfanos();
          $('#property-results').html(data);
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          $('#property-results').html('<div class="alert alert-warning m-3">No se pudieron cargar los resultados. Probá de nuevo (o tocá Buscar).</div>');
        }
      });
    }

    function aplicarVista(modo) {
      vistaActual = modo;
      $('#btn-vista-split, #btn-vista-lista, #btn-vista-mapa').removeClass('active');
      $('#btn-vista-' + modo).addClass('active');

      var $lista = $('#columna-lista');
      var $mapa = $('#columna-mapa');

      // limpiar SIEMPRE todas las clases de layout (idempotente, sin importar la vista anterior)
      var todas = 'col-12 col-lg-5 col-lg-7 col-xl-5 col-xl-7 d-none';
      $lista.removeClass(todas);
      $mapa.removeClass(todas);

      if (modo === 'split') {
        $lista.addClass('col-lg-7 col-xl-7').show();
        $mapa.addClass('col-lg-5 col-xl-5').show();
        loadProperties();
        cargarMapaProperties();
      } else if (modo === 'lista') {
        $lista.addClass('col-12').show();
        $mapa.addClass('d-none').hide();
        loadProperties();
      } else { // mapa
        $lista.addClass('d-none').hide();
        $mapa.addClass('col-12').show();
        cargarMapaProperties();
      }
      // por si el mapa quedó chico al cambiar de columna
      setTimeout(function () { if (mapInstancia) { mapInstancia.invalidateSize(); } }, 250);
    }

    $('#btn-vista-split').on('click', function() { aplicarVista('split'); });
    $('#btn-vista-lista').on('click', function() { aplicarVista('lista'); });
    $('#btn-vista-mapa').on('click', function() { aplicarVista('mapa'); });

    // Determinar vista inicial
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('vista') === 'mapa') {
      aplicarVista('mapa');
    } else if (window.innerWidth >= 992) {
      aplicarVista('split');
    } else {
      aplicarVista('lista');
    }

    $('#search-button').click(function() {
      if (vistaActual === 'split') {
        loadProperties();
        cargarMapaProperties();
      } else if (vistaActual === 'mapa') {
        cargarMapaProperties();
      } else {
        loadProperties();
      }
    });

    // Manejar la paginación
    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      loadProperties(page);
    });

    // Lógica Geográfica GIS: Geocoder con Nominatim
    var timerGeo = null;
    $('#geo_input').on('input', function() {
      clearTimeout(timerGeo);
      var q = $(this).val().trim();
      if (q.length < 3) {
        $('#geo_sugerencias').hide().empty();
        return;
      }
      timerGeo = setTimeout(function() {
        $.ajax({
          url: 'https://nominatim.openstreetmap.org/search',
          method: 'GET',
          data: {
            format: 'jsonv2',
            countrycodes: 'ar',
            limit: 6,
            addressdetails: 1,
            q: q
          },
          headers: { 'Accept': 'application/json', 'Accept-Language': 'es' },
          success: function(data) {
            var $sug = $('#geo_sugerencias');
            $sug.empty();
            if (!data || !data.length) {
              $sug.append('<div class="list-group-item small text-muted py-2 px-3">No se encontraron ciudades, pueblos o direcciones</div>');
              $sug.show();
              return;
            }
            data.forEach(function(item) {
              var nombrePrincipal = item.name || (item.address && (item.address.city || item.address.town || item.address.village)) || item.display_name.split(',')[0];
              var tipo = item.type || item.class || 'Lugar';
              var icono = (item.type === 'city' || item.type === 'town' || item.type === 'village' || item.type === 'administrative') ? 'fa-city' : 'fa-map-marker-alt';
              var $item = $('<a href="#" class="list-group-item list-group-item-action py-2 px-3"></a>');
              $item.html('<div class="d-flex w-100 justify-content-between align-items-center">' +
                '<div><i class="fas ' + icono + ' text-primary me-2"></i><strong>' + nombrePrincipal + '</strong>' +
                '<div class="small text-muted text-truncate" style="max-width: 420px;">' + item.display_name + '</div></div>' +
                '<span class="badge bg-light text-dark border">' + tipo + '</span>' +
                '</div>');
              $item.on('click', function(e) {
                e.preventDefault();
                seleccionarUbicacion(item.lat, item.lon, item.display_name);
              });
              $sug.append($item);
            });
            $sug.show();
          },
          error: function() {
            $('#geo_sugerencias').hide();
          }
        });
      }, 350);
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#geo_input, #geo_sugerencias').length) {
        $('#geo_sugerencias').hide();
      }
    });

    function seleccionarUbicacion(lat, lng, nombre) {
      $('#geo_lat').val(lat);
      $('#geo_lng').val(lng);
      $('#geo_input').val('');
      $('#geo_sugerencias').hide().empty();
      $('#geo_lugar_lbl').text(nombre);
      $('#geo_radio_lbl').text($('#radio_km').val());
      $('#geo_seleccionado_info').show();
      $('#btn-limpiar-geo').show();
      $('#ciudad').val('');
      loadProperties();
    }

    function limpiarUbicacionGeo() {
      $('#geo_lat').val('');
      $('#geo_lng').val('');
      $('#geo_input').val('');
      $('#geo_seleccionado_info').hide();
      $('#btn-limpiar-geo').hide();
    }

    $('#btn-limpiar-geo, #link-quitar-geo').on('click', function(e) {
      e.preventDefault();
      limpiarUbicacionGeo();
      loadProperties();
    });

    $('#radio_km').on('change', function() {
      if ($('#geo_lat').val() && $('#geo_lng').val()) {
        $('#geo_radio_lbl').text($(this).val());
        loadProperties();
      }
    });

    // Botón GPS Cerca de mí
    $('#btn-gps').on('click', function() {
      if (!navigator.geolocation) {
        alert('Tu navegador no tiene soporte para geolocalización.');
        return;
      }
      var $btn = $(this);
      var origHtml = $btn.html();
      $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Ubicando...').prop('disabled', true);
      navigator.geolocation.getCurrentPosition(function(pos) {
        $btn.html(origHtml).prop('disabled', false);
        seleccionarUbicacion(pos.coords.latitude, pos.coords.longitude, 'Mi ubicación actual (GPS)');
      }, function(err) {
        $btn.html(origHtml).prop('disabled', false);
        alert('No se pudo obtener tu ubicación. Verificá los permisos de GPS de tu navegador.');
      }, { timeout: 8000, enableHighAccuracy: true });
    });

    // Clic en chips de ciudades o pueblos
    $('.btn-chip-ciudad').on('click', function() {
      var ciudad = $(this).data('ciudad');
      $('#ciudad').val(ciudad);
      limpiarUbicacionGeo();
      // Mostramos visualmente que se filtró por esa ciudad
      $('#geo_input').attr('placeholder', 'Filtrando por ciudad: ' + ciudad);
      loadProperties();
    });

    // Botón Limpiar filtros
    $('#btn-limpiar-filtros').on('click', function () {
      $('#search-form')[0].reset();
      limpiarUbicacionGeo();
      $('#geo_input').attr('placeholder', 'Escribí una ciudad, pueblo, barrio o dirección...');
      loadProperties();
      if (vistaActual === 'split' || vistaActual === 'mapa') { cargarMapaProperties(); }
    });

    // Al enviar el formulario de alerta, serializar los filtros actuales de la búsqueda
    $('#alert-form').on('submit', function () {
      var filtros = {};
      $('#search-form').serializeArray().forEach(function (item) {
        if (item.value !== '') { filtros[item.name] = item.value; }
      });
      $('#filtros_json').val(JSON.stringify(filtros));
    });
  });
</script>
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@stop


