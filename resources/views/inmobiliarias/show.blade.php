@extends('layouts.landing')

@php
  $preview = $preview ?? false;
  $tema = $inmobiliaria->tema ?: 'moderno';
  // Píldora solo para botones/badges; las tarjetas nunca van en píldora (el contenido queda mal)
  $radius = ['moderno' => '10px', 'elegante' => '6px', 'minimal' => '999px', 'corporativo' => '8px'][$tema] ?? '10px';
  $radiusCard = ['moderno' => '14px', 'elegante' => '6px', 'minimal' => '18px', 'corporativo' => '8px'][$tema] ?? '14px';
  $fuenteTitulos = $tema === 'elegante' ? 'Georgia, "Times New Roman", serif' : null;
  $fuenteBody = $tema === 'corporativo' ? 'Arial, Helvetica, sans-serif' : null;
  // Mapa del perfil: se muestra si mostrar_mapa está activo y hay oficina con coords o propiedades geocodificadas
  $oficinaCoords = $inmobiliaria->lat && $inmobiliaria->lng;
  $tieneMapa = (bool) $inmobiliaria->mostrar_mapa && ($oficinaCoords || $inmueblesMapa->count() > 0);
  $zonas = array_values(array_filter(array_map('trim', explode(',', (string) $inmobiliaria->zonas_cobertura))));
  // Markers de propiedades para el mapa (lat,lng viene como "lat,lng" en coordenadas)
  $markers = $inmueblesMapa->map(function ($i) use ($inmobiliaria, $preview) {
      $p = array_map('trim', explode(',', (string) $i->coordenadas));
      if (count($p) !== 2 || !is_numeric($p[0]) || !is_numeric($p[1])) { return null; }
      $fotos = $i->fotos ?? [];
      return [
          'id' => $i->id,
          'lat' => (float) $p[0],
          'lng' => (float) $p[1],
          'titulo' => e((string) $i->titulo),
          'precio' => $i->precio_formateado,
          'ciudad' => e((string) ($i->ciudad ?: '')),
          'foto' => !empty($fotos) ? asset('images/fotosinmuebles/' . $fotos[0]) : null,
          'url' => ($preview && !$i->publicado) ? route('inmuebles.edit', $i->id) : route('propiedad.show', [$inmobiliaria->slug, $i->id]),
      ];
  })->filter()->values();
  $brand = $inmobiliaria->color_tema ?: '#6366f1';
  // OJO: @json(expr) de Blade hace explode(',') del argumento -> no usar expresiones con comas adentro
  $oficinaPopup = '<strong>' . e($inmobiliaria->nombre_comercial) . '</strong><br>'
      . e(trim(($inmobiliaria->direccion ? $inmobiliaria->direccion . ', ' : '') . $inmobiliaria->ciudad . ', ' . $inmobiliaria->provincia, ', '))
      . '<br><small>Oficina</small>';
@endphp

