@extends('layouts.landing')

@section('title', 'Buscar Propiedades')

@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
  body {
    padding-top: 56px; /* Ajusta este valor según el alto de tu navbar */
  }

  .navbar {
    height: 56px; /* Ajusta este valor según el alto de tu navbar */
  }

  .filter-container {
    margin-bottom: 20px;
  }

  .filter-container .form-group {
    margin-bottom: 15px;
  }

  .filter-container label {
    font-weight: bold;
  }

  .property-card {
    margin-bottom: 20px;
  }

  .property-card img {
    height: 200px;
    object-fit: cover;
  }

  .results-container {
    margin-top: 20px;
  }

  .button-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }
  .footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px 0; /* Reduce el padding para disminuir la altura */
    font-size: 10px; /* Opcional: reduce el tamaño del texto */
  }

  .footer p {
    margin: 0; /* Elimina márgenes extra */
  }

</style>
@stop

@section('content')
<!-- Navbar fijo -->
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
        <li class="nav-item">
          <a class="nav-link" href="{{ route('inicio') }}">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('inmobiliarias.index') }}">Inmobiliarias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('propiedades.index') }}" target="_blank">Propiedades</a>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('propiedades.mapa') }}">Mapa</a>
        </li>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('contacto') }}" target="_blank">Contacto</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="{{ route('sumate') }}">Soy inmobiliaria</a></li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}">Acceso</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h1 class="mb-4">Buscar Propiedades</h1>

  <div class="filter-container">
    <form id="search-form">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="tipo_operacion">Tipo de Operación</label>
            <select class="form-control" id="tipo_operacion" name="tipo_operacion">
              <option value="">Todos</option>
              <option value="venta">Venta</option>
              <option value="alquiler">Alquiler</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="precio_min">Precio Mínimo</label>
            <input type="number" class="form-control" id="precio_min" name="precio_min" placeholder="Precio mínimo">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="precio_max">Precio Máximo</label>
            <input type="number" class="form-control" id="precio_max" name="precio_max" placeholder="Precio máximo">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="habitaciones">Habitaciones</label>
            <select class="form-control" id="habitaciones" name="habitaciones">
              <option value="">Cualquiera</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5+</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="inmobiliaria">Inmobiliaria</label>
            <select class="form-control" id="inmobiliaria" name="inmobiliaria">
              <option value="">Todas</option>
              @foreach($inmobiliarias as $inmobiliaria)
                <option value="{{ $inmobiliaria->slug }}">{{ $inmobiliaria->nombre_comercial }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="banos">Baños</label>
            <select class="form-control" id="banos" name="banos">
              <option value="">Cualquiera</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5+</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="superficie_min">Superficie Mínima (m²)</label>
            <input type="number" class="form-control" id="superficie_min" name="superficie_min" placeholder="Superficie mínima">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="superficie_max">Superficie Máxima (m²)</label>
            <input type="number" class="form-control" id="superficie_max" name="superficie_max" placeholder="Superficie máxima">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" placeholder="Ciudad">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="tipo_propiedad">Tipo de propiedad</label>
            <select class="form-control" id="tipo_propiedad" name="tipo_propiedad">
              <option value="">Todos</option>
              @foreach($tipos as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="moneda">Moneda</label>
            <select class="form-control" id="moneda" name="moneda">
              <option value="">ARS y USD</option>
              <option value="ARS">Pesos (ARS)</option>
              <option value="USD">Dólares (USD)</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="ambientes_min">Ambientes mín.</label>
            <input type="number" class="form-control" id="ambientes_min" name="ambientes_min" min="0" placeholder="Ej: 3">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="cocheras_min">Cocheras mín.</label>
            <input type="number" class="form-control" id="cocheras_min" name="cocheras_min" min="0" placeholder="Ej: 1">
          </div>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="btn btn-primary" id="search-button">Buscar</button>
      </div>
      <hr>
    </form>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title mb-1"><i class="fas fa-bell text-primary"></i> Avisame cuando aparezca algo así</h5>
      <p class="text-muted small mb-3">Guardamos esta búsqueda con los filtros actuales y te enviamos un email cuando publiquen una propiedad que coincida.</p>
      <form id="alert-form" method="POST" action="{{ route('busquedas.guardar') }}">
        @csrf
        <input type="hidden" name="filtros_json" id="filtros_json" value="">
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre (opcional)" value="{{ auth()->user()->name ?? '' }}">
          </div>
          <div class="col-md-4">
            <input type="email" name="email" class="form-control" placeholder="Tu email" required value="{{ auth()->user()->email ?? '' }}">
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-bell"></i> Guardar búsqueda y avisarme</button>
          </div>
        </div>
        @if(config('services.recaptcha.site_key'))
          <div class="mt-2"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div></div>
        @endif
      </form>
    </div>
  </div>

  <div class="results-container" id="property-results">
    <!-- Los resultados se cargarán aquí -->
  </div>

  
</div>

 <!-- Footer -->
 <footer class="footer">
    <div class="container">
      <p>© {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
      <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
    </div>
  </footer>
@stop

@section('js')
<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<!-- Incluir DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
    function loadProperties(page = 1) {
      var formData = $('#search-form').serialize();
      $.ajax({
        url: '/buscar-propiedades?page=' + page,
        method: 'GET',
        data: formData,
        success: function(data) {
          $('#property-results').html(data);
        },
        error: function(xhr) {
          console.error(xhr.responseText);
        }
      });
    }

    $('#search-button').click(function() {
      loadProperties();
    });

    // Manejar la paginación
    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      loadProperties(page);
    });

    // Cargar propiedades al cargar la página
    loadProperties();

    // Al enviar el formulario de alerta, serializar los filtros actuales de la búsqueda
    $('#alert-form').on('submit', function () {
      var filtros = {};
      $('#search-form').serializeArray().forEach(function (item) {
        if (item.value !== '') { filtros[item.name] = item.value; }
      });
      $('#filtros_json').val(JSON.stringify(filtros));
    });
  });
</script>
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@stop


