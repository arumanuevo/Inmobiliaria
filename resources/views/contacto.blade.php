@extends('layouts.landing')

@php
    $inmobiliaria = request('inmobiliaria') ? \App\Models\Inmobiliaria::find(request('inmobiliaria')) : null;
    $property = request('property') ? \App\Models\Inmueble::find(request('property')) : null;
    if ($property) {
        $titulo = 'Me interesa: ' . $property->titulo;
        $subtitulo = 'Tu consulta se envía directo al anunciante (' . ($inmobiliaria->nombre_comercial ?? 'particular') . '), sin intermediarios.';
        $tituloForm = 'Enviar consulta por esta propiedad';
    } elseif ($inmobiliaria) {
        $titulo = 'Contactar a ' . $inmobiliaria->nombre_comercial;
        $subtitulo = 'Tu mensaje llega directo al email de la inmobiliaria.';
        $tituloForm = 'Enviar mensaje a ' . $inmobiliaria->nombre_comercial;
    } else {
        $titulo = 'Contacto';
        $subtitulo = 'Dudas sobre el portal, tu cuenta o cómo publicar: escribinos y te respondemos a la brevedad.';
        $tituloForm = 'Escribinos';
    }
@endphp

@section('title', $titulo . ' | ' . config('app.name'))
@section('meta')
<meta name="description" content="Contactá a {{ config('app.name') }}: consultas por propiedades (llegan directo a la inmobiliaria) o dudas sobre el portal.">
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  body { padding-top: 56px; background: linear-gradient(180deg, #f6f7fb 0%, #eceff7 100%); min-height: 100vh; }
  .contact-hero {
    background: linear-gradient(135deg, #101828 0%, #1d2939 55%, #312e81 100%);
    color: #fff;
    padding: 46px 0 100px;
  }
  .contact-hero h1 { font-weight: 800; }
  .contact-hero .lead { color: #c7d0e0; }
  .contact-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(16, 24, 40, .16);
  }
  .contact-card .card-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    color: #fff;
    border: none;
    padding: 20px 28px;
  }
  .contact-card .card-body { padding: 28px; background: #fff; }
  .form-label { font-weight: 600; font-size: .9rem; color: #344054; }
  .form-control, .form-select {
    border-radius: 12px;
    padding: .7rem 1rem;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    font-size: .95rem;
  }
  .form-control:focus, .form-select:focus {
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, .15);
  }
  .input-icon { position: relative; }
  .input-icon > i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #98a2b3;
    pointer-events: none;
  }
  .input-icon textarea ~ i { top: 20px; transform: none; }
  .input-icon input, .input-icon select, .input-icon textarea { padding-left: 42px; }
  .btn-enviar {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    border: none;
    color: #fff;
    font-weight: 700;
    border-radius: 999px;
    padding: .75rem 2.4rem;
    box-shadow: 0 10px 24px rgba(124, 92, 246, .4);
    transition: filter .18s ease, transform .18s ease;
  }
  .btn-enviar:hover { filter: brightness(1.12); transform: translateY(-1px); color: #fff; }
  .btn-cancelar { border-radius: 999px; padding: .75rem 1.6rem; font-weight: 600; }
  .info-card { border: none; border-radius: 20px; box-shadow: 0 14px 36px rgba(16, 24, 40, .10); background: #fff; }
  .info-card .card-body { padding: 24px; }
  .info-card li { margin-bottom: .65rem; color: #475467; }
  .prop-card { border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; background: #fff; }
  .prop-card img { height: 150px; width: 100%; object-fit: cover; }
  .destinatario-banner {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    color: #3730a3;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: .92rem;
  }
  .cta-card { background: linear-gradient(135deg, #101828 0%, #312e81 100%); color: #fff; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; margin-top: 48px; }
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
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link text-success fw-bold" href="{{ route('register', ['tipo' => 'particular']) }}"><i class="fas fa-plus-circle me-1"></i> Publicar gratis</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="contact-hero">
  <div class="container">
    <h1 class="h2 mb-2">{{ $titulo }}</h1>
    <p class="lead mb-0">{{ $subtitulo }}</p>
  </div>
</div>

<div class="container pb-5" style="margin-top: -68px; position: relative; z-index: 2;">
  <div class="row g-4 justify-content-center">
    <div class="col-lg-7">
      <div class="card contact-card">
        <div class="card-header">
          <h2 class="h5 fw-bold mb-0"><i class="far fa-paper-plane me-2"></i>{{ $tituloForm }}</h2>
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">
              <strong>Revisá el formulario:</strong>
              <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
              </ul>
            </div>
          @endif

          @if($inmobiliaria)
            <div class="destinatario-banner mb-3">
              <i class="fas fa-paper-plane me-1"></i> Tu consulta se enviará directamente a
              <strong>{{ $inmobiliaria->nombre_comercial }}</strong>@if($inmobiliaria->email) ({{ $inmobiliaria->email }})@endif
            </div>
          @endif

          @if($property)
            <div class="prop-card mb-3">
              @if(!empty($property->fotos))
                <img src="{{ asset('images/fotosinmuebles/' . $property->fotos[0]) }}" alt="{{ $property->titulo }}">
              @endif
              <div class="p-3">
                <div class="fw-bold">{{ $property->titulo }}</div>
                <div class="text-muted small">{{ $property->precio_formateado }} &middot; {{ ucfirst($property->tipo_operacion) }} &middot; {{ $property->ciudad }}</div>
              </div>
            </div>
          @endif

          <form action="{{ route('contacto.store') }}" method="POST">
            @csrf
            <input type="hidden" name="inmobiliaria_id" value="{{ $inmobiliaria->id ?? '' }}">
            <input type="hidden" name="inmueble_id" value="{{ $property->id ?? '' }}">
            @if($property || $inmobiliaria)
              <input type="hidden" name="motivo" value="Consulta por propiedad">
            @endif

            @if(!$property && !$inmobiliaria)
            <div class="mb-3">
              <label class="form-label" for="motivo">Motivo del mensaje</label>
              <div class="input-icon">
                <i class="fas fa-question-circle"></i>
                <select name="motivo" id="motivo" class="form-select">
                  <option value="Duda sobre el portal" {{ old('motivo') === 'Duda sobre el portal' ? 'selected' : '' }}>Tengo una duda sobre el portal</option>
                  <option value="Ayuda con mi cuenta" {{ old('motivo') === 'Ayuda con mi cuenta' ? 'selected' : '' }}>Necesito ayuda con mi cuenta o mis publicaciones</option>
                  <option value="Quiero ser inmobiliaria" {{ old('motivo') === 'Quiero ser inmobiliaria' ? 'selected' : '' }}>Quiero ser inmobiliaria (info de planes)</option>
                  <option value="Prensa o publicidad" {{ old('motivo') === 'Prensa o publicidad' ? 'selected' : '' }}>Prensa / publicidad</option>
                  <option value="Otro" {{ old('motivo') === 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
              </div>
            </div>
            @endif

            <div class="mb-3">
              <label class="form-label" for="nombre">Nombre</label>
              <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Tu nombre" value="{{ old('nombre') }}" required>
              </div>
              @error('nombre')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="email">Email</label>
                <div class="input-icon">
                  <i class="fas fa-envelope"></i>
                  <input type="email" name="email" id="email" class="form-control" placeholder="tu@email.com" value="{{ old('email') }}" required>
                </div>
                @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="telefono">Teléfono / WhatsApp</label>
                <div class="input-icon">
                  <i class="fas fa-phone"></i>
                  <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Opcional" value="{{ old('telefono') }}">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="mensaje">Mensaje</label>
              <div class="input-icon">
                <i class="fas fa-comment-dots"></i>
                <textarea name="mensaje" id="mensaje" class="form-control" rows="5" placeholder="Escribí tu mensaje" required>@if($property)Me interesa la propiedad "{{ $property->titulo }}" ({{ $property->precio_formateado }}, {{ $property->ciudad }}).

@endif{{ old('mensaje') }}</textarea>
              </div>
              @error('mensaje')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            @if(config('services.recaptcha.site_key'))
              <div class="mb-3 d-flex justify-content-center">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              </div>
            @endif

            <div class="text-center">
              <button type="submit" class="btn btn-enviar"><i class="far fa-paper-plane me-1"></i> Enviar mensaje</button>
              <button type="button" class="btn btn-outline-secondary btn-cancelar ms-2" onclick="history.back()">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card info-card mb-3">
        <div class="card-body">
          <h3 class="h6 fw-bold mb-3"><i class="fas fa-info-circle me-1" style="color:#6366f1;"></i> ¿Para qué es este formulario?</h3>
          <ul class="list-unstyled mb-3 small">
            <li><i class="fas fa-home me-2" style="color:#6366f1;"></i><strong>¿Te interesa una propiedad?</strong> Usá el botón "Me interesa" de su ficha: el mensaje llega directo al email del anunciante (inmobiliaria o particular).</li>
            <li><i class="fas fa-life-ring me-2" style="color:#6366f1;"></i><strong>¿Duda sobre el portal o tu cuenta?</strong> Escribinos acá y te responde el equipo de {{ config('app.name') }}.</li>
            <li><i class="fas fa-store me-2" style="color:#6366f1;"></i><strong>¿Sos inmobiliaria?</strong> Mirá los planes y sumate en <a href="{{ route('sumate') }}">/sumate</a>.</li>
          </ul>
          <hr>
          <p class="small text-muted mb-0"><i class="fas fa-envelope me-1"></i> También podés escribir directo a <a href="mailto:{{ $mailContacto }}">{{ $mailContacto }}</a></p>
        </div>
      </div>

      @if(!$property && !$inmobiliaria)
      <div class="card info-card cta-card">
        <div class="card-body text-center">
          <h3 class="h6 fw-bold text-white"><i class="fas fa-rocket me-1"></i> ¿Tenés una inmobiliaria?</h3>
          <p class="small mb-3" style="color:#c7d0e0;">Creá tu vidriera gratis en minutos y empezá a recibir consultas directas.</p>
          <a href="{{ route('sumate') }}" class="btn btn-light btn-sm fw-bold rounded-pill px-4">Sumate gratis</a>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
  </div>
</footer>
@stop

@section('js')
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@stop