@section('title', $inmobiliaria->nombre_comercial . ($preview ? ' (vista previa)' : ''))

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
@if($tieneMapa)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endif
<style>
  :root { --tema-radius: {{ $radius }}; --tema-radius-card: {{ $radiusCard }}; }
  @if($fuenteBody) body { font-family: {{ $fuenteBody }}; } @endif
  @if($fuenteTitulos) h1, h2, h3, h5, .card-title { font-family: {{ $fuenteTitulos }}; } @endif
  .card, .property-card .card { border-radius: var(--tema-radius-card); overflow: hidden; }
  .btn, .badge { border-radius: var(--tema-radius); }
  body { padding-top: 56px; }
  .perfil-header {
    background: {{ $inmobiliaria->color_tema ?: '#0d6efd' }};
    color: #fff;
    padding: 40px 0;
  }
  .perfil-logo {
    max-height: 100px;
    max-width: 160px;
    background: #fff;
    border-radius: 8px;
    padding: 6px;
  }
  .perfil-inicial {
    width: 100px; height: 100px; font-size: 2.5rem;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
  }
  .property-card img { height: 200px; object-fit: cover; }
  /* Modal de detalle: scrollable (el body hace scroll, header/footer quedan fijos) y en 2 columnas en pantallas grandes */
  .perfil-modal .modal-content { border: none; border-radius: 18px; overflow: hidden; }
  .perfil-modal .modal-img { height: 240px; object-fit: cover; }
  @media (min-width: 992px) {
    .perfil-modal .modal-img { height: 420px; }
  }
  .perfil-modal .detalle-ico { color: {{ $inmobiliaria->color_tema ?: '#0d6efd' }}; }
  .perfil-modal .detalle-item { font-size: .92rem; }
  .perfil-modal .prox-ico { color: {{ $inmobiliaria->color_tema ?: '#0d6efd' }}; }
  .footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px 0;
    font-size: 10px;
  }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark portal-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}">
      @if($inmobiliaria->logo)
        <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" width="30" height="30" class="d-inline-block align-text-top" style="border-radius: 6px; background: #fff; padding: 2px;">
      @else
        <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      @endif
      {{ $inmobiliaria->nombre_comercial }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Otras inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

@if($preview)
<div class="alert alert-warning mb-0 rounded-0 text-center small fw-semibold py-2">
  <i class="fas fa-eye me-1"></i> MODO VISTA PREVIA: estás viendo tu perfil como lo ve un visitante, incluyendo los borradores (marcados en amarillo).
  <a href="{{ route('inmobiliaria.perfil') }}" class="alert-link ms-1">Volver a editar</a>
</div>
@endif

@if(!empty($inmobiliaria->galeria) && count($inmobiliaria->galeria) > 1)
<div id="jumbotronGaleria" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner" style="height: 320px;">
    @foreach($inmobiliaria->galeria as $i => $img)
      <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" style="height: 320px;">
        <img src="{{ asset($img) }}" class="d-block w-100" style="height: 320px; object-fit: cover;" alt="{{ $inmobiliaria->nombre_comercial }}">
      </div>
    @endforeach
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#jumbotronGaleria" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Anterior</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#jumbotronGaleria" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Siguiente</span>
  </button>
</div>
@elseif($inmobiliaria->imagen_portada || !empty($inmobiliaria->galeria))
  <div style="height: 320px; background: url('{{ asset($inmobiliaria->imagen_portada ?: $inmobiliaria->galeria[0]) }}') center/cover no-repeat;"></div>
@endif

<header class="perfil-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-3 text-center mb-3 mb-md-0">
        @if($inmobiliaria->logo)
          <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" class="perfil-logo">
        @else
          <div class="perfil-inicial">{{ strtoupper(substr($inmobiliaria->nombre_comercial, 0, 1)) }}</div>
        @endif
      </div>
      <div class="col-md-9">
        <h1 class="mb-1">{{ $inmobiliaria->nombre_comercial }}</h1>
        @if($inmobiliaria->mensaje_destacado)
          <p class="mb-1"><span class="badge" style="background: rgba(255,255,255,.22); color: #fff; font-size: 13px; padding: 7px 14px;"><i class="fas fa-bullhorn me-1"></i>{{ $inmobiliaria->mensaje_destacado }}</span></p>
        @endif
        @if($inmobiliaria->mostrar_operaciones && $inmobiliaria->operaciones()->count() > 0)
          <p class="mb-1"><span class="badge bg-light text-dark"><i class="fas fa-check-circle"></i> {{ $inmobiliaria->operaciones()->count() }} {{ Str::plural('operación', $inmobiliaria->operaciones()->count()) }} concretada(s)</span></p>
        @endif
        <p class="mb-1">{{ trim(($inmobiliaria->direccion ? $inmobiliaria->direccion . ', ' : '') . $inmobiliaria->ciudad . ', ' . $inmobiliaria->provincia, ', ') }}</p>
        @if($inmobiliaria->telefono)
          <p class="mb-1"><i class="fas fa-phone"></i> {{ $inmobiliaria->telefono }}</p>
        @endif
        @if($inmobiliaria->email)
          <p class="mb-1"><i class="fas fa-envelope"></i> {{ $inmobiliaria->email }}</p>
        @endif
        @if(!empty($zonas))
          <p class="mb-1"><i class="fas fa-map-marked-alt me-1"></i>
            @foreach($zonas as $zona)
              <span class="badge" style="background: rgba(255,255,255,.18); color:#fff; font-weight:500;">{{ $zona }}</span>
            @endforeach
          </p>
        @endif
        <p class="mb-1"><span class="badge bg-light text-dark"><i class="fas fa-home"></i> {{ $inmuebles->total() }} {{ Str::plural('propiedad', $inmuebles->total()) }} {{ $preview ? 'en total' : 'publicada(s)' }}</span></p>
        @if($inmobiliaria->mostrar_redes)
          <p class="mb-1">
            @if($inmobiliaria->facebook)<a href="{{ $inmobiliaria->facebook }}" target="_blank" class="text-white me-2" title="Facebook"><i class="fab fa-facebook fa-lg"></i></a>@endif
            @if($inmobiliaria->instagram)<a href="{{ $inmobiliaria->instagram }}" target="_blank" class="text-white me-2" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>@endif
            @if($inmobiliaria->sitio_web)<a href="{{ $inmobiliaria->sitio_web }}" target="_blank" class="text-white" title="Sitio web"><i class="fas fa-globe fa-lg"></i></a>@endif
          </p>
        @endif
        <div class="mt-2">
          @if($inmobiliaria->whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inmobiliaria->whatsapp) }}" target="_blank" class="btn btn-success btn-sm">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          @endif
          <a href="{{ route('contacto', ['inmobiliaria' => $inmobiliaria->id]) }}" class="btn btn-light btn-sm">Enviar consulta</a>
        </div>
        <div class="mt-3">
          @include('partials.share', [
            'shareUrl' => route('inmobiliarias.show', $inmobiliaria->slug),
            'shareTitulo' => $inmobiliaria->nombre_comercial . ' en ' . config('app.name'),
            'dark' => true,
          ])
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container mt-4 mb-5">
  @if($inmobiliaria->descripcion)
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5>Sobre nosotros</h5>
        <p class="mb-0">{{ $inmobiliaria->descripcion }}</p>
      </div>
    </div>
  @endif

  @if($tieneMapa)
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="mb-2"><i class="fas fa-map-marked-alt me-1"></i> Mapa: propiedades y oficina</h5>
        <div id="mapa-perfil" style="height: 340px; border-radius: 10px;"></div>
        <small class="text-muted d-block mt-2">
          <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $inmobiliaria->color_tema ?: '#6366f1' }};margin-right:4px;vertical-align:middle;"></span> Propiedad
          @if($oficinaCoords)
            &nbsp;&middot;&nbsp; <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#101828;margin-right:4px;vertical-align:middle;"></span> Oficina
          @endif
        </small>
      </div>
    </div>
  @endif

  @if($destacados->count())
    <h3 class="mb-3">Propiedades destacadas</h3>
    <div class="row mb-4">
      @foreach($destacados as $inmueble)
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm property-card h-100">
            @if(!empty($inmueble->fotos))
              <div id="carouselDest{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  @foreach(($inmueble->fotos ?? []) as $index => $foto)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                      <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
            <div class="card-body">
              @if($preview && !$inmueble->publicado)
                <span class="badge bg-warning text-dark mb-1">Borrador (no público)</span>
              @endif
              <h5 class="card-title">
                @if($preview && !$inmueble->publicado)
                  <a href="{{ route('inmuebles.edit', $inmueble) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a>
                @else
                  <a href="{{ route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a>
                @endif
              </h5>
              <p class="card-text">{{ $inmueble->precio_formateado }} &bull; {{ $inmueble->habitaciones }} Hab. &bull; {{ $inmueble->banos }} Baños &bull; {{ $inmueble->superficie_cubierta }} m²</p>
              <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">Ver Detalles</button>
                @if($tieneMapa)
                  <button type="button" class="btn btn-outline-secondary btn-sm btn-ver-mapa" data-id="{{ $inmueble->id }}"><i class="fas fa-map-marker-alt me-1"></i>En el mapa</button>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <h3 class="mb-3">Todas las propiedades</h3>
  <div class="row">
    @forelse($inmuebles as $inmueble)
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm property-card h-100">
          @if(!empty($inmueble->fotos))
            <div id="carousel{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                @foreach(($inmueble->fotos ?? []) as $index => $foto)
                  <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                  </div>
                @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          @endif
          <div class="card-body">
            @if($preview && !$inmueble->publicado)
              <span class="badge bg-warning text-dark mb-1">Borrador (no público)</span>
            @endif
            <h5 class="card-title">
              @if($preview && !$inmueble->publicado)
                <a href="{{ route('inmuebles.edit', $inmueble) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a>
              @else
                <a href="{{ route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a>
              @endif
            </h5>
            <p class="card-text">{{ ucfirst($inmueble->tipo_operacion) }} &bull; {{ $inmueble->precio_formateado }} &bull; {{ $inmueble->habitaciones }} Hab. &bull; {{ $inmueble->banos }} Baños &bull; {{ $inmueble->superficie_cubierta }} m²</p>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">Ver Detalles</button>
          </div>
        </div>
      </div>

    @empty
      <div class="col-12">
        <p class="text-muted">Esta inmobiliaria todavía no tiene propiedades publicadas.</p>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center">
    {{ $inmuebles->links() }}
  </div>

  <a href="{{ route('inmobiliarias.index') }}" class="btn btn-outline-secondary mt-3">&larr; Volver a inmobiliarias</a>

  {{-- Modales de detalle: uno solo por propiedad (destacadas + página actual), sin ids duplicados.
       modal-dialog-scrollable => el body hace scroll y header/footer quedan siempre visibles. --}}
  @foreach($destacados->merge($inmuebles->getCollection())->unique('id') as $inmueble)
    <div class="modal fade perfil-modal" id="propertyModal{{ $inmueble->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header" style="background: {{ $inmobiliaria->color_tema ?: '#0d6efd' }}; color: #fff; border: none;">
            <h5 class="modal-title fw-bold">{{ $inmueble->titulo }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                  <span style="color: {{ $inmobiliaria->color_tema ?: '#0d6efd' }}; font-weight: 800; font-size: 1.3rem;">{{ $inmueble->precio_formateado }}</span>
                  <span class="badge text-bg-light border">{{ ucfirst($inmueble->tipo_operacion) }}</span>
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
                </ul>
                @include('inmuebles.partials.proximidades', ['inmueble' => $inmueble])
                @if($inmueble->piscina || $inmueble->jardin || $inmueble->garaje || $inmueble->ascensor)
                  <div class="mb-3">
                    @if($inmueble->piscina)<span class="badge text-bg-light border me-1"><i class="fas fa-swimmer me-1"></i>Piscina</span>@endif
                    @if($inmueble->jardin)<span class="badge text-bg-light border me-1"><i class="fas fa-tree me-1"></i>Jardín</span>@endif
                    @if($inmueble->garaje)<span class="badge text-bg-light border me-1"><i class="fas fa-warehouse me-1"></i>Garaje</span>@endif
                    @if($inmueble->ascensor)<span class="badge text-bg-light border me-1"><i class="fas fa-elevator me-1"></i>Ascensor</span>@endif
                  </div>
                @endif
                <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt detalle-ico me-2"></i>{{ $inmueble->direccion }}, {{ $inmueble->ciudad }}, {{ $inmueble->estado }}</p>
                @if($inmueble->descripcion)
                  <hr class="my-2">
                  <p class="small mb-0" style="white-space: pre-line;">{{ $inmueble->descripcion }}</p>
                @endif
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            <a href="{{ route('contacto', ['property' => $inmueble->id, 'inmobiliaria' => $inmobiliaria->id]) }}" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: {{ $inmobiliaria->color_tema ?: '#0d6efd' }}; border-color: transparent;">Me interesa</a>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} {{ $inmobiliaria->nombre_comercial }} | Todos los derechos reservados</p>
    @if($inmobiliaria->email)
      <p>Contacto: <a href="mailto:{{ $inmobiliaria->email }}" class="text-white">{{ $inmobiliaria->email }}</a></p>
    @endif
    <p><small class="text-muted">Sitio potenciado por {{ config('app.name') }}</small></p>
  </div>
</footer>
@stop

@section('js')
@if($tieneMapa)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var div = document.getElementById('mapa-perfil');
    if (!div || typeof L === 'undefined') { return; }
    var brand = @json($brand);
    var props = @json($markers);
    var map = L.map('mapa-perfil', { scrollWheelZoom: false });
    var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });
    var sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      maxZoom: 19, maxNativeZoom: 18, attribution: 'Tiles &copy; Esri'
    });
    L.control.layers({ 'Callejera (OSM)': osm, 'Satelital (Esri)': sat }, null, { position: 'topright' }).addTo(map);
    osm.addTo(map);
    var layer = L.featureGroup().addTo(map);
    var markersById = {};
    var pin = function (color) {
      return L.divIcon({
        className: '',
        html: '<div style="width:16px;height:16px;border-radius:50%;background:' + color + ';border:3px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.45);"></div>',
        iconSize: [16, 16], iconAnchor: [8, 8]
      });
    };
    props.forEach(function (p) {
      var html = '<div style="min-width:200px;max-width:240px;">' +
        (p.foto ? '<img src="' + p.foto + '" style="width:100%;height:110px;object-fit:cover;border-radius:6px;margin-bottom:6px;">' : '') +
        '<div class="fw-bold mb-1">' + p.titulo + '</div>' +
        '<div class="fw-bold mb-1" style="color:' + brand + ';">' + p.precio + '</div>' +
        (p.ciudad ? '<div class="small text-muted mb-1">' + p.ciudad + '</div>' : '') +
        '<a href="' + p.url + '" class="btn btn-sm w-100" style="background:' + brand + ';color:#fff;">Ver propiedad</a>' +
        '</div>';
      markersById[p.id] = L.marker([p.lat, p.lng], { icon: pin(brand) }).addTo(layer).bindPopup(html);
    });
    @if($oficinaCoords)
    L.marker([{{ (float) $inmobiliaria->lat }}, {{ (float) $inmobiliaria->lng }}], { icon: pin('#101828') }).addTo(layer)
      .bindPopup(@json($oficinaPopup));
    @endif
    if (layer.getLayers().length > 0) {
      map.fitBounds(layer.getBounds().pad(0.15));
    } else {
      map.setView([-38.5, -64.5], 5);
    }
    setTimeout(function () { map.invalidateSize(); }, 250);

    // Botón "En el mapa" de cada tarjeta: scrollea al mapa, hace zoom y abre el popup del marcador
    document.querySelectorAll('.btn-ver-mapa').forEach(function (b) {
      b.addEventListener('click', function () {
        var m = markersById[b.getAttribute('data-id')];
        if (!m) { return; }
        document.getElementById('mapa-perfil').scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () {
          map.invalidateSize();
          map.setView(m.getLatLng(), 17);
          m.openPopup();
        }, 400);
      });
    });
});
</script>
@endif
@stop
