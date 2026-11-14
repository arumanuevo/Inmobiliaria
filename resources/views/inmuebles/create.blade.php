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
                @if(config('services.mapbox.token'))
                <div class="form-group">
                    <label>Ubicación en el mapa (buscá la dirección o hacé clic en el mapa)</label>
                    <div id="mapa-inmueble" style="height: 320px; border-radius: 6px; border: 1px solid #ccc;"></div>
                    <small class="text-muted">Se completa solo el campo Coordenadas (lat, lng).</small>
                </div>
                @endif

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
@if(config('services.mapbox.token'))
<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css">
@endif
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@if(config('services.mapbox.token'))
<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';
    var map = new mapboxgl.Map({
        container: 'mapa-inmueble',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [-64.5, -38.5],
        zoom: 4
    });
    var marker = null;
    function setCoords(lat, lng) {
        var c = document.getElementById('coordenadas');
        if (c) { c.value = lat.toFixed(6) + ', ' + lng.toFixed(6); }
        if (marker) { marker.remove(); }
        marker = new mapboxgl.Marker().setLngLat([lng, lat]).addTo(map);
    }
    map.on('click', function (e) { setCoords(e.lngLat.lat, e.lngLat.lng); });
    var geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        language: 'es',
        countries: 'ar',
        placeholder: 'Buscar dirección en Argentina...'
    });
    map.addControl(geocoder, 'top-left');
    geocoder.on('result', function (ev) {
        var lng = ev.result.center[0], lat = ev.result.center[1];
        map.flyTo({ center: [lng, lat], zoom: 15 });
        setCoords(lat, lng);
    });
});
</script>
@endif

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

