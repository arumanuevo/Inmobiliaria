@php
  // Helper JSON para bloques <script type="application/ld+json"> y popups: sin depender de Illuminate\Support\Json
  $jsonLd = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
@extends('layouts.landing')

@section('title', $inmueble->titulo . ' en ' . ($inmueble->ciudad ?: 'la zona') . ' | ' . $inmobiliaria->nombre_comercial)

@section('meta')
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($inmueble->descripcion), 155) }} — {{ $inmueble->precio_formateado }} · {{ $inmobiliaria->nombre_comercial }}">
<meta property="og:title" content="{{ $inmueble->titulo }} | {{ $inmobiliaria->nombre_comercial }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($inmueble->descripcion), 155) }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]) }}">
@if(!empty($inmueble->fotos))
<meta property="og:image" content="{{ !empty($inmueble->fotos) ? asset('images/fotosinmuebles/' . $inmueble->fotos[0]) : asset('images/logoinmo.svg') }}">
@endif
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "RealEstateListing",
  "name": {!! $jsonLd($inmueble->titulo) !!},
  "description": {!! $jsonLd(\Illuminate\Support\Str::limit(strip_tags($inmueble->descripcion), 300)) !!},
  "url": {!! $jsonLd(route('propiedad.show', [$inmobiliaria->slug, $inmueble->id])) !!},
  "address": {
    "@type": "PostalAddress",
    "streetAddress": {!! $jsonLd($inmueble->direccion) !!},
    "addressLocality": {!! $jsonLd($inmueble->ciudad) !!},
    "addressRegion": {!! $jsonLd($inmueble->estado) !!},
    "addressCountry": "AR"
  },
  "offers": {
    "@type": "Offer",
    "price": "{{ (float) $inmueble->precio }}",
    "priceCurrency": "{{ $inmueble->moneda ?? 'ARS' }}",
    "availability": "https://schema.org/InStock"
  },
  "numberOfRooms": {{ (int) ($inmueble->ambientes ?? $inmueble->habitaciones) }},
  "floorSize": { "@type": "QuantitativeValue", "value": {{ (float) $inmueble->superficie_cubierta }}, "unitCode": "MTK" }
}
</script>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  body { padding-top: 56px; }
  .ficha-foto { height: 420px; object-fit: cover; width: 100%; }
  .dato { border-bottom: 1px solid #eee; padding: 8px 0; }
  .dato strong { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; display: block; }
  /* Grilla de datos alineada con el contenido de arriba (sin el margen negativo del .row) */
  .datos-ficha { margin-left: 0; margin-right: 0; }
  .datos-ficha > [class*="col"] { padding-left: 0; padding-right: 16px; }
  .prox-ico { color: #6366f1; }
  #mapa-ficha { height: 300px; border-radius: 12px; border: 1px solid #ccc; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark navbar-links-modern fixed-top" style="background: {{ $inmobiliaria->color_tema ?: '#0d6efd' }};">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}">
      @if($inmobiliaria->logo)
        <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" width="30" height="30" class="d-inline-block align-text-top" style="border-radius: 6px; background:#fff; padding:2px;">
      @endif
      {{ $inmobiliaria->nombre_comercial }}
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Otras inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto', ['inmobiliaria' => $inmobiliaria->id]) }}">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="{{ route('inicio') }}">{{ config('app.name') }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}">{{ $inmobiliaria->nombre_comercial }}</a></li>
      <li class="breadcrumb-item active">{{ $inmueble->titulo }}</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      @if(!empty($inmueble->fotos))
        <div id="galeriaFicha" class="carousel slide mb-3" data-bs-ride="carousel">
          <div class="carousel-inner" style="border-radius: 12px; overflow: hidden;">
            @foreach(($inmueble->fotos ?? []) as $i => $foto)
              <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="ficha-foto" alt="{{ $inmueble->titulo }}">
              </div>
            @endforeach
          </div>
          @if(count($inmueble->fotos ?? []) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#galeriaFicha" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span><span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galeriaFicha" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span><span class="visually-hidden">Siguiente</span>
            </button>
          @endif
        </div>
      @endif

      <h1 class="h3">{{ $inmueble->titulo }}</h1>
      <p class="text-muted mb-2">
        {{ $inmueble->direccion }}, {{ $inmueble->ciudad }}{{ $inmueble->estado ? ', ' . $inmueble->estado : '' }}
      </p>
      <p class="mb-3">
        <span class="badge bg-primary">{{ ucfirst($inmueble->tipo_operacion) }}</span>
        @if($inmueble->tipo_propiedad)
          <span class="badge bg-secondary">{{ \App\Models\Inmueble::tiposPropiedad()[$inmueble->tipo_propiedad] ?? ucfirst($inmueble->tipo_propiedad) }}</span>
        @endif
        @if($inmueble->destacado)
          <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Destacada</span>
        @endif
        @if($inmueble->estado_propiedad)
          <span class="badge bg-info text-dark">{{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$inmueble->estado_propiedad] ?? $inmueble->estado_propiedad }}</span>
        @endif
        @if($inmueble->uso_detalle)
          <span class="badge text-bg-light border"><i class="fas fa-store me-1"></i>{{ $inmueble->uso_detalle }}</span>
        @endif
      </p>

      @if($inmueble->puntaje_entorno !== null)
        @php
          $pe = $inmueble->puntajeEntornoLabel();
          $peDet = is_array($inmueble->puntaje_entorno_detalle) ? $inmueble->puntaje_entorno_detalle : [];
        @endphp
        <div class="mb-3">
          <span class="badge" style="background: {{ $pe['bg'] }}; color: {{ $pe['color'] }}; border: 1px solid {{ $pe['borde'] }}; font-size: .85rem; padding: 6px 12px;">
            <i class="fas fa-chart-line me-1"></i> Puntaje de entorno: <strong>{{ $inmueble->puntaje_entorno }}/100</strong> — {{ $pe['texto'] }}
          </span>
          @if(!empty($peDet))
            <div class="small text-muted mt-1">
              @if(!empty($peDet['arteria']))<span class="me-2"><i class="fas fa-road me-1"></i>{{ $peDet['arteria']['label'] }} (+{{ $peDet['arteria']['puntos'] }})</span>@endif
              @if(!empty($peDet['transporte']))<span class="me-2"><i class="fas fa-bus me-1"></i>{{ $peDet['transporte']['estaciones_tren_subte'] ?? 0 }} est. tren/subte · {{ $peDet['transporte']['paradas_bus'] ?? 0 }} paradas (+{{ $peDet['transporte']['puntos'] ?? 0 }})</span>@endif
              @if(!empty($peDet['comercio']))<span class="me-2"><i class="fas fa-store me-1"></i>{{ $peDet['comercio']['cantidad'] ?? 0 }} comercios (+{{ $peDet['comercio']['puntos'] ?? 0 }})</span>@endif
              @if(!empty($peDet['servicios']))<span class="me-2"><i class="fas fa-concierge-bell me-1"></i>{{ $peDet['servicios']['cantidad'] ?? 0 }} servicios (+{{ $peDet['servicios']['puntos'] ?? 0 }})</span>@endif
            </div>
          @endif
        </div>
      @endif

      <h2 class="h4 fw-bold mb-3" style="color: {{ $inmobiliaria->color_tema ?: '#0d6efd' }};">{{ $inmueble->precio_formateado }}
        @if($inmueble->expensas)<small class="text-muted fw-normal">+ @dinero($inmueble->expensas, 2) expensas</small>@endif
      </h2>
      @if(!empty($comparativa))
        <p class="mb-3">
          @if($comparativa['pct'] < 0)
            <span class="badge bg-success"><i class="fas fa-arrow-down"></i> {{ abs($comparativa['pct']) }}% debajo del promedio de {{ $inmueble->ciudad }}</span>
          @elseif($comparativa['pct'] > 5)
            <span class="badge bg-secondary">{{ $comparativa['pct'] }}% sobre el promedio de {{ $inmueble->ciudad }}</span>
          @else
            <span class="badge bg-info text-dark">En línea con el promedio de {{ $inmueble->ciudad }}</span>
          @endif
          <small class="text-muted">{{ $inmueble->precio_formateado }} / {{ $inmueble->superficie_cubierta }} m² = @dinero($comparativa['m2'], 0)/m² · promedio de la zona: @dinero($comparativa['promedio_m2'], 0)/m² ({{ $comparativa['n'] }} propiedades)</small>
        </p>
      @endif

      <div class="row mb-4 datos-ficha">
        <div class="col-6 col-md-3 dato"><strong>Superficie</strong>{{ $inmueble->superficie_cubierta }} m²</div>
        <div class="col-6 col-md-3 dato"><strong>Ambientes</strong>{{ $inmueble->ambientes ?? '-' }}</div>
        <div class="col-6 col-md-3 dato"><strong>Dormitorios</strong>{{ $inmueble->habitaciones }}</div>
        <div class="col-6 col-md-3 dato"><strong>Baños</strong>{{ $inmueble->banos }}</div>
        <div class="col-6 col-md-3 dato"><strong>Cocheras</strong>{{ $inmueble->cocheras ?? 0 }}</div>
        <div class="col-6 col-md-3 dato"><strong>Orientación</strong>{{ $inmueble->orientacion ?: '-' }}</div>
        <div class="col-6 col-md-3 dato"><strong>Año</strong>{{ $inmueble->anio_construccion ?: '-' }}</div>
        <div class="col-6 col-md-3 dato"><strong>Estado</strong>{{ $inmueble->estado_propiedad ? ucfirst(str_replace('_',' ',$inmueble->estado_propiedad)) : '-' }}</div>
      </div>

      <h5>Descripción</h5>
      <p style="white-space: pre-line;">{{ $inmueble->descripcion }}</p>

      <h5>Comodidades</h5>
      <div class="mb-2">
        @if($inmueble->piscina)<span class="badge text-bg-light border me-1"><i class="fas fa-swimmer me-1 prox-ico"></i>Piscina</span>@endif
        @if($inmueble->jardin)<span class="badge text-bg-light border me-1"><i class="fas fa-tree me-1 prox-ico"></i>Jardín</span>@endif
        @if($inmueble->garaje)<span class="badge text-bg-light border me-1"><i class="fas fa-warehouse me-1 prox-ico"></i>Garaje</span>@endif
        @if($inmueble->ascensor)<span class="badge text-bg-light border me-1"><i class="fas fa-elevator me-1 prox-ico"></i>Ascensor</span>@endif
        @if(!$inmueble->piscina && !$inmueble->jardin && !$inmueble->garaje && !$inmueble->ascensor)<span class="text-muted">Sin comodidades adicionales</span>@endif
      </div>

      @include('inmuebles.partials.proximidades', ['inmueble' => $inmueble])

      @if($inmueble->coordenadas)
        <h5>Ubicación</h5>
        <div id="mapa-ficha" class="mb-3"></div>
      @endif
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm mb-3" style="border-top: 4px solid {{ $inmobiliaria->color_tema ?: '#0d6efd' }};">
        <div class="card-body text-center">
          @if($inmobiliaria->logo)
            <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" style="max-height: 60px;" class="mb-2">
          @endif
          <h5 class="card-title mb-1">
            {{ $inmobiliaria->nombre_comercial }}
          </h5>
          @if($inmobiliaria->usuario?->hasRole('particular'))
            <div class="mb-2"><span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span> <small class="text-success fw-bold d-block mt-1">Operación sin comisión inmobiliaria</small></div>
          @else
            <div class="mb-2"><span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span></div>
          @endif
          <p class="text-muted small mb-3">{{ $inmobiliaria->ciudad }}{{ $inmobiliaria->provincia ? ', ' . $inmobiliaria->provincia : '' }}</p>
          <a href="{{ route('contacto', ['property' => $inmueble->id, 'inmobiliaria' => $inmobiliaria->id]) }}" class="btn btn-primary w-100 mb-2">Me interesa</a>
          @if($inmobiliaria->whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inmobiliaria->whatsapp) }}?text={{ urlencode('Hola, me interesa la propiedad: ' . $inmueble->titulo) }}" target="_blank" class="btn btn-success w-100 mb-2"><i class="fab fa-whatsapp"></i> Consultar por WhatsApp</a>
          @endif
          @if($inmobiliaria->telefono)
            <a href="tel:{{ $inmobiliaria->telefono }}" class="btn btn-outline-secondary w-100"><i class="fas fa-phone"></i> {{ $inmobiliaria->telefono }}</a>
          @endif
          <hr>
          <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" class="small">Ver todas las propiedades de este anunciante</a>
          <div class="mt-3 pt-2 border-top text-start">
            @include('partials.share', [
              'shareUrl' => route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]),
              'shareTitulo' => $inmueble->titulo . ' · ' . $inmueble->precio_formateado . ' · ' . $inmobiliaria->nombre_comercial,
            ])
          </div>
        </div>
      </div>

      <div class="alert alert-light border small text-muted">
        <i class="fas fa-info-circle"></i>
        {{ config('app.name') }} es un portal de avisos: no interviene en la operación ni verifica los datos publicados; el anunciante declaró ser propietario o estar autorizado. Verificá todo antes de señar.
        <a href="{{ route('aviso-legal') }}">Aviso legal</a>
      </div>

      @if($relacionados->count())
        <h6 class="mb-3">Otras propiedades de {{ $inmobiliaria->nombre_comercial }}</h6>
        @foreach($relacionados as $rel)
          <div class="card mb-2">
            <div class="row g-0">
              @if(!empty($rel->fotos))
                <div class="col-4">
                  <img src="{{ asset('images/fotosinmuebles/' . $rel->fotos[0]) }}" class="img-fluid rounded-start" style="height: 80px; width: 100%; object-fit: cover;" alt="{{ $rel->titulo }}">
                </div>
              @endif
              <div class="col-{{ !empty($rel->fotos) ? '8' : '12' }}">
                <div class="card-body py-2 px-3">
                  <a href="{{ route('propiedad.show', [$inmobiliaria->slug, $rel->id]) }}" class="fw-bold small text-decoration-none">{{ $rel->titulo }}</a>
                  <div class="text-muted small">{{ $rel->precio_formateado }} · {{ $rel->ciudad }}</div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} {{ $inmobiliaria->nombre_comercial }} | Todos los derechos reservados</p>
    <p><small class="text-muted">Sitio potenciado por {{ config('app.name') }}</small></p>
  </div>
</footer>
@stop

@section('js')
@if($inmueble->coordenadas)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function initMapaFicha() {
    var div = document.getElementById('mapa-ficha');
    if (!div) { return; }
    if (typeof L === 'undefined') {
        div.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted small p-3">No se pudo cargar el mapa. Recargá la página.</div>';
        return;
    }
    var partes = '{{ $inmueble->coordenadas }}'.split(',');
    var lat = parseFloat(partes[0]), lng = parseFloat(partes[1]);
    if (isNaN(lat) || isNaN(lng)) { return; }
    var map = L.map('mapa-ficha', { scrollWheelZoom: false }).setView([lat, lng], 16);
    var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18, attribution: 'Tiles &copy; Esri' });
    L.control.layers({ 'Callejera': osm, 'Satelital': sat }, null, { position: 'topright' }).addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup({!! $jsonLd($inmueble->titulo) !!}).openPopup();
    setTimeout(function () { map.invalidateSize(); }, 300);
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMapaFicha);
} else {
    initMapaFicha();
}
</script>
@endif
@stop
