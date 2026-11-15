<div class="row g-3">
  @forelse($properties as $property)
    <div class="col-md-6 col-12 property-card">
      <div class="card shadow-sm h-100 border-0">
        <div id="carousel{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach($property->fotos as $index => $foto)
              <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" class="d-block w-100" alt="{{ $property->titulo }}">
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $property->id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $property->id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            @if($property->inmobiliaria?->usuario?->hasRole('particular'))
              <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span>
            @else
              <span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span>
            @endif
            <small class="text-muted">{{ ucfirst($property->tipo_operacion) }}</small>
          </div>
          <h5 class="card-title"><a href="{{ route('propiedad.show', [$property->inmobiliaria->slug, $property->id]) }}" class="text-decoration-none text-dark">{{ $property->titulo }}</a></h5>
          <p class="card-text text-muted mb-1 small">
            @if($property->inmobiliaria?->usuario?->hasRole('particular'))
              <span>{{ $property->inmobiliaria->nombre_comercial }} · <strong class="text-success">Sin comisión</strong></span>
            @else
              <span>{{ $property->inmobiliaria->nombre_comercial }}</span>
            @endif
          </p>
          <p class="card-text">{{ $property->precio_formateado }} • {{ $property->habitaciones }} Hab. • {{ $property->banos }} Baños • {{ $property->superficie_cubierta }} m²</p>
          @if(isset($property->distancia_km))
            <div class="mb-2">
              <span class="badge bg-light text-dark border">
                <i class="fas fa-location-arrow text-primary me-1"></i> a {{ number_format((float) $property->distancia_km, 1) }} km del punto buscado
              </span>
            </div>
          @endif
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#propertyModal{{ $property->id }}">
            Ver Detalles
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="propertyModal{{ $property->id }}" tabindex="-1" aria-labelledby="propertyModalLabel{{ $property->id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="propertyModalLabel{{ $property->id }}">{{ $property->titulo }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
              <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel{{ $property->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel{{ $property->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
            <hr>
            <p><strong>Precio:</strong> {{ $property->precio_formateado }}</p>
            @if(isset($property->distancia_km))
              <p><strong>Distancia:</strong> <span class="badge bg-light text-dark border"><i class="fas fa-location-arrow text-primary me-1"></i> a {{ number_format((float) $property->distancia_km, 1) }} km de tu búsqueda</span></p>
            @endif
            <p><strong>Anunciante:</strong>
              @if($property->inmobiliaria?->usuario?->hasRole('particular'))
                <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Dueño Directo</span> {{ $property->inmobiliaria->nombre_comercial }} <small class="text-success fw-bold">(Sin comisión)</small>
              @else
                <span class="badge bg-primary"><i class="fas fa-building me-1"></i> Inmobiliaria</span> {{ $property->inmobiliaria->nombre_comercial }}
              @endif
            </p>
            <p><strong>Habitaciones:</strong> {{ $property->habitaciones }}</p>
            <p><strong>Baños:</strong> {{ $property->banos }}</p>
@if($property->tipo_propiedad)<p><strong>Tipo:</strong> {{ \App\Models\Inmueble::tiposPropiedad()[$property->tipo_propiedad] ?? ucfirst($property->tipo_propiedad) }}</p>@endif
            @if($property->ambientes)<p><strong>Ambientes:</strong> {{ $property->ambientes }}</p>@endif
            @if($property->cocheras)<p><strong>Cocheras:</strong> {{ $property->cocheras }}</p>@endif
            @if($property->expensas)<p><strong>Expensas:</strong> ${{ number_format($property->expensas, 2) }}</p>@endif
            @if($property->orientacion)<p><strong>Orientación:</strong> {{ $property->orientacion }}</p>@endif
            @if($property->estado_propiedad)<p><strong>Estado:</strong> {{ ['a_estrenar'=>'A estrenar','usado'=>'Usado / buen estado','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'][$property->estado_propiedad] ?? $property->estado_propiedad }}</p>@endif

            <p><strong>Superficie Cubierta:</strong> {{ $property->superficie_cubierta }} m²</p>
            <p><strong>Descripción:</strong> {{ $property->descripcion }}</p>
            <p><strong>Dirección:</strong> {{ $property->direccion }}, {{ $property->ciudad }}, {{ $property->estado }}</p>
            <p><strong>Año de Construcción:</strong> {{ $property->anio_construccion }}</p>
            <p><strong>Piscina:</strong> {{ $property->piscina ? 'Sí' : 'No' }}</p>
            <p><strong>Jardín:</strong> {{ $property->jardin ? 'Sí' : 'No' }}</p>
            <p><strong>Garaje:</strong> {{ $property->garaje ? 'Sí' : 'No' }}</p>
            <p><strong>Ascensor:</strong> {{ $property->ascensor ? 'Sí' : 'No' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <a href="{{ route('contacto', ['property' => $property->id, 'inmobiliaria' => $property->inmobiliaria_id]) }}" target="_blank" class="btn btn-primary">Me Interesa</a>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 py-5 text-center text-muted">
      <i class="fas fa-home fa-3x mb-3 text-secondary opacity-50"></i>
      <h5 class="fw-bold text-dark">No se encontraron propiedades</h5>
      <p class="small">Probá ampliando el radio de búsqueda geográfica o ajustando los filtros de precio y características.</p>
    </div>
  @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
  {{ $properties->links() }}
</div>

<style>
  .modal-img {
    height: 400px; /* Ajusta este valor según tus necesidades */
    object-fit: cover;
  }
</style>



