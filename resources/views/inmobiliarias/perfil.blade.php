@extends('layouts.panel')

@section('title', 'Mi Inmobiliaria')

@section('content_header')
    <h1>Perfil de mi inmobiliaria</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Estado de la cuenta</h3>
                </div>
                <div class="card-body">
                    <p><strong>Estado:</strong>
                        @if($inmobiliaria->estado === 'activa')
                            <span class="badge badge-success">Activa</span>
                        @elseif($inmobiliaria->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente de aprobación</span>
                        @else
                            <span class="badge badge-danger">Suspendida</span>
                        @endif
                    </p>
                    <p><strong>Plan:</strong> {{ $inmobiliaria->plan->nombre ?? 'Sin plan' }}
                        ({{ $inmobiliaria->limiteInmuebles() }} publicaciones, {{ $inmobiliaria->limiteDestacados() }} destacadas)</p>
                    @if($inmobiliaria->plan_vence_at)
                        <p><strong>Vence:</strong> {{ $inmobiliaria->plan_vence_at->format('d/m/Y') }}</p>
                    @endif
                    <p><strong>URL pública:</strong>
                        <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" target="_blank">{{ route('inmobiliarias.show', $inmobiliaria->slug) }}</a>
                    </p>
                    <p><a href="{{ route('boletos.mios') }}" class="btn btn-outline-primary btn-sm">Mis boletos</a></p>
                    @if($inmobiliaria->logo)
                        <p><strong>Logo actual:</strong><br>
                            <img src="{{ asset($inmobiliaria->logo) }}" alt="Logo" style="max-height: 60px;">
                        </p>
                    @endif
                </div>
            </div>

            @if(config('services.mercadopago.access_token'))
            <div class="card card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Plan y pagos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('mp.pagar') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <select name="plan_id" class="form-control mb-2">
                                @foreach(\App\Models\Plan::where('activo', 1)->orderBy('precio_mensual')->get() as $p)
                                    <option value="{{ $p->id }}" {{ $inmobiliaria->plan_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }} - ${{ number_format($p->precio_mensual, 0) }}/mes
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-block w-100">Pagar / mejorar plan con Mercado Pago</button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos de la inmobiliaria</h3>
                </div>
                <div class="card-body" style="max-height: 75vh; overflow-y: auto;">
                    <form action="{{ route('inmobiliaria.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nombre_comercial">Nombre comercial</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control"
                                   value="{{ old('nombre_comercial', $inmobiliaria->nombre_comercial) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="slug">URL pública de tu perfil</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/inmobiliaria') }}/</span>
                                <input type="text" name="slug" id="slug" class="form-control"
                                       value="{{ old('slug', $inmobiliaria->slug) }}" required pattern="[a-z0-9-]+"
                                       title="Solo minúsculas, números y guiones">
                            </div>
                            <small class="text-muted">Solo minúsculas, números y guiones. Ej: mi-inmobiliaria. Cambiarla rompe los enlaces viejos.</small>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion', $inmobiliaria->descripcion) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" name="direccion" id="direccion" class="form-control"
                                       value="{{ old('direccion', $inmobiliaria->direccion) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="codigo_postal">Código postal</label>
                                <input type="text" name="codigo_postal" id="codigo_postal" class="form-control"
                                       value="{{ old('codigo_postal', $inmobiliaria->codigo_postal) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="ciudad">Ciudad</label>
                                <input type="text" name="ciudad" id="ciudad" class="form-control"
                                       value="{{ old('ciudad', $inmobiliaria->ciudad) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="provincia">Provincia</label>
                                <input type="text" name="provincia" id="provincia" class="form-control"
                                       value="{{ old('provincia', $inmobiliaria->provincia) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="zonas_cobertura">Zonas de cobertura (ciudades/partidos que atienden)</label>
                            <input type="text" name="zonas_cobertura" id="zonas_cobertura" class="form-control"
                                   value="{{ old('zonas_cobertura', $inmobiliaria->zonas_cobertura) }}" placeholder="Ej: tu ciudad y alrededores">
                            <small class="text-muted">Se usa para buscar tu inmobiliaria por zona en el directorio del portal.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Ubicación de la oficina en el mapa</label>
                                <div class="row">
                                    <div class="col-6"><input type="text" name="lat" id="lat" class="form-control" placeholder="Latitud" value="{{ old('lat', $inmobiliaria->lat) }}"></div>
                                    <div class="col-6"><input type="text" name="lng" id="lng" class="form-control" placeholder="Longitud" value="{{ old('lng', $inmobiliaria->lng) }}"></div>
                                </div>
                                <div id="mapa-perfil" style="height: 240px; border-radius: 8px; border: 1px solid #ccc;" class="mt-2"></div>
                                <small class="text-muted">Hacé clic en el mapa para marcar la oficina.</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="imagen_portada">Imagen de portada (jumbotron de tu perfil)</label>
                                    <input type="file" name="imagen_portada" id="imagen_portada" class="form-control-file" accept="image/*">
                                    @if($inmobiliaria->imagen_portada)
                                        <img src="{{ asset($inmobiliaria->imagen_portada) }}" class="img-thumbnail mt-2" style="max-height: 90px;">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="galeria">Galería del jumbotron (varias imágenes = carrusel, máx. 10)</label>
                                    <input type="file" name="galeria[]" id="galeria" class="form-control-file" accept="image/*" multiple>
                                    @if(!empty($inmobiliaria->galeria))
                                        <div class="d-flex flex-wrap mt-2">
                                            @foreach($inmobiliaria->galeria as $img)
                                                <img src="{{ asset($img) }}" class="img-thumbnail me-1 mb-1" style="height: 60px;">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control"
                                       value="{{ old('telefono', $inmobiliaria->telefono) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="whatsapp">WhatsApp (con código de país, sin +)</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control"
                                       value="{{ old('whatsapp', $inmobiliaria->whatsapp) }}" placeholder="Ej: 5492364123456">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Email de contacto</label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="{{ old('email', $inmobiliaria->email) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="sitio_web">Sitio web</label>
                                <input type="text" name="sitio_web" id="sitio_web" class="form-control"
                                       value="{{ old('sitio_web', $inmobiliaria->sitio_web) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="facebook">Facebook</label>
                                <input type="text" name="facebook" id="facebook" class="form-control"
                                       value="{{ old('facebook', $inmobiliaria->facebook) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="instagram">Instagram</label>
                                <input type="text" name="instagram" id="instagram" class="form-control"
                                       value="{{ old('instagram', $inmobiliaria->instagram) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="color_tema">Color del perfil</label>
                                <input type="color" name="color_tema" id="color_tema" class="form-control"
                                       value="{{ old('color_tema', $inmobiliaria->color_tema ?: '#0d6efd') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="logo">Logo (png, jpg, svg)</label>
                                <input type="file" name="logo" id="logo" class="form-control-file" accept="image/*">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">Ir a mis inmuebles</a>
                        </div>
                    </form>
                </div>
            </div>
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
    var div = document.getElementById('mapa-perfil');
    if (!div || typeof L === 'undefined') { return; }
    var latInput = document.getElementById('lat');
    var lngInput = document.getElementById('lng');
    var lat0 = parseFloat(latInput.value), lng0 = parseFloat(lngInput.value);
    var tieneCoords = !isNaN(lat0) && !isNaN(lng0);
    var map = L.map('mapa-perfil').setView(tieneCoords ? [lat0, lng0] : [-38.5, -64.5], tieneCoords ? 14 : 4);
    var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, maxNativeZoom: 18, attribution: 'Tiles &copy; Esri' });
    L.control.layers({ 'Callejera': osm, 'Satelital': sat }, null, { position: 'topright' }).addTo(map);
    var marker = null;
    function setMarker(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        if (marker) { map.removeLayer(marker); }
        marker = L.marker([lat, lng]).addTo(map);
    }
    if (tieneCoords) { setMarker(lat0, lng0); }
    map.on('click', function (e) { setMarker(e.latlng.lat, e.latlng.lng); });
});
</script>
@stop
