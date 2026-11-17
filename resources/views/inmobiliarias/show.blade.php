@extends('layouts.landing')

@section('title', $inmobiliaria->nombre_comercial)

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
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
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a></li></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

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
        @if($inmobiliaria->operaciones()->count() > 0)
          <p class="mb-1"><span class="badge bg-light text-dark"><i class="fas fa-check-circle"></i> {{ $inmobiliaria->operaciones()->count() }} {{ Str::plural('operación', $inmobiliaria->operaciones()->count()) }} concretada(s)</span></p>
        @endif
        <p class="mb-1">{{ trim(($inmobiliaria->direccion ? $inmobiliaria->direccion . ', ' : '') . $inmobiliaria->ciudad . ', ' . $inmobiliaria->provincia, ', ') }}</p>
        @if($inmobiliaria->telefono)
          <p class="mb-1"><i class="fas fa-phone"></i> {{ $inmobiliaria->telefono }}</p>
        @endif
        @if($inmobiliaria->email)
          <p class="mb-1"><i class="fas fa-envelope"></i> {{ $inmobiliaria->email }}</p>
        @endif
        <div class="mt-2">
          @if($inmobiliaria->whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inmobiliaria->whatsapp) }}" target="_blank" class="btn btn-success btn-sm">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          @endif
          <a href="{{ route('contacto', ['inmobiliaria' => $inmobiliaria->id]) }}" class="btn btn-light btn-sm">Enviar consulta</a>
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

  @if($destacados->count())
    <h3 class="mb-3">Propiedades destacadas</h3>
    <div class="row mb-4">
      @foreach($destacados as $inmueble)
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm property-card h-100">
            @if(!empty($inmueble->fotos))
              <div id="carouselDest{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  @foreach($inmueble->fotos as $index => $foto)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                      <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
            <div class="card-body">
              <h5 class="card-title"><a href="{{ route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a></h5>
              <p class="card-text">{{ $inmueble->precio_formateado }} &bull; {{ $inmueble->habitaciones }} Hab. &bull; {{ $inmueble->banos }} Baños &bull; {{ $inmueble->superficie_cubierta }} m²</p>
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">Ver Detalles</button>
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
                @foreach($inmueble->fotos as $index => $foto)
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
            <h5 class="card-title"><a href="{{ route('propiedad.show', [$inmobiliaria->slug, $inmueble->id]) }}" class="text-decoration-none text-dark">{{ $inmueble->titulo }}</a></h5>
            <p class="card-text">{{ ucfirst($inmueble->tipo_operacion) }} &bull; {{ $inmueble->precio_formateado }} &bull; {{ $inmueble->habitaciones }} Hab. &bull; {{ $inmueble->banos }} Baños &bull; {{ $inmueble->superficie_cubierta }} m²</p>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">Ver Detalles</button>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="propertyModal{{ $inmueble->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ $inmueble->titulo }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              @if(!empty($inmueble->fotos))
                <div id="modalCarousel{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    @foreach($inmueble->fotos as $index => $foto)
                      <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="{{ $inmueble->titulo }}">
                      </div>
                    @endforeach
                  </div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>
                <hr>
              @endif
              <p><strong>Operación:</strong> {{ ucfirst($inmueble->tipo_operacion) }}</p>
              <p><strong>Precio:</strong> {{ $inmueble->precio_formateado }}</p>
              <p><strong>Habitaciones:</strong> {{ $inmueble->habitaciones }} &bull; <strong>Baños:</strong> {{ $inmueble->banos }} &bull; <strong>Superficie:</strong> {{ $inmueble->superficie_cubierta }} m²</p>
              @if($inmueble->tipo_propiedad)<p><strong>Tipo:</strong> {{ \App\Models\Inmueble::tiposPropiedad()[$inmueble->tipo_propiedad] ?? ucfirst($inmueble->tipo_propiedad) }}</p>@endif
              @if($inmueble->ambientes)<p><strong>Ambientes:</strong> {{ $inmueble->ambientes }}</p>@endif
              @if($inmueble->cocheras)<p><strong>Cocheras:</strong> {{ $inmueble->cocheras }}</p>@endif
              @if($inmueble->expensas)<p><strong>Expensas:</strong> ${{ number_format($inmueble->expensas, 2) }}</p>@endif
              @if($inmueble->orientacion)<p><strong>Orientación:</strong> {{ $inmueble->orientacion }}</p>@endif
              @if($inmueble->estado_propiedad)<p><strong>Estado:</strong> {{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$inmueble->estado_propiedad] ?? $inmueble->estado_propiedad }}</p>@endif
              <p><strong>Dirección:</strong> {{ $inmueble->direccion }}, {{ $inmueble->ciudad }}, {{ $inmueble->estado }}</p>
              <p><strong>Descripción:</strong> {{ $inmueble->descripcion }}</p>
              <p>
                {{ $inmueble->piscina ? 'Piscina · ' : '' }}{{ $inmueble->jardin ? 'Jardín · ' : '' }}{{ $inmueble->garaje ? 'Garaje · ' : '' }}{{ $inmueble->ascensor ? 'Ascensor' : '' }}
              </p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <a href="{{ route('contacto', ['property' => $inmueble->id, 'inmobiliaria' => $inmobiliaria->id]) }}" class="btn btn-primary">Me Interesa</a>
            </div>
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
