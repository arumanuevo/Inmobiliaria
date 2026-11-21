@php
  // Helper JSON para bloques <script type="application/ld+json"> y popups: sin depender de Illuminate\Support\Json
  $jsonLd = fn($v) => json_encode($v, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
@extends('layouts.landing')

@section('title', 'Propiedades en ' . $operacion . ' en ' . ucfirst($ciudadNombre) . ' | ' . config('app.name'))

@section('meta')
<meta name="description" content="Encontrá propiedades en {{ $operacion }} en {{ ucfirst($ciudadNombre) }}: casas, departamentos, terrenos y más publicadas por inmobiliarias verificadas de la zona.">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Propiedades en {{ $operacion }} en {{ ucfirst($ciudadNombre) }}",
  "numberOfItems": {{ $propiedades->total() }},
  "itemListElement": [
    @foreach($propiedades as $i => $p)
    {
      "@type": "ListItem",
      "position": {{ $i + 1 }},
      "url": {!! $jsonLd($p->inmobiliaria ? route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) : '') !!}
    }@if(!$loop->last),@endif
    @endforeach
  ]
}
</script>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  body { padding-top: 56px; }
  .property-card img { height: 200px; object-fit: cover; width: 100%; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark portal-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo {{ config('app.name') }}" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <div class="collapse navbar-collapse">
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

<div class="container mt-5 mb-5">
  <h1 class="h3 mb-1">Propiedades en {{ $operacion }} en {{ ucfirst($ciudadNombre) }}</h1>
  <p class="text-muted mb-4">{{ $propiedades->total() }} {{ Str::plural('resultado', $propiedades->total()) }} publicados por inmobiliarias verificadas y dueños directos.</p>

  <div class="row">
    @forelse($propiedades as $p)
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm property-card h-100">
          @if(!empty($p->fotos))
            <img src="{{ asset('images/fotosinmuebles/' . $p->fotos[0]) }}" alt="{{ $p->titulo }}">
          @else
            <div class="bg-secondary d-flex align-items-center justify-content-center text-white">Sin foto</div>
          @endif
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              @if($p->inmobiliaria?->usuario?->hasRole('particular'))
                <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span>
              @else
                <span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span>
              @endif
              <small class="text-muted">{{ ucfirst($p->tipo_operacion) }}</small>
            </div>
            <h5 class="card-title">
              <a href="{{ $p->inmobiliaria ? route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) : '#' }}" class="text-decoration-none text-dark">{{ $p->titulo }}</a>
            </h5>
            <p class="card-text text-muted small mb-1">{{ $p->inmobiliaria->nombre_comercial ?? '' }}</p>
            <p class="card-text fw-bold">{{ $p->precio_formateado }}</p>
            <p class="card-text small">{{ $p->habitaciones }} hab. · {{ $p->banos }} baños · {{ $p->superficie_cubierta }} m²{{ $p->ambientes ? ' · ' . $p->ambientes . ' amb.' : '' }}</p>
            @if($p->inmobiliaria)
              <a href="{{ route('propiedad.show', [$p->inmobiliaria->slug, $p->id]) }}" class="btn btn-primary btn-sm">Ver ficha</a>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">Todavía no hay propiedades en {{ $operacion }} en {{ ucfirst($ciudadNombre) }}. <a href="{{ route('propiedades.index') }}">Ver todas las propiedades</a></div>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center">
    {{ $propiedades->links() }}
  </div>

  <p class="mt-4">
    <a href="{{ route('propiedades.zona', ['operacion' => $operacion === 'venta' ? 'alquiler' : 'venta', 'ciudad' => Str::slug($ciudadNombre)]) }}" class="btn btn-outline-primary btn-sm">
      Ver en {{ $operacion === 'venta' ? 'alquiler' : 'venta' }} en {{ ucfirst($ciudadNombre) }}
    </a>
  </p>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
  </div>
</footer>
@stop
