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
  .prop-modal .modal-img { height: 240px; object-fit: cover; }
  @media (min-width: 992px) {
    .prop-modal .modal-img { height: 420px; }
  }
  .prop-modal .detalle-ico { color: #6366f1; }
  .prop-modal .detalle-item { font-size: .92rem; }
</style>

<div class="row">
  @forelse($properties as $property)
    <div class="col-md-6 col-xl-4 prop-item property-card mb-4">
      <div class="card border-0 shadow-sm h-100">
        <div id="carousel{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach(($property->fotos ?? []) as $index => $foto)
              <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $property->titulo }}">
              </div>
            @endforeach
          </div>
          @if(count($property->fotos ?? []) > 1)
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
            <div class="d-flex gap-1 align-items-center flex-wrap">
              @if($property->inmobiliaria && $property->inmobiliaria->usuario?->hasRole('particular'))
                <span class="badge rounded-pill text-bg-secondary"><i class="fas fa-user me-1"></i>Dueño directo</span>
              @else
                <span class="badge rounded-pill text-bg-primary"><i class="fas fa-store me-1"></i>Inmobiliaria</span>
              @endif
              @if($property->tipo_propiedad)
                <span class="badge rounded-pill text-bg-light border">{{ \App\Models\Inmueble::tiposPropiedad()[$property->tipo_propiedad] ?? ucfirst($property->tipo_propiedad) }}</span>
              @endif
              @if($property->puntaje_entorno !== null)
                <span class="badge rounded-pill text-bg-light border" title="Puntaje de entorno (GIS)"><i class="fas fa-chart-line me-1" style="color:#059669;"></i>{{ $property->puntaje_entorno }}/100</span>
              @endif
            </div>
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
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="propertyModalLabel{{ $property->id }}">{{ $property->titulo }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body p-0">
            <div class="row g-0">
              <div class="col-lg-6 bg-dark">
                @if(!empty($property->fotos))
                  <div id="modalCarousel{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                      @foreach(($property->fotos ?? []) as $index => $foto)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                          <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100 modal-img" alt="{{ $property->titulo }}">
                        </div>
                      @endforeach
                    </div>
                    @if(count($property->fotos ?? []) > 1)
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
                @else
                  <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 240px;">
                    <i class="fas fa-image fa-3x text-white-50"></i>
                  </div>
                @endif
              </div>
              <div class="col-lg-6 p-3 p-lg-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="precio" style="font-size:1.3rem;">{{ $property->precio_formateado }}</span>
                  @if($property->inmobiliaria && $property->inmobiliaria->usuario?->hasRole('particular'))
                    <span class="badge rounded-pill text-bg-secondary">Dueño directo</span>
                  @else
                    <span class="badge rounded-pill text-bg-primary">Inmobiliaria</span>
                  @endif
                </div>
                <ul class="list-unstyled mb-3">
                  <li class="detalle-item mb-1">
                    <i class="fas fa-bed detalle-ico me-2"></i>{{ $property->habitaciones }} hab.
                    <i class="fas fa-bath detalle-ico mx-1"></i>{{ $property->banos }} baños
                    <i class="fas fa-ruler-combined detalle-ico mx-1"></i>{{ $property->superficie_cubierta }} m&sup2;
                  </li>
                  @if($property->tipo_propiedad)
                    <li class="detalle-item mb-1"><i class="fas fa-building detalle-ico me-2"></i>{{ \App\Models\Inmueble::tiposPropiedad()[$property->tipo_propiedad] ?? ucfirst($property->tipo_propiedad) }}</li>
                  @endif
                  @if($property->ambientes)
                    <li class="detalle-item mb-1"><i class="fas fa-door-open detalle-ico me-2"></i>{{ $property->ambientes }} ambientes</li>
                  @endif
                  @if($property->cocheras)
                    <li class="detalle-item mb-1"><i class="fas fa-car detalle-ico me-2"></i>{{ $property->cocheras }} cochera(s)</li>
                  @endif
                  @if($property->expensas)
                    <li class="detalle-item mb-1"><i class="fas fa-receipt detalle-ico me-2"></i>Expensas @dinero($property->expensas, 2)</li>
                  @endif
                  @if($property->orientacion)
                    <li class="detalle-item mb-1"><i class="fas fa-compass detalle-ico me-2"></i>{{ $property->orientacion }}</li>
                  @endif
                  @if($property->estado_propiedad)
                    <li class="detalle-item mb-1"><i class="fas fa-tag detalle-ico me-2"></i>{{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$property->estado_propiedad] ?? $property->estado_propiedad }}</li>
                  @endif
                </ul>
                @include('inmuebles.partials.proximidades', ['inmueble' => $property])
                @include('inmuebles.partials.entorno', ['inmueble' => $property])
                <p class="mb-1 text-muted small"><i class="fas fa-map-marker-alt detalle-ico me-2"></i>{{ $property->direccion }}, {{ $property->ciudad }}, {{ $property->estado }}</p>
                <p class="mb-2 text-muted small"><strong>Anunciante:</strong> {{ $property->inmobiliaria->nombre_comercial ?? '' }}</p>
                @if($property->descripcion)
                  <hr class="my-2">
                  <p class="small mb-0" style="white-space: pre-line;">{{ $property->descripcion }}</p>
                @endif
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            <a href="{{ route('contacto', ['property' => $property->id, 'inmobiliaria' => $property->inmobiliaria_id]) }}" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: linear-gradient(135deg,#6366f1,#8b5cf6 60%,#d946ef); border: none;">Me interesa</a>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-center py-5">
      <i class="fas fa-search fa-2x text-muted mb-3"></i>
      <p class="text-muted mb-1 fw-semibold">Todavía no hay propiedades con esos filtros.</p>
      <p class="text-muted small mb-0">Probá ampliar el rango de precio o la zona, o <a href="{{ route('propiedades.mapa') }}">mirá en el mapa</a>.</p>
    </div>
  @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
  {{ $properties->links() }}
</div>
