<style>
  .prop-item .card { border-radius: 18px; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
  .prop-item .card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(16,24,40,.16) !important; }
  .prop-item .carousel-item img { height: 210px; object-fit: cover; }
  .prop-item .precio { color: #4f46e5; font-weight: 800; font-size: 1.1rem; }
  .prop-item .btn-detalle { border-radius: 999px; font-weight: 600; border-color: #6366f1; color: #4f46e5; }
  .prop-item .btn-detalle:hover {
    background: linear-gradient(135deg, #6366f1, #8b5cf6 60%, #d946ef);
    border-color: transparent; color: #fff;
  }
  .prop-modal .modal-content { border: none; border-radius: 20px; overflow: hidden; }
  .prop-modal .modal-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    color: #fff; border: none;
  }
  .prop-modal .modal-header .btn-close { filter: invert(1); }
  .prop-modal .modal-img { height: 380px; object-fit: cover; }
</style>

<div class="row">
  @foreach($properties as $property)
    <div class="col-md-6 col-xl-4 prop-item property-card mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div id="carousel{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach($property->fotos as $index => $foto)
              <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $property->titulo }}">
              </div>
            @endforeach
          </div>
          @if(count($property->fotos) > 1)
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $property->id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $property->id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
          @endif
        </div>
        <div class="card-body d-flex flex-column">
          <div class="d-flex justify-content-between align-items-center mb-2">
            @if($property->inmobiliaria && $property->inmobiliaria->usuario?->hasRole('particular'))
              <span class="badge rounded-pill text-bg-secondary"><i class="fas fa-user me-1"></i>Dueño directo</span>
            @else
              <span class="badge rounded-pill text-bg-primary"><i class="fas fa-store me-1"></i>Inmobiliaria</span>
            @endif
            <span class="precio">{{ $property->precio_formateado }}</span>
          </div>
          <h5 class="card-title mb-1">
            <a href="{{ route('propiedad.show', [$property->inmobiliaria->slug, $property->id]) }}" class="text-decoration-none text-dark">{{ $property->titulo }}</a>
          </h5>
          <p class="card-text text-muted small mb-2">
            <i class="fas fa-building me-1"></i>{{ $property->inmobiliaria->nombre_comercial ?? '' }}
            @if($property->ciudad) &middot; <i class="fas fa-map-marker-alt me-1"></i>{{ $property->ciudad }}@endif
          </p>
          <p class="card-text small text-muted mb-3">
            <i class="fas fa-bed me-1"></i>{{ $property->habitaciones }} hab.
            <i class="fas fa-bath ms-2 me-1"></i>{{ $property->banos }} baños
            <i class="fas fa-ruler-combined ms-2 me-1"></i>{{ $property->superficie_cubierta }} m&sup2;
            @if($property->ambientes)<i class="fas fa-door-open ms-2 me-1"></i>{{ $property->ambientes }} amb.@endif
          </p>
          <button type="button" class="btn btn-outline-primary btn-sm btn-detalle mt-auto align-self-start px-3" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $property->id }}">
            <i class="fas fa-eye me-1"></i> Ver detalles
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade prop-modal" id="propertyModal{{ $property->id }}" tabindex="-1" aria-labelledby="propertyModalLabel{{ $property->id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="propertyModalLabel{{ $property->id }}">{{ $property->titulo }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div id="modalCarousel{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                @foreach($property->fotos as $index => $foto)
                  <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100 modal-img" alt="{{ $property->titulo }}">
                  </div>
                @endforeach
              </div>
              @if(count($property->fotos) > 1)
              <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel{{ $property->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel{{ $property->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
              </button>
              @endif
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="precio" style="font-size:1.3rem;">{{ $property->precio_formateado }}</span>
              @if($property->inmobiliaria && $property->inmobiliaria->usuario?->hasRole('particular'))
                <span class="badge rounded-pill text-bg-secondary">Dueño directo</span>
              @else
                <span class="badge rounded-pill text-bg-primary">Inmobiliaria</span>
              @endif
            </div>
            <p class="mb-1"><strong>Habitaciones:</strong> {{ $property->habitaciones }} &middot; <strong>Baños:</strong> {{ $property->banos }} &middot; <strong>Superficie:</strong> {{ $property->superficie_cubierta }} m&sup2;</p>
@if($property->tipo_propiedad)<p class="mb-1"><strong>Tipo:</strong> {{ \App\Models\Inmueble::tiposPropiedad()[$property->tipo_propiedad] ?? ucfirst($property->tipo_propiedad) }}</p>@endif
            @if($property->ambientes)<p class="mb-1"><strong>Ambientes:</strong> {{ $property->ambientes }}</p>@endif
            @if($property->cocheras)<p class="mb-1"><strong>Cocheras:</strong> {{ $property->cocheras }}</p>@endif
            @if($property->expensas)<p class="mb-1"><strong>Expensas:</strong> ${{ number_format($property->expensas, 2) }}</p>@endif
            @if($property->orientacion)<p class="mb-1"><strong>Orientación:</strong> {{ $property->orientacion }}</p>@endif
            @if($property->estado_propiedad)<p class="mb-1"><strong>Estado:</strong> {{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$property->estado_propiedad] ?? $property->estado_propiedad }}</p>@endif
            <p class="mb-1"><strong>Descripción:</strong> {{ $property->descripcion }}</p>
            <p class="mb-1 text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $property->direccion }}, {{ $property->ciudad }}, {{ $property->estado }}</p>
            <p class="mb-0 text-muted small"><strong>Anunciante:</strong> {{ $property->inmobiliaria->nombre_comercial ?? '' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            <a href="{{ route('contacto', ['property' => $property->id, 'inmobiliaria' => $property->inmobiliaria_id]) }}" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef); border: none;">Me interesa</a>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="d-flex justify-content-center mt-4">
  {{ $properties->links() }}
</div>
