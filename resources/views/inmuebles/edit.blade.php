@extends('adminlte::page')

@section('title', 'Editar Inmueble')

@section('content_header')
    <h1>Editar Inmueble</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Editar Inmueble</h3>
        </div>
        <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
            <form action="{{ route('inmuebles.update', $inmueble) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Datos básicos -->
                <div class="form-group">
                    <label for="tipo_operacion">Tipo de Operación</label>
                    <select name="tipo_operacion" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="venta" {{ $inmueble->tipo_operacion == 'venta' ? 'selected' : '' }}>Venta</option>
                        <option value="alquiler" {{ $inmueble->tipo_operacion == 'alquiler' ? 'selected' : '' }}>Alquiler</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" class="form-control" value="{{ $inmueble->titulo }}" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="5" required>{{ $inmueble->descripcion }}</textarea>
                </div>
                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" value="{{ $inmueble->precio }}" required>
                </div>
                <div class="form-group">
                    <label for="superficie_cubierta">Superficie Cubierta (m²)</label>
                    <input type="number" step="0.01" name="superficie_cubierta" class="form-control" value="{{ $inmueble->superficie_cubierta }}" required>
                </div>
                <div class="form-group">
                    <label for="habitaciones">Habitaciones</label>
                    <input type="number" name="habitaciones" class="form-control" value="{{ $inmueble->habitaciones }}" required>
                </div>
                <div class="form-group">
                    <label for="banos">Baños</label>
                    <input type="number" name="banos" class="form-control" value="{{ $inmueble->banos }}" required>
                </div>

                <!-- Datos complementarios -->
                <div class="form-group">
                    <label for="anio_construccion">Año de Construcción</label>
                    <input type="number" name="anio_construccion" class="form-control" placeholder="Ej: 1999" value="{{ $inmueble->anio_construccion }}">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Ingrese la dirección" value="{{ $inmueble->direccion }}">
                </div>
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="{{ $inmueble->ciudad ?? 'Chivilcoy' }}" readonly>
                </div>
                <div class="form-group">
                    <label for="coordenadas">Coordenadas</label>
                    <input type="text" name="coordenadas" id="coordenadas" class="form-control" placeholder="Latitud, Longitud" value="{{ $inmueble->coordenadas }}">
                </div>
                <div class="form-group">
                    <label>Ubicación en el mapa (buscá la dirección o hacé clic en el mapa)</label>
                    <input type="text" id="busqueda-mapa" class="form-control mb-1" placeholder="Buscar dirección en Argentina... (ej: Av. Sarmiento 100, Chivilcoy)">
                    <div id="resultados-mapa" class="list-group mb-1"></div>
                    <div id="mapa-inmueble" style="height: 320px; border-radius: 6px; border: 1px solid #ccc;"></div>
                    <small class="text-muted">Callejera: OpenStreetMap · Satelital: Esri · Buscador: Nominatim (gratis, sin clave). Se completa solo el campo Coordenadas (lat, lng).</small>
                </div>

                <!-- Prestaciones -->
                <div class="form-group">
                    <label>Prestaciones</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="piscina" value="1" id="piscina" {{ $inmueble->piscina ? 'checked' : '' }}>
                        <label class="form-check-label" for="piscina">Piscina</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="jardin" value="1" id="jardin" {{ $inmueble->jardin ? 'checked' : '' }}>
                        <label class="form-check-label" for="jardin">Jardín</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="garaje" value="1" id="garaje" {{ $inmueble->garaje ? 'checked' : '' }}>
                        <label class="form-check-label" for="garaje">Garaje</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="ascensor" value="1" id="ascensor" {{ $inmueble->ascensor ? 'checked' : '' }}>
                        <label class="form-check-label" for="ascensor">Ascensor</label>
                    </div>
                </div>

                <!-- Datos del Propietario -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Datos del Propietario</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="propdom">Domicilio del Propietario</label>
                            <input type="text" name="propdom" class="form-control" placeholder="Ingrese el domicilio del propietario" value="{{ $inmueble->propdom }}">
                        </div>
                        <div class="form-group">
                            <label for="propnombre">Nombre del Propietario</label>
                            <input type="text" name="propnombre" class="form-control" placeholder="Ingrese el nombre del propietario" value="{{ $inmueble->propnombre }}">
                        </div>
                        <div class="form-group">
                            <label for="propdni">DNI del Propietario</label>
                            <input type="text" name="propdni" class="form-control" placeholder="Ingrese el DNI del propietario" value="{{ $inmueble->propdni }}">
                        </div>
                        <div class="form-group">
                            <label for="propemail">Email del Propietario</label>
                            <input type="email" name="propemail" class="form-control" placeholder="Ingrese el email del propietario" value="{{ $inmueble->propemail }}">
                        </div>
                        <div class="form-group">
                            <label for="proptel">Teléfono del Propietario</label>
                            <input type="text" name="proptel" class="form-control" placeholder="Ingrese el teléfono del propietario" value="{{ $inmueble->proptel }}">
                        </div>
                    </div>
                </div>

                <!-- Visibilidad -->
                <div class="form-group form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="publicado" value="1" id="publicado" {{ $inmueble->publicado ? 'checked' : '' }}>
                    <label class="form-check-label" for="publicado">Publicado (visible en el portal público)</label>
                </div>

                <!-- Botones centrados -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-warning">Actualizar Inmueble</button>
                    <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var inputCoord = document.getElementById('coordenadas');
    var centro = [-38.5, -64.5], zoom = 4;
    var lat0 = NaN, lng0 = NaN;
    if (inputCoord && inputCoord.value) {
        var partes = inputCoord.value.split(',');
        lat0 = parseFloat(partes[0]);
        lng0 = parseFloat(partes[1]);
        if (!isNaN(lat0) && !isNaN(lng0)) { centro = [lat0, lng0]; zoom = 15; }
    }
    var map = L.map('mapa-inmueble').setView(centro, zoom);
    var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics'
    });
    L.control.layers({ 'Callejera (OSM)': osm, 'Satelital (Esri)': satelite }, null, { position: 'topright' }).addTo(map);
    var marker = null;
    function setCoords(lat, lng) {
        if (inputCoord) { inputCoord.value = lat.toFixed(6) + ', ' + lng.toFixed(6); }
        if (marker) { map.removeLayer(marker); }
        marker = L.marker([lat, lng]).addTo(map);
    }
    if (!isNaN(lat0) && !isNaN(lng0)) { setCoords(lat0, lng0); }
    map.on('click', function (e) { setCoords(e.latlng.lat, e.latlng.lng); });
    var inputBusqueda = document.getElementById('busqueda-mapa');
    var resultados = document.getElementById('resultados-mapa');
    var timer = null;
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function () {
            clearTimeout(timer);
            var q = inputBusqueda.value.trim();
            if (q.length < 3) { resultados.innerHTML = ''; return; }
            timer = setTimeout(function () {
                fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=ar&limit=5&addressdetails=1&q=' + encodeURIComponent(q), {
                    headers: { 'Accept-Language': 'es' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        resultados.innerHTML = '';
                        data.forEach(function (item) {
                            var b = document.createElement('button');
                            b.type = 'button';
                            b.className = 'list-group-item list-group-item-action py-1 px-2 small text-left';
                            b.textContent = item.display_name;
                            b.onclick = function () {
                                var lat = parseFloat(item.lat), lng = parseFloat(item.lon);
                                map.flyTo([lat, lng], 16);
                                setCoords(lat, lng);
                                resultados.innerHTML = '';
                                inputBusqueda.value = '';
                            };
                            resultados.appendChild(b);
                        });
                    })
                    .catch(function () {});
            }, 400);
        });
    }
});
</script>
@stop

