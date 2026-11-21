@extends('layouts.landing')

@section('title', config('app.name') . ' | Portal inmobiliario')
@section('meta')
<meta name="description" content="{{ config('app.name') }}: el portal donde inmobiliarias verificadas publican sus propiedades. Buscá por zona o en el mapa y contactá directo con la inmobiliaria. ¿Tenés una inmobiliaria? Creá tu página gratis.">
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
    .hero {
      height: auto; /* Para que el contenido se expanda */
      display: flex;
      flex-direction: column;
      position: relative;
      height: 550px;
    }

    .carousel {
      order: 1; /* Mantiene el carrusel arriba */
    }

    .hero-content {
      order: 2; /* Mueve el texto debajo */
      position: static;
      transform: none;
      text-align: center;
      padding: 20px;
      background: rgba(0, 0, 0, 0.6); /* Opcional: fondo oscuro para mejor legibilidad */
    }

    .hero .container {
      position: absolute;
      left: 50%;
      transform: translate(-50%, -0%);
      text-align: center;
      z-index: 10;
      width: 100%;
      margin-top: 0px;
    }

    .carousel-item img {
      height: 275px; /* Ajusta la altura en dispositivos móviles */
    }

    .hero::before {
      background: none;
    }

    @media (orientation: landscape) {
      .hero {
        height: 50vh; /* Reduce la altura para adaptarse mejor al ancho */
        display: flex;
        flex-direction: row; /* Cambia la dirección para alinearlo horizontalmente */
        justify-content: space-between; /* Espaciado entre el carrusel y el contenido */
        align-items: center; /* Centra el contenido verticalmente */
      }

      .carousel-inner {
        height: 500px; /* Ajusta el tamaño del carrusel */
      }

      .carousel-item img {
        height: 100%;
        object-fit: cover; /* Asegura que la imagen siga cubriendo el área del carrusel */
      }

      .hero-content {
        order: 1; /* Mueve el contenido del texto hacia la izquierda */
        position: static;
        transform: none;
        text-align: left;
        padding: 30px;
        width: 30%; /* Ajusta el ancho del texto para no ocupar toda la pantalla */
        height: 10%;
      }

      .carousel {
        order: 2; /* Mueve el carrusel a la derecha */
        width: 50%; /* Ajusta el ancho del carrusel para que encaje mejor */
      }

      .hero .container {
        position: static;
        transform: none;
        width: 100%;
        margin-top: 0;
        height: 35vh; /* Reduce aún más la altura */
        padding: 10px 0; /* Reduce el espacio interno */
      }

      .hero .container h1,
      .hero .container p {
        font-size: 100%; /* Reduce el tamaño del texto */
      }

      .hero .container button {
        font-size: 100%; /* Reduce un poco el tamaño del botón */
        padding: 8px 12px; /* Ajusta el padding del botón */
      }

      .carousel-item img {
        height: 400px; /* Ajusta la altura del carrusel para que no ocupe tanto espacio */
      }

      .hero .container {
        position: static;
        transform: none;
        width: 100%;
        margin-top: 0;
        height: 30vh; /* Reduce aún más la altura */
        padding: 5px 0; /* Reduce el espacio interno */
      }

      .hero .container h1,
      .hero .container p {
        font-size: 100%; /* Ajusta el tamaño del texto */
        margin-bottom: 5px; /* Reduce el espacio entre texto y botón */
      }

      .hero .container button {
        font-size: 90%; /* Reduce un poco el tamaño del botón */
        padding: 6px 10px; /* Ajusta el padding para hacerlo más compacto */
        margin-top: 0px; /* Reduce la separación entre el texto y el botón */
      }

      .hero .container button {
        font-size: 80%; /* Reduce el tamaño del texto */
        padding: 5px 8px; /* Ajusta el tamaño del botón */
      }
    }
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
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);"></div>
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
                <small class="text-muted">{{ ucfirst($inmueble->tipo_operacion) }}</small>
              </div>
              <h5 class="card-title"><a href="{{ route('propiedad.show', [$inmueble->inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a></h5>
              <p class="card-text text-muted mb-1 small">{{ $inmueble->inmobiliaria->nombre_comercial ?? '' }}</p>
              <p class="card-text">{{ $inmueble->precio_formateado }} • {{ $inmueble->habitaciones }} Habitaciones • {{ $inmueble->banos }} Baños • {{ $inmueble->superficie_cubierta }} m²</p>
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">
                Ver Detalles
              </button>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade home-modal" id="propertyModal{{ $inmueble->id }}" tabindex="-1" aria-labelledby="propertyModalLabel{{ $inmueble->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="propertyModalLabel{{ $inmueble->id }}">{{ $inmueble->titulo }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-0">
                <div class="row g-0">
                  <div class="col-lg-6 bg-dark">
                    @if(!empty($inmueble->fotos))
                      <div id="modalCarousel{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                          @foreach(($inmueble->fotos ?? []) as $index => $foto)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                              <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100 modal-img" alt="{{ $inmueble->titulo }}">
                            </div>
                          @endforeach
                        </div>
                        @if(count($inmueble->fotos ?? []) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Siguiente</span>
                        </button>
                        @endif
                      </div>
                    @else
                      <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 240px;">
                        <i class="fas fa-image fa-3x text-white-50"></i>
                      </div>
                    @endif
                  </div>
                  <div class="col-lg-6 p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <span class="fw-bold" style="color:#4f46e5; font-size:1.3rem;">{{ $inmueble->precio_formateado }}</span>
                      @if($inmueble->inmobiliaria?->usuario?->hasRole('particular'))
                        <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span>
                      @else
                        <span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span>
                      @endif
                    </div>
                    <ul class="list-unstyled mb-3">
                      <li class="detalle-item mb-1">
                        <i class="fas fa-bed detalle-ico me-2"></i>{{ $inmueble->habitaciones }} hab.
                        <i class="fas fa-bath detalle-ico mx-1"></i>{{ $inmueble->banos }} baños
                        <i class="fas fa-ruler-combined detalle-ico mx-1"></i>{{ $inmueble->superficie_cubierta }} m&sup2;
                      </li>
                      @if($inmueble->tipo_propiedad)
                        <li class="detalle-item mb-1"><i class="fas fa-building detalle-ico me-2"></i>{{ \App\Models\Inmueble::tiposPropiedad()[$inmueble->tipo_propiedad] ?? ucfirst($inmueble->tipo_propiedad) }}</li>
                      @endif
                      @if($inmueble->ambientes)
                        <li class="detalle-item mb-1"><i class="fas fa-door-open detalle-ico me-2"></i>{{ $inmueble->ambientes }} ambientes</li>
                      @endif
                      @if($inmueble->cocheras)
                        <li class="detalle-item mb-1"><i class="fas fa-car detalle-ico me-2"></i>{{ $inmueble->cocheras }} cochera(s)</li>
                      @endif
                      @if($inmueble->expensas)
                        <li class="detalle-item mb-1"><i class="fas fa-receipt detalle-ico me-2"></i>Expensas ${{ number_format($inmueble->expensas, 2) }}</li>
                      @endif
                      @if($inmueble->orientacion)
                        <li class="detalle-item mb-1"><i class="fas fa-compass detalle-ico me-2"></i>{{ $inmueble->orientacion }}</li>
                      @endif
                      @if($inmueble->estado_propiedad)
                        <li class="detalle-item mb-1"><i class="fas fa-tag detalle-ico me-2"></i>{{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$inmueble->estado_propiedad] ?? $inmueble->estado_propiedad }}</li>
                      @endif
                      @if($inmueble->anio_construccion)
                        <li class="detalle-item mb-1"><i class="fas fa-calendar detalle-ico me-2"></i>Construido en {{ $inmueble->anio_construccion }}</li>
                      @endif
                    </ul>
                    @if($inmueble->piscina || $inmueble->jardin || $inmueble->garaje || $inmueble->ascensor)
                      <div class="mb-3">
                        @if($inmueble->piscina)<span class="badge text-bg-light border me-1"><i class="fas fa-swimmer me-1"></i>Piscina</span>@endif
                        @if($inmueble->jardin)<span class="badge text-bg-light border me-1"><i class="fas fa-tree me-1"></i>Jardín</span>@endif
                        @if($inmueble->garaje)<span class="badge text-bg-light border me-1"><i class="fas fa-warehouse me-1"></i>Garaje</span>@endif
                        @if($inmueble->ascensor)<span class="badge text-bg-light border me-1"><i class="fas fa-elevator me-1"></i>Ascensor</span>@endif
                      </div>
                    @endif
                    <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt detalle-ico me-2"></i>{{ $inmueble->direccion }}, {{ $inmueble->ciudad }}, {{ $inmueble->estado }}</p>
                    <p class="small text-muted mb-2">
                      <strong>Anunciante:</strong> {{ $inmueble->inmobiliaria->nombre_comercial ?? '' }}
                      @if($inmueble->inmobiliaria?->usuario?->hasRole('particular'))
                        <small class="text-success fw-bold">(Dueño directo · sin comisión)</small>
                      @endif
                    </p>
                    @if($inmueble->descripcion)
                      <hr class="my-2">
                      <p class="small mb-0" style="white-space: pre-line;">{{ $inmueble->descripcion }}</p>
                    @endif
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <a href="{{ route('inmobiliarias.show', $inmueble->inmobiliaria->slug) }}" class="btn btn-outline-primary">Ver inmobiliaria</a>
                <a href="{{ route('contacto', ['property' => $inmueble->id, 'inmobiliaria' => $inmueble->inmobiliaria_id]) }}" target="_blank" class="btn btn-primary">Me Interesa</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
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
