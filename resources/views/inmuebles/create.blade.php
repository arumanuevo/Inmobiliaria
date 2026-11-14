@extends('adminlte::page')

@section('title', 'Nuevo Inmueble')

@section('content_header')
    <h1>Nuevo Inmueble</h1>
@stop

@section('content')
<div class="container-fluid scrollable-container">
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Crear Inmueble</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('inmuebles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Datos básicos -->
                <div class="form-group">
                    <label for="tipo_operacion">Tipo de Operación</label>
                    <select name="tipo_operacion" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="venta">Venta</option>
                        <option value="alquiler">Alquiler</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="superficie_cubierta">Superficie Cubierta (m²)</label>
                    <input type="number" step="0.01" name="superficie_cubierta" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="habitaciones">Habitaciones</label>
                    <input type="number" name="habitaciones" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="banos">Baños</label>
                    <input type="number" name="banos" class="form-control" required>
                </div>

                <!-- Datos complementarios -->
                <div class="form-group">
                    <label for="anio_construccion">Año de Construcción</label>
                    <input type="number" name="anio_construccion" class="form-control" placeholder="Ej: 1999">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Ingrese la dirección">
                </div>
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="Chivilcoy" readonly>
                </div>
                <div class="form-group">
                    <label for="coordenadas">Coordenadas</label>
                    <input type="text" name="coordenadas" id="coordenadas" class="form-control" placeholder="Latitud, Longitud">
                </div>
                <div class="form-group">
                    <label for="busqueda-mapa">Ubicación en el mapa</label>
                    <div class="input-group mb-1">
                        <input type="text" id="busqueda-mapa" class="form-control" placeholder="Buscar dirección en Argentina... (ej: Av. Sarmiento 100, Chivilcoy)">
                        <button type="button" id="btn-buscar-mapa" class="btn btn-outline-secondary">Buscar</button>
                    </div>
                    <div id="resultados-mapa" class="list-group mb-1"></div>
                    <div id="mapa-inmueble" style="height: 320px; border-radius: 6px; border: 1px solid #ccc;"></div>
                    <small class="text-muted">Ayuda: escribí la dirección y elegí un resultado (o apretá Buscar), o hacé clic directamente en el mapa. Las coordenadas se completan solas.</small>
                </div>

                <!-- Prestaciones -->
                <div class="form-group">
                    <label>Prestaciones</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="piscina" value="1">
                        <label class="form-check-label" for="piscina">Piscina</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="jardin" value="1">
                        <label class="form-check-label" for="jardin">Jardín</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="garaje" value="1">
                        <label class="form-check-label" for="garaje">Garaje</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="ascensor" value="1">
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
                            <input type="text" name="propdom" class="form-control" placeholder="Ingrese el domicilio del propietario">
                        </div>
                        <div class="form-group">
                            <label for="propnombre">Nombre del Propietario</label>
                            <input type="text" name="propnombre" class="form-control" placeholder="Ingrese el nombre del propietario">
                        </div>
                        <div class="form-group">
                            <label for="propdni">DNI del Propietario</label>
                            <input type="text" name="propdni" class="form-control" placeholder="Ingrese el DNI del propietario">
                        </div>
                        <div class="form-group">
                            <label for="propemail">Email del Propietario</label>
                            <input type="email" name="propemail" class="form-control" placeholder="Ingrese el email del propietario">
                        </div>
                        <div class="form-group">
                            <label for="proptel">Teléfono del Propietario</label>
                            <input type="text" name="proptel" class="form-control" placeholder="Ingrese el teléfono del propietario">
                        </div>
                    </div>
                </div>

                <!-- Subida de imágenes con Alpine.js -->
                <div class="form-group" x-data="imageUploader()">
                    <!-- Contenedor para centrar el botón -->
                    <div class="d-flex justify-content-center mb-3">
                        <!-- Botón de Bootstrap para seleccionar imágenes -->
                        <!-- <button type="button" class="btn btn-primary" @click="$refs.fileInput.click()">
                            Seleccionar Imágenes
                        </button>-->
                    </div>

                    <!-- Input file oculto -->
                    <input type="file" name="imagenes[]" class="d-none" multiple accept="image/*"
                        @change="handleFileSelect($event)" x-ref="fileInput">

                    <!-- Vista previa de imágenes -->
                    <div class="mt-2 d-flex flex-wrap">
                        <template x-for="(image, index) in images" :key="index">
                            <div class="m-2 position-relative">
                                <img :src="image" class="img-thumbnail" style="width: 100px; height: 100px;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute"
                                    style="top: 5px; right: 5px;"
                                    @click="removeImage(index)">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Visibilidad -->
                <div class="form-group form-check">
                    <input class="form-check-input" type="checkbox" name="publicado" value="1" id="publicado" checked>
                    <label class="form-check-label" for="publicado">Publicado (visible en el portal público)</label>
                </div>

                <hr>
                <!-- Contenedor para los botones -->
                <div class="d-flex justify-content-center">
                    <!-- Botón Guardar Inmueble -->
                    <button type="submit" class="btn btn-success mr-2">Guardar Inmueble</button>

                    <!-- Botón Cancelar -->
                    <a href="#" class="btn btn-secondary" onclick="window.history.back(); return false;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Estilo para el contenedor scrollable */
    .scrollable-container {
        max-height: 80vh;
        overflow-y: auto;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contenedor = document.getElementById('mapa-inmueble');
    var inputCoord = document.getElementById('coordenadas');
    if (!contenedor) { return; }
    if (typeof L === 'undefined') {
        contenedor.innerHTML = '<div class="p-3 text-danger small">No se pudo cargar el mapa (Leaflet). Recargá la página.</div>';
        return;
    }
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
    var btnBuscar = document.getElementById('btn-buscar-mapa');
    var resultados = document.getElementById('resultados-mapa');
    function buscar() {
        var q = inputBusqueda.value.trim();
        if (q.length < 3) { resultados.innerHTML = ''; return; }
        resultados.innerHTML = '<div class="list-group-item small text-muted py-1 px-2">Buscando...</div>';
        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&countrycodes=ar&limit=5&addressdetails=1&q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'Accept-Language': 'es' }
        })
            .then(function (r) {
                if (!r.ok) { throw new Error('HTTP ' + r.status); }
                return r.json();
            })
            .then(function (data) {
                resultados.innerHTML = '';
                if (!data.length) {
                    resultados.innerHTML = '<div class="list-group-item small text-muted py-1 px-2">Sin resultados. Probá con más detalle (calle y número, ciudad).</div>';
                    return;
                }
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
            .catch(function (err) {
                resultados.innerHTML = '<div class="list-group-item small text-danger py-1 px-2">Error al buscar (' + err.message + '). Probá de nuevo o usá el clic en el mapa.</div>';
            });
    }
    var timer = null;
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(buscar, 400);
        });
        inputBusqueda.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); clearTimeout(timer); buscar(); }
        });
    }
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function (e) { e.preventDefault(); clearTimeout(timer); buscar(); });
    }
});
</script>

<script>
    function imageUploader() {
        return {
            images: [],

            handleFileSelect(event) {
                let files = event.target.files;
                Array.from(files).forEach(file => {
                    let reader = new FileReader();
                    reader.onload = e => {
                        this.images.push(e.target.result); // Agregar nuevas imágenes sin eliminar las anteriores
                    };
                    reader.readAsDataURL(file);
                });
            },

            removeImage(index) {
                this.images.splice(index, 1);

                // Restablecer el valor del input file para permitir cargar la misma imagen nuevamente
                this.$refs.fileInput.value = '';
            }
        };
    }
</script>
@stop

