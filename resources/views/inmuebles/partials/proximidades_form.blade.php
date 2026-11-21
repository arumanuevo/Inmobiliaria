@php
  // Card de proximidades para alta/edición de inmueble (repeater -> hidden proximidades_json)
  $proxInicial = old('proximidades_json');
  if ($proxInicial === null) {
      $proxInicial = isset($inmueble) ? json_encode($inmueble->proximidadesLista()) : '[]';
  }
  $sugerencias = \App\Models\Inmueble::sugerenciasProximidad();
@endphp
<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-header bg-white fw-bold">
        <i class="fas fa-location-arrow me-1 text-primary"></i> Proximidades y ventajas del entorno
        <small class="text-muted fw-normal">(opcional)</small>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Indicá puntos de interés cercanos (estación de tren, autopista, plaza, escuela, hospital…) con la distancia en metros.
            Se muestran en la ficha pública como ventaja comparativa del inmueble.
        </p>
        <datalist id="proximidades-suggest">
            @foreach($sugerencias as $sug)
                <option value="{{ $sug }}">
            @endforeach
        </datalist>
        <div id="proximidades-list"></div>
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btn-agregar-proximidad">
            <i class="fas fa-plus me-1"></i> Agregar proximidad
        </button>
        <input type="hidden" name="proximidades_json" id="proximidades_json" value="{{ e($proxInicial) }}">
        @error('proximidades_json')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>
