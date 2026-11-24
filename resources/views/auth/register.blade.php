@extends('layouts.landing')

@section('title', request('tipo') === 'particular' ? 'Publicar mi propiedad gratis | ' . config('app.name') : 'Creá tu cuenta | ' . config('app.name'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  body {
    padding-top: 56px;
    background-color: #f8fafc;
  }
  .register-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  }
  .register-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
    padding: 35px 30px;
    color: white;
    text-align: center;
    position: relative;
  }
  .tipo-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
    height: 100%;
  }
  .tipo-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
  }
  .tipo-card.selected {
    border-color: #0d6efd;
    background: #eff6ff;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
  }
  .form-control-icon-wrap {
    position: relative;
  }
  .form-control-icon-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
  }
  .form-control-icon-wrap input {
    padding-left: 40px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 15px;
  }
  .form-control-icon-wrap input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
  }
  .btn-google-custom {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    font-weight: 600;
    padding: 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  .btn-google-custom:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  }
  .divider-text {
    display: flex;
    align-items: center;
    text-align: center;
    color: #94a3b8;
    font-size: 13px;
    margin: 20px 0;
  }
  .divider-text::before, .divider-text::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e2e8f0;
  }
  .divider-text span {
    padding: 0 10px;
  }
  .footer {
    background-color: #1e293b;
    color: white;
    text-align: center;
    padding: 15px 0;
    font-size: 12px;
  }
  .footer a { color: #94a3b8; text-decoration: none; }
  .footer a:hover { color: #ffffff; }
</style>
@stop

@section('content')
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark portal-navbar fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      {{ config('app.name') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('propiedades.index') }}">Propiedades</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a></li>
        <li class="nav-item"><a class="nav-link text-success fw-bold active" href="{{ route('register', ['tipo' => 'particular']) }}"><i class="fas fa-plus-circle me-1"></i> Publicar gratis</a></li>
        <li class="nav-item"><a class="nav-link nav-cta" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
        <li class="nav-item"><a class="nav-link nav-login" href="{{ route('login') }}">Acceso</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

      <div class="register-card bg-white mb-4">
        <!-- Header con personalidad -->
        <div class="register-header">
          @if(request('tipo') === 'particular')
            <span class="badge bg-success px-3 py-2 mb-2" style="font-size: 13px;">
              <i class="fas fa-check-circle me-1"></i> Plan Gratuito para Particulares
            </span>
            <h2 class="fw-bold mb-1">Publicá tu inmueble como Dueño Directo</h2>
            <p class="mb-0 opacity-90 small">Sin comisiones inmobiliarias. Tu aviso se activa inmediatamente.</p>
          @else
            <span class="badge bg-light text-primary px-3 py-2 mb-2" style="font-size: 13px;">
              <i class="fas fa-user-plus me-1"></i> Registro en el Portal
            </span>
            <h2 class="fw-bold mb-1">Creá tu cuenta en {{ config('app.name') }}</h2>
            <p class="mb-0 opacity-90 small">Publicá como particular o sumá tu agencia inmobiliaria.</p>
          @endif
        </div>

        <div class="p-4 p-md-5">

          <!-- Registro Rápido con Google -->
          @if(config('services.google.client_id'))
            <a href="{{ route('login.google', ['tipo' => request('tipo', 'particular')]) }}" id="btn-google-register" class="btn btn-google-custom w-100 d-flex align-items-center justify-content-center mb-3">
              <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
              </svg>
              Continuar con Google (1 solo clic)
            </a>
            <div class="divider-text"><span>o completá con tu email</span></div>
          @endif

          <form method="POST" action="{{ route('register') }}" id="form-registro">
            @csrf

            <!-- Selección de Perfil Visual -->
            <label class="form-label fw-bold text-dark mb-2">¿Cómo querés publicar?</label>
            <div class="row g-3 mb-4">
              <div class="col-sm-6">
                <div class="tipo-card {{ old('tipo_cuenta', request('tipo', 'particular')) === 'particular' ? 'selected' : '' }}" onclick="seleccionarTipo('particular')">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_cuenta" id="tipo_particular" value="particular"
                           {{ old('tipo_cuenta', request('tipo', 'particular')) === 'particular' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold d-block" for="tipo_particular">
                      <i class="fas fa-user text-success me-1"></i> Soy Particular
                    </label>
                  </div>
                  <div class="text-muted small mt-1 ps-4">
                    Publicás tu propia vivienda. <strong class="text-success">Gratis y sin comisiones</strong>.
                  </div>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="tipo-card {{ old('tipo_cuenta', request('tipo')) === 'inmobiliaria' ? 'selected' : '' }}" onclick="seleccionarTipo('inmobiliaria')">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_cuenta" id="tipo_inmobiliaria" value="inmobiliaria"
                           {{ old('tipo_cuenta', request('tipo')) === 'inmobiliaria' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold d-block" for="tipo_inmobiliaria">
                      <i class="fas fa-building text-primary me-1"></i> Soy Inmobiliaria
                    </label>
                  </div>
                  <div class="text-muted small mt-1 ps-4">
                    Agencia o corredor. Vidriera propia y múltiples publicaciones.
                  </div>
                </div>
              </div>
            </div>
            @error('tipo_cuenta')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

            <!-- Datos del Usuario -->
            <div class="mb-3">
              <label for="name" class="form-label fw-bold small text-secondary">Nombre y Apellido <span class="text-danger">*</span></label>
              <div class="form-control-icon-wrap">
                <i class="fas fa-user"></i>
                <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez" autofocus>
              </div>
              @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <!-- Campo dinámico: Nombre Comercial (solo inmobiliaria) -->
            <div class="mb-3" id="campo-nombre-comercial" style="display: none;">
              <label for="nombre_comercial" class="form-label fw-bold small text-secondary">Nombre Comercial de la Inmobiliaria <span class="text-danger">*</span></label>
              <div class="form-control-icon-wrap">
                <i class="fas fa-store"></i>
                <input id="nombre_comercial" type="text" class="form-control form-control-lg @error('nombre_comercial') is-invalid @enderror"
                       name="nombre_comercial" value="{{ old('nombre_comercial') }}" placeholder="Ej: Inmobiliaria del Centro">
              </div>
              @error('nombre_comercial')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label fw-bold small text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
              <div class="form-control-icon-wrap">
                <i class="fas fa-envelope"></i>
                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required placeholder="tuemail@ejemplo.com">
              </div>
              @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label for="whatsapp" class="form-label fw-bold small text-secondary">WhatsApp de contacto</label>
                <div class="form-control-icon-wrap">
                  <i class="fab fa-whatsapp text-success"></i>
                  <input id="whatsapp" type="text" class="form-control form-control-lg @error('whatsapp') is-invalid @enderror"
                         name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Ej: 5491123456789">
                </div>
                @error('whatsapp')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="telefono" class="form-label fw-bold small text-secondary">Teléfono alternativo</label>
                <div class="form-control-icon-wrap">
                  <i class="fas fa-phone"></i>
                  <input id="telefono" type="text" class="form-control form-control-lg @error('telefono') is-invalid @enderror"
                         name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 011 4444-5555">
                </div>
                @error('telefono')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="row g-2 mb-4">
              <div class="col-md-6">
                <label for="password" class="form-label fw-bold small text-secondary">Contraseña <span class="text-danger">*</span></label>
                <div class="form-control-icon-wrap">
                  <i class="fas fa-lock"></i>
                  <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                         name="password" required placeholder="Mínimo 8 caracteres">
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="password-confirm" class="form-label fw-bold small text-secondary">Confirmar Contraseña <span class="text-danger">*</span></label>
                <div class="form-control-icon-wrap">
                  <i class="fas fa-lock"></i>
                  <input id="password-confirm" type="password" class="form-control form-control-lg"
                         name="password_confirmation" required placeholder="Repetí tu contraseña">
                </div>
              </div>
            </div>

            @if(config('services.recaptcha.site_key'))
              <div class="mb-3 d-flex justify-content-center">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
              </div>
            @endif

            <div class="form-check mb-4 p-3 bg-light rounded border">
              <input class="form-check-input ms-0 me-2" type="checkbox" name="acepto_terminos" value="1" id="acepto_terminos" {{ old('acepto_terminos') ? 'checked' : '' }} required>
              <label class="form-check-label small text-dark" for="acepto_terminos">
                Acepto los <a href="{{ route('aviso-legal') }}" target="_blank" class="fw-bold text-primary">términos, condiciones y aviso legal</a> del portal inmobiliario.
              </label>
              @error('acepto_terminos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm mb-3" style="border-radius: 8px;">
              <i class="fas fa-check-circle me-1"></i> Crear cuenta y empezar a publicar
            </button>

            <div class="text-center text-muted small">
              <i class="fas fa-shield-alt text-success me-1"></i> Registro seguro y gratuito. Tus datos no se comparten con terceros.
            </div>
          </form>

          <hr class="my-4">

          <div class="text-center">
            <span class="text-muted">¿Ya tenés una cuenta creada?</span>
            <a href="{{ route('login') }}" class="fw-bold text-primary ms-1">Iniciá sesión acá</a>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p class="mb-1">&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p class="mb-0">
      <a href="mailto:{{ $mailContacto }}">Contacto</a> · 
      <a href="{{ route('aviso-legal') }}">Aviso legal y términos</a>
    </p>
  </div>
</footer>
@stop

@section('js')
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
  function seleccionarTipo(tipo) {
    var radioParticular = document.getElementById('tipo_particular');
    var radioInmobiliaria = document.getElementById('tipo_inmobiliaria');
    if (tipo === 'particular') {
      radioParticular.checked = true;
    } else {
      radioInmobiliaria.checked = true;
    }
    actualizarVistaTipo();
  }

  function actualizarVistaTipo() {
    var esParticular = document.getElementById('tipo_particular').checked;
    document.querySelectorAll('.tipo-card').forEach(function(card) {
      card.classList.remove('selected');
    });
    if (esParticular) {
      document.querySelector('.tipo-card:first-child').classList.add('selected');
      document.getElementById('campo-nombre-comercial').style.display = 'none';
    } else {
      document.querySelectorAll('.tipo-card')[1].classList.add('selected');
      document.getElementById('campo-nombre-comercial').style.display = 'block';
    }

    var btnGoogle = document.getElementById('btn-google-register');
    if (btnGoogle) {
      var baseUrl = "{{ route('login.google') }}";
      btnGoogle.href = baseUrl + '?tipo=' + (esParticular ? 'particular' : 'inmobiliaria');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="tipo_cuenta"]').forEach(function (r) {
      r.addEventListener('change', actualizarVistaTipo);
    });
    actualizarVistaTipo();
  });
</script>
@stop
