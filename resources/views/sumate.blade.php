@extends('layouts.landing')

@section('title', 'Sumá tu inmobiliaria gratis | ' . config('app.name'))

@section('meta')
<meta name="description" content="Publicá tus propiedades en {{ config('app.name') }} con tu propia página y tu marca. Gratis para empezar: las consultas llegan directo a tu WhatsApp y tu email.">
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<style>
  body { padding-top: 56px; }
  .hero-sumate {
    background: linear-gradient(135deg, #101828 0%, #1d2939 100%);
    color: #fff;
    padding: 70px 0;
    text-align: center;
  }
  .hero-sumate h1 { font-weight: 800; font-size: 2.2rem; }
  .hero-sumate p { color: #d0d5dd; font-size: 1.1rem; }
  .beneficio { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 22px; height: 100%; box-shadow: 0 1px 3px rgba(16,24,40,.06); }
  .beneficio i { font-size: 24px; color: #4f46e5; margin-bottom: 10px; }
  .beneficio h5 { font-weight: 700; }
  .beneficio p { color: #667085; font-size: 14px; margin: 0; }
  .stat-num { font-size: 2rem; font-weight: 800; color: #4f46e5; }
  .plan-card { border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; height: 100%; background: #fff; }
  .plan-card.destacado { border: 2px solid #4f46e5; }
  .footer { background-color: #343a40; color: white; text-align: center; padding: 10px 0; font-size: 10px; }
  .footer p { margin: 0; }
</style>
@stop

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo {{ config('app.name') }}" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero-sumate">
  <div class="container">
    <h1>Dale a tu inmobiliaria su propia página en internet</h1>
    <p class="mx-auto" style="max-width: 640px;">Publicá tus propiedades con tu marca, en tu propia dirección, y recibí las consultas directo en tu WhatsApp y tu email. Empezá gratis.</p>
    <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold mt-3">Crear mi cuenta gratis</a>
    <div class="mt-4 d-flex justify-content-center gap-4 flex-wrap">
      <div><div class="stat-num text-white">{{ $stats['inmobiliarias'] }}</div><small class="text-secondary">inmobiliarias</small></div>
      <div><div class="stat-num text-white">{{ $stats['propiedades'] }}</div><small class="text-secondary">propiedades publicadas</small></div>
      <div><div class="stat-num text-white">{{ $stats['operaciones'] }}</div><small class="text-secondary">operaciones concretadas</small></div>
    </div>
  </div>
</section>

<div class="container py-5">
  <h2 class="text-center fw-bold mb-4">¿Qué ganás con {{ config('app.name') }}?</h2>
  <div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-store"></i>
        <h5>Tu vidriera propia con tu marca</h5>
        <p>Tu logo, tus colores y tu nombre en tu propia dirección: <em>/inmobiliaria/tu-inmobiliaria</em>. El cliente te recuerda a vos, no a un portal.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-bullhorn"></i>
        <h5>Las consultas llegan a VOS</h5>
        <p>Cada "Me interesa" llega directo a tu WhatsApp y a tu email, con la propiedad que consultaron. Sin intermediarios ni formularios perdidos.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-chart-line"></i>
        <h5>Sabés qué propiedad funciona</h5>
        <p>Ves cuántas personas entraron a ver cada propiedad y cuántas te consultaron. Y comparás su precio con el promedio de tu zona.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fab fa-google"></i>
        <h5>Aparecés en Google en tu zona</h5>
        <p>Cada propiedad tiene su propia página indexada: cuando alguien busca "departamentos en venta en tu ciudad", puede encontrarte a vos.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-map-marked-alt"></i>
        <h5>Mapa de todas tus propiedades</h5>
        <p>Todas tus publicaciones geolocalizadas en un mapa público para que el comprador vea la zona antes de llamarte.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-shield-alt"></i>
        <h5>Perfil verificado</h5>
        <p>Tu inmobiliaria pasa por aprobación y figura en el directorio del portal con insignia de verificada: más confianza, menos estafas.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-bell"></i>
        <h5>Alertas automáticas a interesados</h5>
        <p>Quienes buscan en el portal guardan sus búsquedas y reciben un aviso por email apenas publicás algo que les interesa: más consultas entrantes sin mover un dedo.</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="beneficio">
        <i class="fas fa-hands-helping"></i>
        <h5>Te ayudamos a arrancar</h5>
        <p>Si querés, cargamos por vos tus primeras propiedades para que tu vidriera no arranque vacía. Tu portal, tu marca, desde el día uno.</p>
      </div>
    </div>
  </div>

  <h2 class="text-center fw-bold mb-4">Planes simples, sin letra chica</h2>
  <div class="row g-4 mb-5">
    @foreach($planes as $plan)
      <div class="col-md-4">
        <div class="plan-card {{ $plan->precio_mensual > 0 && $loop->iteration == $planes->count() ? 'destacado' : '' }} text-center">
          <h5 class="fw-bold">{{ $plan->nombre }}</h5>
          <div class="display-6 fw-bold my-2">@if($plan->precio_mensual > 0)${{ number_format($plan->precio_mensual, 0) }}<small class="fs-6 text-muted">/mes</small>@else Gratis @endif</div>
          <ul class="list-unstyled text-muted small">
            <li>{{ $plan->limite_inmuebles }} publicaciones</li>
            <li>{{ $plan->limite_destacados }} destacadas</li>
            <li>{{ $plan->limite_fotos }} fotos por publicación</li>
          </ul>
          <a href="{{ route('register') }}" class="btn {{ $plan->precio_mensual == 0 ? 'btn-primary' : 'btn-outline-primary' }} w-100">Empezar</a>
        </div>
      </div>
    @endforeach
  </div>

  <div class="text-center py-4">
    <h3 class="fw-bold">¿Lista para publicar?</h3>
    <p class="text-muted">Creá tu cuenta en 2 minutos. Tu perfil queda visible cuando lo aprobamos.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Crear mi cuenta gratis</a>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
  </div>
</footer>
@stop
