@extends('layouts.landing')

@section('title', 'Ingresar')

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
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title mb-0">Ingresá a tu cuenta</h3>
        </div>
        <div class="card-body">
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-3">
              <label for="email">Correo electrónico</label>
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label for="password">Contraseña</label>
              <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                     name="password" required autocomplete="current-password">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3 form-check">
              <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
          </form>

          @if(config('services.google.client_id'))
            <hr>
            <div class="text-center">
              <a href="{{ route('login.google') }}" class="btn btn-outline-danger btn-block w-100">
                Continuar con Google
              </a>
            </div>
          @endif

          <hr>
          <p class="mb-1">¿Sos una inmobiliaria y no tenés cuenta? <a href="{{ route('register') }}">Registrate acá</a></p>
          @if (Route::has('password.request'))
            <p class="mb-0"><a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
          @endif
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
