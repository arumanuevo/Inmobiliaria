@extends('layouts.landing')

@section('title', 'Contacto')

@section('content_header')
    <h1>Contacto</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<style>
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
  }

  .content-wrapper {
    flex: 1;
    padding-top: 56px; /* Ajusta este valor según el alto de tu navbar */
  }

  .footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px 0;
    font-size: 10px;
  }

  .footer p {
    margin: 0;
  }
</style>
@stop

@section('content')
<!-- Navbar fijo -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="{{ route('inicio') }}">
      <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo Inmobiliaria XYZ" width="30" height="30" class="d-inline-block align-text-top">
      Inmobiliaria XYZ
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('inmuebles.index') }}" target="_blank">Propiedades</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('contacto') }}" target="_blank">Contacto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">Acceso</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="content-wrapper">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Envíanos tu consulta</h3>
          </div>
          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success">
                {{ session('success') }}
              </div>
            @endif
            <form action="{{ route('contacto.store') }}" method="POST">
              @csrf
              <input type="hidden" name="inmobiliaria_id" value="{{ request('inmobiliaria') }}">
              <input type="hidden" name="inmueble_id" value="{{ request('property') }}">
              @if(request()->has('inmobiliaria'))
                <?php $inmobiliaria = \App\Models\Inmobiliaria::find(request('inmobiliaria')); ?>
                @if($inmobiliaria)
                  <div class="alert alert-info">Tu consulta se enviará a <strong>{{ $inmobiliaria->nombre_comercial }}</strong></div>
                @endif
              @endif
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required>
              </div>
              <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="Tu correo" required>
              </div>
              <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" class="form-control" placeholder="Tu teléfono">
              </div>
              <div class="form-group">
                <label for="mensaje">Mensaje</label>
                <textarea name="mensaje" class="form-control" rows="5" placeholder="Escribe tu mensaje" required>
@if(request()->has('property'))
    <?php $property = \App\Models\Inmueble::find(request()->property); ?>
Me interesa la propiedad con las siguientes características:
    - Título: {{ $property->titulo }}
    - Precio: ${{ number_format($property->precio, 2) }}
    - Habitaciones: {{ $property->habitaciones }}
    - Baños: {{ $property->banos }}
    - Superficie Cubierta: {{ $property->superficie_cubierta }} m²
    - Dirección: {{ $property->direccion }}, {{ $property->ciudad }}, {{ $property->estado }}
    - Año de Construcción: {{ $property->anio_construccion }}
    - Piscina: {{ $property->piscina ? 'Sí' : 'No' }}
    - Jardín: {{ $property->jardin ? 'Sí' : 'No' }}
    - Garaje: {{ $property->garaje ? 'Sí' : 'No' }}
    - Ascensor: {{ $property->ascensor ? 'Sí' : 'No' }}
@endif
                </textarea>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-info">Enviar Mensaje</button>
                <button type="button" class="btn btn-secondary" onclick="history.back()">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>© 2024 GerTech-ArumaSoft | Todos los derechos reservados</p>
    <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
  </div>
</footer>
@stop

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const carouselInner = document.getElementById('carousel-inner');
    const carouselIndicators = document.getElementById('carousel-indicators');

    fetch('/portadaimages')
      .then(response => response.json())
      .then(images => {
        images.forEach((image, index) => {
          // Crear el elemento del carrusel
          const carouselItem = document.createElement('div');
          carouselItem.classList.add('carousel-item');
          if (index === 0) {
            carouselItem.classList.add('active');
          }
          carouselItem.innerHTML = `<img src="/images/portada/${image}" class="d-block w-100" alt="...">`;
          carouselInner.appendChild(carouselItem);

          // Crear el indicador del carrusel
          const indicator = document.createElement('li');
          indicator.setAttribute('data-target', '#carouselExampleIndicators');
          indicator.setAttribute('data-slide-to', index);
          if (index === 0) {
            indicator.classList.add('active');
          }
          carouselIndicators.appendChild(indicator);
        });
      })
      .catch(error => console.error('Error fetching images:', error));
  });
</script>
@stop




