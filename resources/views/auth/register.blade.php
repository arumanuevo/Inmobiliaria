@extends('layouts.landing')

@section('title', 'Registrá tu inmobiliaria')

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
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title mb-0">Creá la cuenta de tu inmobiliaria</h3>
        </div>
        <div class="card-body">
          <p class="text-muted">
            Registrate gratis con el plan <strong>Gratis</strong> y empezá a publicar tus propiedades en el portal.
            Tu perfil queda <strong>pendiente de aprobación</strong> hasta que el administrador lo active.
          </p>

          <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group mb-3">
              <label for="name">Tu nombre (responsable)</label>
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                     name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label for="nombre_comercial">Nombre comercial de la inmobiliaria</label>
              <input id="nombre_comercial" type="text" class="form-control @error('nombre_comercial') is-invalid @enderror"
                     name="nombre_comercial" value="{{ old('nombre_comercial') }}" required>
              @error('nombre_comercial')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label for="email">Correo electrónico</label>
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}" required autocomplete="email">
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
              <div class="col-md-6 form-group mb-3">
                <label for="telefono">Teléfono</label>
                <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror"
                       name="telefono" value="{{ old('telefono') }}">
                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 form-group mb-3">
                <label for="whatsapp">WhatsApp</label>
                <input id="whatsapp" type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                       name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Ej: 5492364123456">
                @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group mb-3">
                <label for="password">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 form-group mb-3">
                <label for="password-confirm">Confirmar contraseña</label>
                <input id="password-confirm" type="password" class="form-control"
                       name="password_confirmation" required autocomplete="new-password">
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Registrarme</button>
            </div>
          </form>
          <hr>
          <p class="mb-0">¿Ya tenés cuenta? <a href="{{ route('login') }}">Ingresá acá</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
  </div>
</footer>
@stop
