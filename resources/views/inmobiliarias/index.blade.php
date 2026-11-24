@extends('layouts.landing')

@section('title', 'Inmobiliarias verificadas | ' . config('app.name'))
@section('meta')
<meta name="description" content="Directorio de inmobiliarias verificadas y dueños directos que publican en {{ config('app.name') }}. Buscá por nombre, ciudad o zona.">
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  body { padding-top: 56px; background: linear-gradient(180deg, #f6f7fb 0%, #eceff7 100%); min-height: 100vh; }
  .dir-hero {
    background: linear-gradient(135deg, #101828 0%, #1d2939 55%, #312e81 100%);
    color: #fff;
    padding: 46px 0 96px;
  }
  .dir-hero h1 { font-weight: 800; }
  .dir-hero .lead { color: #c7d0e0; }
  .dir-search {
    display: flex; align-items: center; gap: 8px;
    background: #fff; border-radius: 999px; padding: 6px 6px 6px 18px;
    max-width: 560px; margin: 0 auto;
    box-shadow: 0 18px 40px rgba(2, 6, 23, .35);
  }
  .dir-search > i { color: #98a2b3; }
  .dir-search input {
    border: none; outline: none; flex: 1; font-size: .95rem; background: transparent; color: #1f2937; min-width: 0;
  }
  .dir-search .btn-buscar {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    border: none; color: #fff; font-weight: 700; border-radius: 999px; padding: .55rem 1.6rem;
    white-space: nowrap; transition: filter .18s ease;
  }
  .dir-search .btn-buscar:hover { filter: brightness(1.12); color: #fff; }
  .dir-verif {
    display: inline-flex; align-items: center; gap: 7px; cursor: pointer; user-select: none;
    font-size: .83rem; font-weight: 600; color: #e4e7ec; white-space: nowrap; margin: 0;
    background: rgba(255,255,255,.10); border: 1.5px solid rgba(255,255,255,.35);
    border-radius: 999px; padding: .4rem .95rem; transition: all .15s ease;
  }
  .dir-verif input { accent-color: #a5b4fc; margin: 0; }
  .dir-verif:hover { background: rgba(255,255,255,.22); }
  .dir-verif.activo {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    border-color: transparent; color: #fff;
  }
  .dir-verif.activo input { accent-color: #fff; }
  .dir-panel {
    border: none;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 24px 60px rgba(16, 24, 40, .14);
    padding: 24px;
  }
  .dir-card {
    border: 1px solid #eef0f6;
    border-radius: 18px;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .dir-card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(16, 24, 40, .16) !important; }
  .dir-accent { height: 5px; }
  .dir-logo { max-height: 70px; max-width: 150px; object-fit: contain; border-radius: 10px; background: #fff; }
  .dir-inicial {
    width: 72px; height: 72px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.8rem; font-weight: 700;
  }
  .dir-stat {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 999px;
    padding: 4px 12px; font-size: 12.5px; font-weight: 600; color: #344054; margin: 2px 3px;
  }
  .dir-stat i { font-size: 11px; }
  .btn-dir {
    border: 1.5px solid var(--dir-color, #6366f1);
    color: var(--dir-color, #6366f1);
    border-radius: 999px; font-weight: 600; padding: .5rem 1.4rem;
    transition: all .18s ease;
  }
  .btn-dir:hover { background: var(--dir-color, #6366f1); border-color: var(--dir-color, #6366f1); color: #fff; }
  .pagination { justify-content: center; }
  .pagination .page-link { border-radius: 10px; margin: 0 2px; border-color: #e2e8f0; color: #4f46e5; }
  .pagination .page-item.active .page-link { background: linear-gradient(135deg, #6366f1, #8b5cf6); border-color: transparent; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; margin-top: 40px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark portal-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link text-success fw-bold" href="{{ route('register', ['tipo' => 'particular']) }}"><i class="fas fa-plus-circle me-1"></i> Publicar gratis</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="dir-hero">
  <div class="container text-center">
    <h1 class="h2 mb-2 text-white">Inmobiliarias del portal</h1>
    <p class="lead mb-4">Inmobiliarias verificadas y dueños directos que publican acá. Buscá por nombre, ciudad o zona.</p>
    <form method="GET" action="{{ route('inmobiliarias.index') }}">
      <div class="dir-search">
        <i class="fas fa-search"></i>
        <input type="text" name="q" placeholder="Buscar por nombre, ciudad, provincia o zona..." value="{{ $q ?? '' }}" aria-label="Buscar inmobiliarias">
        <button class="btn btn-buscar" type="submit">Buscar</button>
      </div>
      <div class="text-center mt-3">
        <label class="dir-verif {{ ($verificada ?? false) ? 'activo' : '' }}" title="Mostrar solo inmobiliarias con CUIT y matrícula verificados por el portal">
          <input type="checkbox" name="verificada" value="1" onchange="this.form.submit()" {{ ($verificada ?? false) ? 'checked' : '' }}>
          <i class="fas fa-shield-alt"></i> Solo verificadas
        </label>
      </div>
    </form>
  </div>
</div>

<div class="container pb-5" style="margin-top: -52px; position: relative; z-index: 2;">
  <div class="card dir-panel">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h2 class="h5 fw-bold mb-0 text-dark">
      {{ $inmobiliarias->total() }} {{ Str::plural('inmobiliaria', $inmobiliarias->total()) }} encontrada(s)
    </h2>
    @if(!empty($q) || ($verificada ?? false))
      <span class="text-muted small">
        Resultados
        @if(!empty($q)) para “<strong>{{ $q }}</strong>” @endif
        @if($verificada ?? false) <span class="badge text-bg-success ms-1"><i class="fas fa-shield-alt"></i> solo verificadas</span> @endif
        <a href="{{ route('inmobiliarias.index') }}" class="ms-1"><i class="fas fa-times-circle"></i> limpiar</a>
      </span>
    @endif
  </div>

  <div class="row">
    @forelse($inmobiliarias as $inmobiliaria)
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card dir-card shadow-sm h-100">
          <div class="dir-accent" style="background: {{ $inmobiliaria->color_tema ?: '#6366f1' }};"></div>
          <div class="card-body text-center pt-4">
            @if($inmobiliaria->logo)
              <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" class="dir-logo">
            @else
              <div class="dir-inicial" style="background: {{ $inmobiliaria->color_tema ?: '#6366f1' }};">
                {{ strtoupper(substr($inmobiliaria->nombre_comercial, 0, 1)) }}
              </div>
            @endif
            <h5 class="mt-3 mb-1 fw-bold">
              {{ $inmobiliaria->nombre_comercial }}
              @if($inmobiliaria->verificada)
                <span class="badge text-bg-success ms-1" title="CUIT y matrícula verificados por el portal"><i class="fas fa-shield-alt"></i> Verificada</span>
              @endif
              @if($inmobiliaria->usuario?->hasRole('particular'))
                <span class="badge rounded-pill text-bg-secondary ms-1">Particular</span>
              @endif
            </h5>
            <p class="text-muted small mb-1">
              <i class="fas fa-map-marker-alt me-1" style="color: {{ $inmobiliaria->color_tema ?: '#6366f1' }};"></i>
              {{ trim($inmobiliaria->ciudad . ', ' . $inmobiliaria->provincia, ', ') ?: 'Sin ubicación' }}
            </p>
            @if($inmobiliaria->zonas_cobertura)
              <p class="text-muted small mb-2"><i class="fas fa-location-arrow me-1"></i> Atiende: {{ $inmobiliaria->zonas_cobertura }}</p>
            @endif
            <div class="mb-3">
              <span class="dir-stat"><i class="fas fa-home" style="color: {{ $inmobiliaria->color_tema ?: '#6366f1' }};"></i>{{ $inmobiliaria->inmuebles_publicados_count }} {{ Str::plural('propiedad', $inmobiliaria->inmuebles_publicados_count) }}</span>
              @if($inmobiliaria->mostrar_operaciones && $inmobiliaria->operaciones_count > 0)
                <span class="dir-stat"><i class="fas fa-check-circle text-success"></i>{{ $inmobiliaria->operaciones_count }} {{ Str::plural('operación', $inmobiliaria->operaciones_count) }}</span>
              @endif
            </div>
            <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" class="btn btn-dir btn-sm" style="--dir-color: {{ $inmobiliaria->color_tema ?: '#6366f1' }};">
              Ver perfil <i class="fas fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 18px;">
          <div class="card-body">
            <i class="fas fa-search fa-2x text-muted mb-3"></i>
            <p class="text-muted mb-1">No encontramos inmobiliarias con ese criterio.</p>
            @if(!empty($q) || ($verificada ?? false))
              <a href="{{ route('inmobiliarias.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2">Ver todas</a>
            @else
              <p class="text-muted small mb-0">Pronto habrá inmobiliarias verificadas acá. ¿Tenés una? <a href="{{ route('sumate') }}">Sumate gratis</a>.</p>
            @endif
          </div>
        </div>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center">
    {{ $inmobiliarias->links() }}
  </div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
  </div>
</footer>
@stop
