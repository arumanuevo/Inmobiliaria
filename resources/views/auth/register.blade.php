@extends('layouts.landing')

@section('title', 'Creá tu cuenta en ' . config('app.name'))

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
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
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
          <h3 class="card-title mb-0">Creá tu cuenta en el portal</h3>
        </div>
        <div class="card-body">
          <p class="text-muted">
            Registrate gratis con el plan <strong>Gratis</strong> y empezá a publicar.
            Si sos <strong>inmobiliaria</strong>, tu perfil queda pendiente de aprobación.
            Si sos <strong>particular</strong> (publicás vos tu propia vivienda), ya podés empezar hoy.
          </p>

          <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group mb-3">
              <label>¿Qué vas a publicar?</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_cuenta" id="tipo_inmobiliaria" value="inmobiliaria"
                       {{ old('tipo_cuenta', request('tipo')) === 'particular' ? '' : 'checked' }}>
                <label class="form-check-label" for="tipo_inmobiliaria">
                  <strong>Soy inmobiliaria</strong> — tengo una agencia y publico varias propiedades
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_cuenta" id="tipo_particular" value="particular"
                       {{ old('tipo_cuenta', request('tipo')) === 'particular' ? 'checked' : '' }}>
                <label class="form-check-label" for="tipo_particular">
                  <strong>Soy particular</strong> — quiero publicar mi propia casa o departamento
                </label>
              </div>
              @error('tipo_cuenta')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
              <label for="name">Tu nombre</label>
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                     name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3" id="campo-nombre-comercial">
              <label for="nombre_comercial">Nombre comercial de la inmobiliaria</label>
              <input id="nombre_comercial" type="text" class="form-control @error('nombre_comercial') is-invalid @enderror"
                     name="nombre_comercial" value="{{ old('nombre_comercial') }}">
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
            @if(config('services.recaptcha.site_key'))
              <div class="form-group mb-3 d-flex justify-content-center">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              </div>
            @endif
            <div class="form-group form-check mb-3">
              <input class="form-check-input" type="checkbox" name="acepto_terminos" value="1" id="acepto_terminos" {{ old('acepto_terminos') ? 'checked' : '' }}>
              <label class="form-check-label" for="acepto_terminos">
                Acepto los <a href="{{ route('aviso-legal') }}" target="_blank">términos y condiciones y el aviso legal</a> del portal.
              </label>
              @error('acepto_terminos')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Registrarme</button>
            </div>
          </form>

          @if(config('services.google.client_id'))
            <hr>
            <div class="text-center">
              <a href="{{ route('login.google') }}" id="btn-google-register" class="btn btn-light border w-100 d-flex align-items-center justify-content-center" style="gap: 10px; padding: 10px; font-weight: 500; color: #3c4043;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                  <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                  <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                  <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                  <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                </svg>
                Registrarme con Google
              </a>
              <p class="text-muted small mt-2 mb-0">Si elegiste "Soy particular", tu cuenta se crea gratis y activa al instante.</p>
            </div>
          @endif

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
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
    <p><a href="{{ route('aviso-legal') }}" class="text-white">Aviso legal y términos</a></p>
  </div>
</footer>
@stop

@section('js')
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function () {
    function toggleCampo() {
      var esParticular = document.getElementById('tipo_particular').checked;
      document.getElementById('campo-nombre-comercial').style.display = esParticular ? 'none' : 'block';
    }
    document.querySelectorAll('input[name="tipo_cuenta"]').forEach(function (r) {
      r.addEventListener('change', toggleCampo);
    });
    toggleCampo();

    var btnGoogle = document.getElementById('btn-google-register');
    if (btnGoogle) {
      btnGoogle.addEventListener('click', function () {
        if (document.getElementById('tipo_particular').checked) {
          this.href = this.href + '?tipo=particular';
        }
      });
    }
  });
</script>
@stop
