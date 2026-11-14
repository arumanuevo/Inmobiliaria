@extends('layouts.landing')

@section('title', 'Bienvenido a ' . config('app.name'))
@section('css')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
<style>
  .carousel-inner {
    height: auto; /* Ajusta según necesites */
  }

  .carousel-item img {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Asegura que la imagen se recorte en lugar de deformarse */
  }

  .hero {
    position: relative;
    height: 60vh;
    overflow: hidden;
  }

  .hero .container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 10;
    width: 100%;
  }

  .hero h1, .hero p {
    margin-bottom: 15px;
  }

  .hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 5;
  }

  @media (max-width: 768px) {
    .hero {
      height: auto; /* Para que el contenido se expanda */
      display: flex;
      flex-direction: column;
      position: relative;
      height: 550px;
    }

    .carousel {
      order: 1; /* Mantiene el carrusel arriba */
    }

    .hero-content {
      order: 2; /* Mueve el texto debajo */
      position: static;
      transform: none;
      text-align: center;
      padding: 20px;
      background: rgba(0, 0, 0, 0.6); /* Opcional: fondo oscuro para mejor legibilidad */
    }

    .hero .container {
      position: absolute;
      left: 50%;
      transform: translate(-50%, -0%);
      text-align: center;
      z-index: 10;
      width: 100%;
      margin-top: 0px;
    }

    .carousel-item img {
      height: 275px; /* Ajusta la altura en dispositivos móviles */
    }

    .hero::before {
      background: none;
    }

    @media (orientation: landscape) {
      .hero {
        height: 50vh; /* Reduce la altura para adaptarse mejor al ancho */
        display: flex;
        flex-direction: row; /* Cambia la dirección para alinearlo horizontalmente */
        justify-content: space-between; /* Espaciado entre el carrusel y el contenido */
        align-items: center; /* Centra el contenido verticalmente */
      }

      .carousel-inner {
        height: 500px; /* Ajusta el tamaño del carrusel */
      }

      .carousel-item img {
        height: 100%;
        object-fit: cover; /* Asegura que la imagen siga cubriendo el área del carrusel */
      }

      .hero-content {
        order: 1; /* Mueve el contenido del texto hacia la izquierda */
        position: static;
        transform: none;
        text-align: left;
        padding: 30px;
        width: 30%; /* Ajusta el ancho del texto para no ocupar toda la pantalla */
        height: 10%;
      }

      .carousel {
        order: 2; /* Mueve el carrusel a la derecha */
        width: 50%; /* Ajusta el ancho del carrusel para que encaje mejor */
      }

      .hero .container {
        position: static;
        transform: none;
        width: 100%;
        margin-top: 0;
        height: 35vh; /* Reduce aún más la altura */
        padding: 10px 0; /* Reduce el espacio interno */
      }

      .hero .container h1,
      .hero .container p {
        font-size: 100%; /* Reduce el tamaño del texto */
      }

      .hero .container button {
        font-size: 100%; /* Reduce un poco el tamaño del botón */
        padding: 8px 12px; /* Ajusta el padding del botón */
      }

      .carousel-item img {
        height: 400px; /* Ajusta la altura del carrusel para que no ocupe tanto espacio */
      }

      .hero .container {
        position: static;
        transform: none;
        width: 100%;
        margin-top: 0;
        height: 30vh; /* Reduce aún más la altura */
        padding: 5px 0; /* Reduce el espacio interno */
      }

      .hero .container h1,
      .hero .container p {
        font-size: 100%; /* Ajusta el tamaño del texto */
        margin-bottom: 5px; /* Reduce el espacio entre texto y botón */
      }

      .hero .container button {
        font-size: 90%; /* Reduce un poco el tamaño del botón */
        padding: 6px 10px; /* Ajusta el padding para hacerlo más compacto */
        margin-top: 0px; /* Reduce la separación entre el texto y el botón */
      }

      .hero .container button {
        font-size: 80%; /* Reduce el tamaño del texto */
        padding: 5px 8px; /* Ajusta el tamaño del botón */
      }
    }
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

  .carousel-destacado .carousel-inner {
    height: 200px; /* Altura específica para los carruseles destacados */
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
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('contacto') }}" target="_blank">Contacto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('login') }}" target="_blank" >Acceso</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<!-- Agregar padding superior para evitar que el contenido quede tapado por el navbar fijo -->
<div style="padding-top: 56px;">
  <!-- Sección Hero -->
  <section class="hero" style="position: relative;">
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators" id="carousel-indicators">
        <!-- Indicadores dinámicos -->
      </ol>
      <div class="carousel-inner" id="carousel-inner">
        <!-- Imágenes dinámicas -->
      </div>
      <!--<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>-->
    </div>
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);"></div>
    <div class="container hero-content">
      <div class="row align-items-center justify-content-center text-center">
        <div class="col-md-10">
          <h1 class="display-5 fw-bold text-white">Encuentra el hogar de tus sueños</h1>
          <p class="lead text-white">Propiedades exclusivas para compra, venta y alquiler en las mejores ubicaciones.</p>
          <a href="{{ route('propiedades.index') }}" target="_blank" class="btn btn-primary btn-lg">Explorar Propiedades</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Sobre Nosotros -->
  <section id="about" class="py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
          <img src="{{ asset('images/about.jpg') }}" class="img-fluid rounded" alt="Sobre {{ config('app.name') }}">
        </div>
        <div class="col-md-6">
          <h2 class="mb-4">Sobre Nosotros</h2>
          <p>Con más de 20 años de experiencia, nos dedicamos a conectar a personas con el hogar ideal. Nuestro compromiso es ofrecer asesoramiento personalizado, propiedades de alta calidad y un servicio excepcional en cada transacción.</p>
          <p>Ya sea que busques comprar, vender o alquilar, nuestro equipo de profesionales está listo para acompañarte en cada paso del camino.</p>
          <p><strong>Dirección:</strong> Calle Falsa 123, Ciudad, País</p>
          <p><strong>Teléfono:</strong> +123 456 7890</p>
          <a href="{{ route('contacto') }}" target="_blank" class="btn btn-outline-primary mt-3">Contáctanos</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Propiedades Destacadas -->
  <section id="properties" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="mb-4">Propiedades Destacadas</h2>
        <p class="text-muted">Explora algunas de las propiedades más exclusivas que tenemos para ti.</p>
      </div>
      <div class="row">
        @foreach($destacados as $inmueble)
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm">
            <div id="carousel{{ $inmueble->id }}" class="carousel carousel-destacado slide" data-ride="carousel">
              <div class="carousel-inner">
                @foreach($inmueble->fotos as $index => $foto)
                  <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                  </div>
                @endforeach
              </div>
              <a class="carousel-control-prev" href="#carousel{{ $inmueble->id }}" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carousel{{ $inmueble->id }}" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ $inmueble->titulo }}</h5>
              <p class="card-text text-muted mb-1">{{ $inmueble->inmobiliaria->nombre_comercial ?? '' }}</p>
              <p class="card-text">${{ number_format($inmueble->precio, 2) }} • {{ $inmueble->habitaciones }} Habitaciones • {{ $inmueble->banos }} Baños • {{ $inmueble->superficie_cubierta }} m²</p>
              <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $inmueble->id }}">
                Ver Detalles
              </button>
            </div>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="propertyModal{{ $inmueble->id }}" tabindex="-1" aria-labelledby="propertyModalLabel{{ $inmueble->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="propertyModalLabel{{ $inmueble->id }}">{{ $inmueble->titulo }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div id="modalCarousel{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    @foreach($inmueble->fotos as $index => $foto)
                      <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $inmueble->titulo }}">
                      </div>
                    @endforeach
                  </div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel{{ $inmueble->id }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>
                <hr>
                <p><strong>Precio:</strong> ${{ number_format($inmueble->precio, 2) }}</p>
                <p><strong>Habitaciones:</strong> {{ $inmueble->habitaciones }}</p>
                <p><strong>Baños:</strong> {{ $inmueble->banos }}</p>
                <p><strong>Superficie Cubierta:</strong> {{ $inmueble->superficie_cubierta }} m²</p>
                <p><strong>Descripción:</strong> {{ $inmueble->descripcion }}</p>
                <p><strong>Dirección:</strong> {{ $inmueble->direccion }}, {{ $inmueble->ciudad }}, {{ $inmueble->estado }}</p>
                <p><strong>Año de Construcción:</strong> {{ $inmueble->anio_construccion }}</p>
                <p><strong>Piscina:</strong> {{ $inmueble->piscina ? 'Sí' : 'No' }}</p>
                <p><strong>Jardín:</strong> {{ $inmueble->jardin ? 'Sí' : 'No' }}</p>
                <p><strong>Garaje:</strong> {{ $inmueble->garaje ? 'Sí' : 'No' }}</p>
                <p><strong>Ascensor:</strong> {{ $inmueble->ascensor ? 'Sí' : 'No' }}</p>
              </div>
              <div class="modal-footer">
                <a href="{{ route('inmobiliarias.show', $inmueble->inmobiliaria->slug) }}" class="btn btn-outline-primary">Ver inmobiliaria</a>
                <a href="{{ route('contacto', ['property' => $inmueble->id, 'inmobiliaria' => $inmueble->inmobiliaria_id]) }}" target="_blank" class="btn btn-primary">Me Interesa</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Sección Testimonios -->
  <section id="testimonials" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Testimonios</h2>
        <p class="text-muted">Lo que dicen nuestros clientes</p>
      </div>
      <div class="row">
        <!-- Testimonio 1 -->
        <div class="col-md-4 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <p class="card-text">"{{ config('app.name') }} hizo realidad el sueño de mi familia. Un servicio impecable y profesional."</p>
              <h6 class="card-subtitle text-muted">- Juan Pérez</h6>
            </div>
          </div>
        </div>
        <!-- Testimonio 2 -->
        <div class="col-md-4 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <p class="card-text">"La atención y el compromiso de su equipo es excepcional. Recomiendo totalmente sus servicios."</p>
              <h6 class="card-subtitle text-muted">- María Gómez</h6>
            </div>
          </div>
        </div>
        <!-- Testimonio 3 -->
        <div class="col-md-4 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <p class="card-text">"Profesionales y confiables. Mi experiencia fue excelente desde el primer contacto."</p>
              <h6 class="card-subtitle text-muted">- Carlos López</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Llamada a la Acción -->
  <section id="cta" class="py-5 bg-primary text-white">
    <div class="container text-center">
      <h2 class="mb-4">¿Listo para encontrar tu nuevo hogar?</h2>
      <p class="mb-4">Contáctanos hoy mismo y descubre cómo podemos ayudarte a dar el siguiente paso.</p>
      <a href="{{ route('contacto') }}" class="btn btn-light btn-lg">Contáctanos</a>
    </div>
  </section>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/67ca3d8b374e52190e33cf0a/1iln00qbg';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>© {{ date('Y') }} Arumasoft | Todos los derechos reservados</p>
      <p>Contacto: <a href="mailto:contacto@gertech-arumasoft.com" class="text-white">contacto@gertech-arumasoft.com</a></p>
    </div>
  </footer>
<!-- Mensaje de éxito -->
@if(session('success'))

  <script>
    alert('Su mensaje fue enviado con éxito! Pronto nos pondremos en contacto con usted.');
  </script>
@endif

</div>
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
