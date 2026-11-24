@extends('layouts.landing')

@section('title', config('app.name') . ' | Buscá propiedades o creá tu propia inmobiliaria online')
@section('meta')
<meta name="description" content="{{ config('app.name') }}: buscá propiedades de inmobiliarias verificadas y dueños directos por zona o en el mapa, o creá gratis tu propia inmobiliaria online con perfil público, mapa y publicaciones ilimitadas según tu plan.">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ route('inicio') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ config('app.name') }} | Buscá propiedades o creá tu propia inmobiliaria online">
<meta property="og:description" content="Portal inmobiliario: propiedades verificadas y dueños directos, y tu propia inmobiliaria online con perfil público propio.">
<meta property="og:url" content="{{ route('inicio') }}">
<meta property="og:image" content="{{ asset('images/logoinmo.svg') }}">
<meta name="twitter:card" content="summary">
<script type="application/ld+json">{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Organization',
  'name' => config('app.name'),
  'url' => route('inicio'),
  'logo' => asset('images/logoinmo.svg'),
  'sameAs' => array_values(array_filter([config('app.url')])),
  'description' => 'Portal inmobiliario SaaS: buscador de propiedades y creación de inmobiliarias online con perfil público propio.',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  .carousel-inner {
    height: auto; /* Ajusta según necesites */
  }

  .carousel-item img {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Asegura que la imagen se recorte en lugar de deformarse */
  }
  /* Modal de detalle de la home: scrollable y en 2 columnas en pantallas grandes */
  .home-modal .modal-content { border: none; border-radius: 18px; overflow: hidden; }
  .home-modal .modal-img { height: 240px !important; object-fit: cover; }
  @media (min-width: 992px) {
    .home-modal .modal-img { height: 420px !important; }
  }
  .home-modal .detalle-ico { color: #6366f1; }
  .home-modal .detalle-item { font-size: .92rem; }

  .hero {
    position: relative;
    height: 60vh;
    min-height: 520px; /* que el contenido (título + 3 botones) siempre entre sin cortarse */
    overflow: hidden;
    background: linear-gradient(135deg, #101828 0%, #1d2939 100%);
  }

  .hero .container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 10;
    width: 100%;
  }

  .hero h1, .hero p {
    margin-bottom: 15px;
  }

  .hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 5;
  }

  @media (max-width: 768px) {
    /* Mobile: hero en columna con altura automática para que NUNCA se corten los botones */
    .hero {
      height: auto;
      min-height: 0;
      overflow: visible;
      display: flex;
      flex-direction: column;
    }
    .hero-overlay { display: none; }
    .hero::before { background: none; }
    .carousel { order: 1; width: 100%; flex: 0 0 auto; }
    .carousel-item img { height: 220px; }
    .hero .container,
    .hero-content {
      order: 2;
      position: static;
      transform: none;
      left: auto;
      top: auto;
      width: 100%;
      max-width: 100%;
      margin: 0;
      z-index: 10;
      text-align: center;
      padding: 26px 16px 30px;
      background: linear-gradient(180deg, rgba(16,24,40,.96) 0%, rgba(29,41,57,.94) 100%);
    }
    .hero-content h1 { font-size: 1.5rem; }
    .hero-content .lead { font-size: .95rem; }
    /* Botones a ancho completo en mobile: ninguno queda semi oculto */
    .hero-content .btn { width: 100%; max-width: 340px; font-size: .98rem; padding: .6rem 1rem; }
  }

  .footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px 0; /* Reduce el padding para disminuir la altura */
    font-size: 10px; /* Opcional: reduce el tamaño del texto */
  }

  .footer p {
    margin: 0; /* Elimina márgenes extra */
  }

  .carousel-destacado .carousel-inner {
    height: 200px; /* Altura específica para los carruseles destacados */
  }

  /* Sección "Recién publicadas" */
  .reciente-card { border-radius: 16px; overflow: hidden; transition: transform .18s ease, box-shadow .18s ease; }
  .reciente-card:hover { transform: translateY(-4px); box-shadow: 0 16px 34px rgba(16,24,40,.16) !important; }
  .reciente-card .card-img-top { height: 150px; object-fit: cover; }
  .reciente-sin-foto {
    height: 150px; display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%); color: #a5b4fc;
  }
  .reciente-chip {
    display: inline-block; background: #eef2ff; color: #4338ca; font-size: .7rem; font-weight: 700;
    border-radius: 999px; padding: 2px 9px; margin: 0 4px 4px 0; text-transform: capitalize;
  }
  .reciente-chip-op { background: #ecfdf3; color: #067647; }
  .reciente-titulo {
    font-weight: 700; color: #101828; font-size: .92rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .reciente-precio { font-weight: 800; color: #4338ca; font-size: 1rem; margin: 2px 0; }
  .reciente-ubi { font-size: .78rem; color: #475467; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  @media (max-width: 768px) {
    .reciente-card .card-img-top, .reciente-sin-foto { height: 110px; }
    .reciente-titulo { font-size: .85rem; }
    .reciente-precio { font-size: .92rem; }
  }
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
        <li class="nav-item"><a class="nav-link active" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link text-success fw-bold" href="{{ route('register', ['tipo' => 'particular']) }}"><i class="fas fa-plus-circle me-1"></i> Publicar gratis</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>


<!-- Agregar padding superior para evitar que el contenido quede tapado por el navbar fijo -->
<div style="padding-top: 56px;">
  <!-- Sección Hero -->
  <section class="hero" style="position: relative;">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
      <ol class="carousel-indicators" id="carousel-indicators">
        <!-- Indicadores dinámicos -->
      </ol>
      <div class="carousel-inner" id="carousel-inner">
        <!-- Imágenes dinámicas -->
      </div>
    </div>
    <div class="hero-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);"></div>
    <div class="container hero-content">
      <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-11">
          <h1 class="display-5 fw-bold text-white">Encontrá tu próximo hogar o publicá tu propiedad</h1>
          <p class="lead text-white">Explorá propiedades de inmobiliarias verificadas y dueños directos de la zona, o publicá tu inmueble sin intermediarios.</p>
          <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
            <a href="{{ route('propiedades.index') }}" class="btn btn-primary btn-lg shadow-sm">
              <i class="fas fa-search me-1"></i> Buscar propiedades
            </a>
            <a href="{{ route('register', ['tipo' => 'particular']) }}" class="btn btn-success btn-lg shadow-sm">
              <i class="fas fa-home me-1"></i> Publicar mi inmueble gratis
            </a>
            <a href="{{ route('sumate') }}" class="btn btn-outline-light btn-lg">
              <i class="fas fa-building me-1"></i> Soy inmobiliaria
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Qué es el portal -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
          <img src="{{ asset('images/about.jpg') }}" class="img-fluid rounded" alt="{{ config('app.name') }}">
        </div>
        <div class="col-md-6">
          <h2 class="mb-4">¿Qué es {{ config('app.name') }}?</h2>
          <p>Somos el portal donde las <strong>inmobiliarias de la zona</strong> y los <strong>dueños particulares</strong> publican sus propiedades de manera transparente. Vos buscás por características o en el mapa interactivo y contactás directo con el anunciante: sin intermediarios ni comisiones sorpresa.</p>
          <p>Buscá por localidad o en el mapa con geolocalización, guardá tus alertas y recibí un aviso por email cuando aparezca una propiedad que coincida con tus gustos. ¿Querés vender o alquilar tu casa? Publicala gratis en minutos.</p>
          <div class="row text-center my-4">
            <div class="col-4">
              <div class="h3 fw-bold text-primary mb-0">{{ $stats['inmobiliarias'] }}</div>
              <small class="text-muted">inmobiliarias verificadas</small>
            </div>
            <div class="col-4">
              <div class="h3 fw-bold text-primary mb-0">{{ $stats['propiedades'] }}</div>
              <small class="text-muted">propiedades publicadas</small>
            </div>
            <div class="col-4">
              <div class="h3 fw-bold text-primary mb-0">{{ $stats['operaciones'] }}</div>
              <small class="text-muted">operaciones concretadas</small>
            </div>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('register', ['tipo' => 'particular']) }}" class="btn btn-success"><i class="fas fa-home me-1"></i> Publicar como particular</a>
            <a href="{{ route('sumate') }}" class="btn btn-outline-primary"><i class="fas fa-building me-1"></i> Sumá tu inmobiliaria</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Propiedades Destacadas -->
  <section id="properties" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="mb-4">Propiedades Destacadas</h2>
        <p class="text-muted">Seleccionadas por las inmobiliarias verificadas del portal.</p>
      </div>
      <div class="row">
        @forelse($destacados as $inmueble)
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm">
            <div id="carousel{{ $inmueble->id }}" class="carousel carousel-destacado slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                @foreach(($inmueble->fotos ?? []) as $index => $foto)
                  <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                  </div>
                @endforeach
              </div>
              <a class="carousel-control-prev" href="#carousel{{ $inmueble->id }}" role="button" data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carousel{{ $inmueble->id }}" role="button" data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </a>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                @if($inmueble->inmobiliaria?->usuario?->hasRole('particular'))
                  <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span>
                @else
                  <span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span>
                @endif
                @php
                  // OJO: nunca pegar una directiva de Blade justo después de una letra (el @ debe ir precedido de espacio o no-word) porque el regex \B@ no la compila y después explota con "unexpected endif"
                  $lineaTipo = ucfirst($inmueble->tipo_operacion);
                  if ($inmueble->tipo_propiedad) {
                      $lineaTipo .= ' &middot; ' . (\App\Models\Inmueble::tiposPropiedad()[$inmueble->tipo_propiedad] ?? ucfirst($inmueble->tipo_propiedad));
                  }
                  if ($inmueble->puntaje_entorno !== null) {
                      $lineaTipo .= ' &middot; <i class="fas fa-chart-line"></i> ' . $inmueble->puntaje_entorno . '/100';
                  }
                @endphp
                <small class="text-muted">{!! $lineaTipo !!}</small>
              </div>
              <h5 class="card-title"><a href="{{ route('propiedad.show', [$inmueble->inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a></h5>
              <p class="card-text text-muted mb-1 small">{{ $inmueble->inmobiliaria->nombre_comercial ?? '' }}</p>
              <p class="card-text">{{ $inmueble->precio_formateado }} • {{ $inmueble->habitaciones }} Habitaciones • {{ $inmueble->banos }} Baños • {{ $inmueble->superficie_cubierta }} m²</p>
              <a href="{{ route('propiedad.show', [$inmueble->inmobiliaria->slug, $inmueble->id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-eye me-1"></i> Ver ficha
              </a>
              <a href="{{ route('propiedades.index', ['vista' => 'mapa', 'prop' => $inmueble->id]) }}" class="btn btn-outline-primary btn-sm ms-1" title="Ver esta propiedad en el mapa del portal">
                <i class="fas fa-map-marked-alt me-1"></i> En el mapa
              </a>
            </div>
          </div>
        </div>

        @empty
        <div class="col-12 text-center text-muted py-4">
          Todavía no hay propiedades destacadas. <a href="{{ route('propiedades.index') }}">Explorá todas las propiedades del portal</a>
        </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Sección Nuevas publicaciones (recién agregadas al portal) -->
  @if(isset($recientes) && $recientes->count())
  <section id="recientes" class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <div>
          <h2 class="fw-bold mb-1"><i class="fas fa-bolt me-2" style="color:#6366f1;"></i>Recién publicadas</h2>
          <p class="text-muted mb-0">Las últimas propiedades agregadas al portal.</p>
        </div>
        <a href="{{ route('propiedades.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
          Ver todas <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="row g-3">
        @foreach($recientes as $r)
          @if($r->inmobiliaria)
          <div class="col-6 col-md-3">
            <a href="{{ route('propiedad.show', [$r->inmobiliaria->slug, $r->id]) }}" class="text-decoration-none">
              <div class="card reciente-card h-100 border-0 shadow-sm">
                @if(!empty($r->fotos))
                  <img src="{{ asset('images/fotosinmuebles/' . $r->fotos[0]) }}" class="card-img-top" alt="{{ $r->titulo }}" loading="lazy">
                @else
                  <div class="reciente-sin-foto"><i class="fas fa-home fa-2x"></i></div>
                @endif
                <div class="card-body p-3">
                  <div>
                    <span class="reciente-chip">{{ $r->tipo_propiedad }}</span>
                    <span class="reciente-chip reciente-chip-op">{{ ucfirst($r->tipo_operacion) }}</span>
                  </div>
                  <div class="reciente-titulo" title="{{ $r->titulo }}">{{ $r->titulo }}</div>
                  <div class="reciente-precio">@if($r->moneda === 'USD')US$ @endif{{ $r->precio_formateado }}</div>
                  <div class="reciente-ubi"><i class="fas fa-map-marker-alt me-1"></i>{{ $r->ciudad ?: 'Sin zona' }} &middot; {{ $r->inmobiliaria->nombre_comercial }}</div>
                </div>
              </div>
            </a>
          </div>
          @endif
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- Sección Inmobiliarias verificadas (datos reales del portal) -->
  <section id="inmobiliarias" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Inmobiliarias verificadas</h2>
        <p class="text-muted">Cada inmobiliaria del portal tiene su propia página pública y pasa por aprobación antes de figurar.</p>
      </div>
      <div class="row">
        @forelse($inmobiliariasTop as $inmobiliaria)
        <div class="col-md-4 mb-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
              @if($inmobiliaria->logo)
                <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" class="mb-2" style="max-height: 60px; max-width: 140px; object-fit: contain;">
              @else
                <div class="rounded bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; font-weight: 700; font-size: 24px;">{{ mb_substr($inmobiliaria->nombre_comercial, 0, 1) }}</div>
              @endif
              <h5 class="card-title mb-1">
                {{ $inmobiliaria->nombre_comercial }}
                @if($inmobiliaria->usuario?->hasRole('particular'))
                  <span class="badge bg-secondary ms-1">Particular</span>
                @endif
              </h5>
              <p class="text-muted small mb-2">{{ $inmobiliaria->ciudad }}{{ $inmobiliaria->provincia ? ', ' . $inmobiliaria->provincia : '' }}</p>
              <p class="small mb-3">{{ $inmobiliaria->inmuebles_publicados_count }} propiedades &middot; {{ $inmobiliaria->operaciones_count }} operaciones</p>
              <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" class="btn btn-outline-primary btn-sm">Ver vidriera</a>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4">
          Pronto vas a ver acá las inmobiliarias verificadas. <a href="{{ route('inmobiliarias.index') }}">Ver directorio</a>
        </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Sección Llamada a la Acción -->
  <section id="cta" class="py-5 bg-primary text-white">
    <div class="container text-center">
      <h2 class="mb-4">¿Buscás propiedad o querés publicar la tuya?</h2>
      <p class="mb-4">Explorá todas las propiedades del portal, creá gratis la página pública de tu inmobiliaria o publicá tu propia vivienda como particular.</p>
      <a href="{{ route('propiedades.index') }}" class="btn btn-light btn-lg me-2 mb-2">Buscar propiedades</a>
      <a href="{{ route('sumate') }}" class="btn btn-outline-light btn-lg me-2 mb-2">Soy inmobiliaria</a>
      <a href="{{ route('register', ['tipo' => 'particular']) }}" class="btn btn-outline-light btn-lg mb-2">Publicar mi propiedad (gratis)</a>
    </div>
  </section>
  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>© {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
      <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
    </div>
  </footer>
<!-- Mensaje de éxito -->
@if(session('success'))

  <script>
    alert('Su mensaje fue enviado con éxito! Pronto nos pondremos en contacto con usted.');
  </script>
@endif

</div>
@stop

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const carouselInner = document.getElementById('carousel-inner');
    const carouselIndicators = document.getElementById('carousel-indicators');

    fetch('/portadaimages')
      .then(response => response.json())
      .then(images => {
        images.forEach((image, index) => {
          // Crear el elemento del carrusel
          const carouselItem = document.createElement('div');
          carouselItem.classList.add('carousel-item');
          if (index === 0) {
            carouselItem.classList.add('active');
          }
          carouselItem.innerHTML = `<img src="/images/portada/${image}" class="d-block w-100" alt="...">`;
          carouselInner.appendChild(carouselItem);

          // Crear el indicador del carrusel
          const indicator = document.createElement('li');
          indicator.setAttribute('data-bs-target', '#carouselExampleIndicators');
          indicator.setAttribute('data-bs-slide-to', index);
          if (index === 0) {
            indicator.classList.add('active');
          }
          carouselIndicators.appendChild(indicator);
        });
      })
      .catch(error => console.error('Error fetching images:', error));
  });
</script>
@stop
