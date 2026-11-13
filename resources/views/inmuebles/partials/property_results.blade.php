<div class="row">
  @foreach($properties as $property)
    <div class="col-md-4 property-card">
      <div class="card shadow-sm">
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
          <h5 class="card-title">{{ $property->titulo }}</h5>
          <p class="card-text text-muted mb-1">{{ $property->inmobiliaria->nombre_comercial ?? '' }}</p>
          <p class="card-text">${{ number_format($property->precio, 2) }} • {{ $property->habitaciones }} Habitaciones • {{ $property->banos }} Baños • {{ $property->superficie_cubierta }} m²</p>
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
            <p><strong>Precio:</strong> ${{ number_format($property->precio, 2) }}</p>
            <p><strong>Habitaciones:</strong> {{ $property->habitaciones }}</p>
            <p><strong>Baños:</strong> {{ $property->banos }}</p>
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
  @endforeach
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



