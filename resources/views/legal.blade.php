@extends('layouts.landing')

@section('title', 'Aviso legal y términos | ' . config('app.name'))
@section('meta')
<meta name="description" content="Aviso legal y términos de uso de {{ config('app.name') }}: el portal es un espacio de avisos; cada anunciante declara ser propietario o estar autorizado.">
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<style>
  body { padding-top: 56px; }
  .legal h2 { font-size: 1.3rem; font-weight: 700; margin-top: 1.5rem; }
  .legal p, .legal li { color: #475467; }
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
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Volver al portal</a></li>
    </ul>
  </div>
</nav>

<div class="container py-5 legal" style="max-width: 860px;">
  <h1 class="h3 fw-bold mb-4">Aviso legal y términos de uso</h1>
  <p class="text-muted">Última actualización: {{ date('d/m/Y') }}</p>

  <h2>1. Naturaleza del servicio</h2>
  <p>{{ config('app.name') }} es un <strong>portal de avisos inmobiliarios</strong>: un espacio donde inmobiliarias verificadas y propietarios particulares publican sus propiedades. El portal <strong>no es una inmobiliaria</strong>, no interviene en las operaciones de compra, venta o alquiler, no intermedia pagos ni documentos, y no verifica legal ni físicamente las propiedades anunciadas.</p>
  <p><strong>Habilitación profesional:</strong> en Argentina la actividad de corredor/martillero inmobiliario está regulada por cada provincia (por ejemplo, el Registro/Colegio de Martilleros y Corredores Inmobiliarios de la Ciudad Autónoma de Buenos Aires y de la Provincia de Buenos Aires) y requiere inscripción vigente, además de la inscripción en AFIP (CUIT e Ingresos Brutos) para facturar comisiones. {{ config('app.name') }} no valida automáticamente esas matrículas: el sello "Verificada" que puede exhibir una inmobiliaria indica que el administrador del portal chequeó los datos declarados (CUIT y matrícula), pero <strong>cada usuario debe corroborar la habilitación vigente en el registro provincial correspondiente antes de operar</strong>. Asimismo, en locaciones residenciales la comisión corresponde al locador, no al inquilino (Ley 27.551).</p>

  <h2>2. Responsabilidad del anunciante</h2>
  <p>Quien publica una propiedad declara, bajo su exclusiva responsabilidad, que es propietario/a de la misma o que cuenta con autorización expresa del propietario para anunciarla, y que todos los datos cargados (precio, superficie, ubicación, características, fotos) son veraces y no inducen a error. El portal no se responsabiliza por el contenido de los avisos ni por las operaciones que se concreten entre las partes.</p>

  <h2>3. Usuarios particulares</h2>
  <p>Las cuentas de tipo "particular" pueden publicar su propia vivienda en forma gratuita y dentro de los límites del plan asignado (cantidad de publicaciones, fotos y destacados). El portal puede suspender o dar de baja avisos de particulares que infrinjan estos términos.</p>

  <h2>4. Consultas y datos personales</h2>
  <p>Cuando un interesado completa el formulario de contacto, sus datos (nombre, email, teléfono y mensaje) se envían directamente al anunciante y quedan registrados en el portal con fines de seguimiento. No compartimos datos personales con terceros. Podés ejercer tus derechos de acceso, rectificación y supresión escribiendo a <a href="mailto:{{ $mailContacto }}">{{ $mailContacto }}</a>.</p>

  <h2>5. Contenidos prohibidos</h2>
  <ul>
    <li>Avisos falsos, engañosos o de propiedades de terceros sin autorización.</li>
    <li>Contenido discriminatorio, ilegal o que infrinja derechos de terceros.</li>
    <li>Publicación de datos de contacto falsos para captar consultas.</li>
  </ul>

  <h2>6. Baja y suspensión</h2>
  <p>El administrador del portal puede suspender cuentas, despublicar avisos o dar de baja perfiles que incumplan estos términos, sin necesidad de aviso previo y sin que ello genere derecho a compensación alguna.</p>

  <h2>7. Propiedad intelectual</h2>
  <p>El diseño, marca y contenidos del portal pertenecen a su titular. Las fotos y descripciones de cada aviso son responsabilidad de su anunciante, quien declara contar con los derechos para publicarlas.</p>

  <h2>8. Ley aplicable</h2>
  <p>Estos términos se rigen por las leyes de la República Argentina. Para cualquier diferencia, las partes se someten a los tribunales ordinarios del domicilio del titular del portal.</p>

  <h2>9. Contacto</h2>
  <p>Consultas sobre estos términos: <a href="mailto:{{ $mailContacto }}">{{ $mailContacto }}</a></p>
</div>

<footer class="footer">
  <div class="container">
    <p>&copy; {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:{{ $mailContacto }}" class="text-white">{{ $mailContacto }}</a></p>
  </div>
</footer>
@stop
