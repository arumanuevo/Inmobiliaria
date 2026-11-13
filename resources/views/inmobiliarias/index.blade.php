@extends('layouts.landing')

@section('title', 'Inmobiliarias')

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<style>
  body { padding-top: 56px; }
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      Portal Inmobiliario
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
  <div class="text-center mb-4">
    <h1>Nuestras Inmobiliarias</h1>
    <p class="text-muted">Conocé las inmobiliarias que publican en nuestro portal y explorá sus propiedades.</p>
  </div>

  <div class="row">
    @forelse($inmobiliarias as $inmobiliaria)
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            @if($inmobiliaria->logo)
              <img src="{{ asset($inmobiliaria->logo) }}" alt="{{ $inmobiliaria->nombre_comercial }}" style="max-height: 80px; max-width: 100%;">
            @else
              <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                {{ strtoupper(substr($inmobiliaria->nombre_comercial, 0, 1)) }}
              </div>
            @endif
            <h5 class="mt-3 mb-1">{{ $inmobiliaria->nombre_comercial }}</h5>
            <p class="text-muted mb-1">{{ trim($inmobiliaria->ciudad . ', ' . $inmobiliaria->provincia, ', ') ?: 'Sin ubicación' }}</p>
            <p class="text-muted mb-3">{{ $inmobiliaria->inmuebles_publicados_count }} {{ Str::plural('propiedad', $inmobiliaria->inmuebles_publicados_count) }} publicada(s)</p>
            <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" class="btn btn-primary btn-sm">Ver perfil</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <p class="text-muted text-center">Todavía no hay inmobiliarias registradas.</p>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center">
    {{ $inmobiliarias->links() }}
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; 2024 GerTech-ArumaSoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
  </div>
</footer>
@stop
