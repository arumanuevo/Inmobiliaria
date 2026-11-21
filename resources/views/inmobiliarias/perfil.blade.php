@extends('layouts.panel')

@section('title', auth()->user()->hasRole('particular') ? 'Mi perfil' : 'Mi Inmobiliaria')

@section('content_header')
    <h1>{{ auth()->user()->hasRole('particular') ? 'Mi perfil público' : 'Perfil de mi inmobiliaria' }}</h1>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  .card-hero > .card-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    color: #fff;
    border: none;
    border-radius: 16px 16px 0 0;
    padding: 16px 22px;
  }
  .card-hero > .card-header .card-title { color: #fff; font-weight: 700; font-size: 16px; margin: 0; }
  .card { border-radius: 16px; }
  .section-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em;
    color: #6366f1;
    margin: 26px 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eef0f6;
  }
  .section-title:first-of-type { margin-top: 0; }
  .section-title i { font-size: 13px; }
  .stat-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f3f4f6; border: 1px solid #e5e7eb;
    border-radius: 999px; padding: 6px 13px;
    font-size: 13px; font-weight: 600; color: #344054;
    margin: 2px 4px 2px 0;
  }
  .stat-chip i { color: #6366f1; }
  .btn-guardar {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #d946ef 100%);
    border: none; color: #fff; font-weight: 700;
    border-radius: 999px; padding: 11px 30px;
    box-shadow: 0 10px 24px rgba(124, 92, 246, .38);
    transition: filter .18s ease, transform .18s ease;
  }
  .btn-guardar:hover { filter: brightness(1.1); transform: translateY(-1px); color: #fff; }
  .btn-volver { border-radius: 999px; font-weight: 600; padding: 11px 24px; }
  #mapa-perfil { height: 260px; border-radius: 12px; border: 1px solid #e5e7eb; }
  #resultados-mapa { z-index: 1050; max-height: 240px; overflow-y: auto; border-radius: 0 0 12px 12px; }
  .geo-bar .form-control { border-radius: 12px 0 0 12px; }
  .geo-bar .btn { border-radius: 0 12px 12px 0; }
  .preview-img { border-radius: 10px; border: 1px solid #e5e7eb; }
  .url-chip {
    display: inline-block; background: #eef2ff; border: 1px solid #c7d2fe; color: #3730a3;
    border-radius: 999px; padding: 5px 12px; font-size: 12.5px; font-weight: 600;
    text-decoration: none;
  }
  .url-chip:hover { background: #e0e7ff; color: #312e81; }
  input[type="color"].form-control { height: 42px; padding: 4px 8px; cursor: pointer; }
</style>
@stop

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Columna izquierda: estado, plan y pagos -->
        <div class="col-lg-4">
            <div class="card card-hero">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card me-2"></i>Estado de la cuenta</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted d-block mb-1 small fw-semibold">ESTADO</span>
                        @if($inmobiliaria->estado === 'activa')
                            <span class="badge badge-success">Activa</span>
                        @elseif($inmobiliaria->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente de aprobación</span>
                        @else
                            <span class="badge badge-danger">Suspendida</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block mb-1 small fw-semibold">PLAN</span>
                        <span class="stat-chip"><i class="fas fa-layer-group"></i>{{ $inmobiliaria->plan->nombre ?? 'Sin plan' }}</span>
                        <span class="stat-chip"><i class="fas fa-key"></i>{{ $inmobiliaria->limiteInmuebles() }} publicaciones</span>
                        <span class="stat-chip"><i class="fas fa-star"></i>{{ $inmobiliaria->limiteDestacados() }} destacadas</span>
                        @if($inmobiliaria->plan_vence_at)
                            <span class="stat-chip"><i class="fas fa-calendar"></i>Vence {{ $inmobiliaria->plan_vence_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block mb-1 small fw-semibold">URL PÚBLICA</span>
                        <a href="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" target="_blank" class="url-chip">
                            <i class="fas fa-external-link-alt me-1"></i>/inmobiliaria/{{ $inmobiliaria->slug }}
                        </a>
                        <a href="{{ route('inmobiliaria.preview') }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold ms-1" title="Ver tu perfil como visitante, incluyendo borradores">
                            <i class="fas fa-eye me-1"></i> Vista previa
                        </a>
                        <button type="button" id="btn-copy-perfil" data-url="{{ route('inmobiliarias.show', $inmobiliaria->slug) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Copiar el enlace de tu perfil para compartirlo">
                            <i class="fas fa-link me-1"></i> Copiar enlace
                        </button>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('boletos.mios') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold"><i class="fas fa-receipt me-1"></i> Mis boletos</a>
                    </div>
                    @if($inmobiliaria->logo)
                        <div>
                            <span class="text-muted d-block mb-1 small fw-semibold">LOGO ACTUAL</span>
                            <img src="{{ asset($inmobiliaria->logo) }}" alt="Logo" class="preview-img" style="max-height: 60px; background:#fff; padding:4px;">
                        </div>
                    @endif
                </div>
            </div>

            @if(config('services.mercadopago.access_token'))
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-credit-card me-2"></i>Plan y pagos</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('mp.pagar') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="plan_id">Mejorar plan</label>
                            <select name="plan_id" id="plan_id" class="form-select mb-3">
                                @foreach(\App\Models\Plan::where('activo', 1)->where('slug', '!=', 'particular')->orderBy('precio_mensual')->get() as $p)
                                    <option value="{{ $p->id }}" {{ $inmobiliaria->plan_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }} - @dinero($p->precio_mensual, 0)/mes
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold"><i class="fas fa-lock me-1"></i> Pagar con Mercado Pago</button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna derecha: formulario del perfil -->
        <div class="col-lg-8">
            <div class="card card-hero">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-store me-2"></i>{{ auth()->user()->hasRole('particular') ? 'Datos de mi perfil' : 'Datos de la inmobiliaria' }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('inmobiliaria.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h6 class="section-title"><i class="fas fa-fingerprint"></i> Identidad</h6>
                        <div class="form-group">
                            <label for="nombre_comercial">{{ auth()->user()->hasRole('particular') ? 'Tu nombre (así figura en el portal)' : 'Nombre comercial' }}</label>
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
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="color_tema">Color del perfil</label>
                                <input type="color" name="color_tema" id="color_tema" class="form-control"
                                       value="{{ old('color_tema', $inmobiliaria->color_tema ?: '#6366f1') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="logo">Logo (png, jpg, svg)</label>
                                <input type="file" name="logo" id="logo" class="form-control-file" accept="image/*">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ old('descripcion', $inmobiliaria->descripcion) }}</textarea>
                        </div>

                        <h6 class="section-title"><i class="fas fa-palette"></i> Personalización (opcional)</h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="tema">Estilo de tu perfil público</label>
                                <select name="tema" id="tema" class="form-select">
                                    @foreach(\App\Models\Inmobiliaria::temas() as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('tema', $inmobiliaria->tema ?: 'moderno') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="mensaje_destacado">Mensaje destacado</label>
                                <input type="text" name="mensaje_destacado" id="mensaje_destacado" class="form-control" maxlength="255"
                                       value="{{ old('mensaje_destacado', $inmobiliaria->mensaje_destacado) }}"
                                       placeholder="Ej: ¡Ahora con financiación en 12 cuotas!">
                                <small class="text-muted">Se muestra como aviso en tu perfil. Vacío = no se muestra.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Secciones visibles en tu perfil público</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mostrar_operaciones" id="mostrar_operaciones" value="1" {{ old('mostrar_operaciones', $inmobiliaria->mostrar_operaciones ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mostrar_operaciones">Contador de operaciones concretadas (ej: "12 operaciones")</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mostrar_mapa" id="mostrar_mapa" value="1" {{ old('mostrar_mapa', $inmobiliaria->mostrar_mapa ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mostrar_mapa">Mapa de la oficina (si cargaste la ubicación)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="mostrar_redes" id="mostrar_redes" value="1" {{ old('mostrar_redes', $inmobiliaria->mostrar_redes ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mostrar_redes">Redes sociales y sitio web</label>
                            </div>
                        </div>

                        <h6 class="section-title"><i class="fas fa-map-marker-alt"></i> Ubicación y contacto</h6>
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

                        <h6 class="section-title"><i class="fas fa-map"></i> Oficina en el mapa</h6>
                        <div class="form-group">
                            <label for="busqueda-mapa">Buscar dirección</label>
                            <div class="input-group geo-bar mb-1">
                                <input type="text" id="busqueda-mapa" class="form-control" placeholder="Buscar dirección en Argentina... (ej: Av. Sarmiento 100, tu ciudad)" autocomplete="off">
                                <button type="button" id="btn-buscar-mapa" class="btn btn-outline-primary">Buscar</button>
                            </div>
                            <div id="resultados-mapa" class="list-group position-absolute w-100 shadow" style="display:none;"></div>
                            <div class="row mt-2 g-2">
                                <div class="col-6"><input type="text" name="lat" id="lat" class="form-control form-control-sm" placeholder="Latitud" value="{{ old('lat', $inmobiliaria->lat) }}"></div>
                                <div class="col-6"><input type="text" name="lng" id="lng" class="form-control form-control-sm" placeholder="Longitud" value="{{ old('lng', $inmobiliaria->lng) }}"></div>
                            </div>
                            <div id="mapa-perfil" class="mt-2"></div>
                            <small class="text-muted">Escribí la dirección y elegí un resultado (o apretá Buscar), o hacé clic directamente en el mapa. Las coordenadas se completan solas.</small>
                        </div>

                        <h6 class="section-title"><i class="fas fa-images"></i> Imágenes del perfil</h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="imagen_portada">Imagen de portada (jumbotron de tu perfil)</label>
                                <input type="file" name="imagen_portada" id="imagen_portada" class="form-control-file" accept="image/*">
                                @if($inmobiliaria->imagen_portada)
                                    <img src="{{ asset($inmobiliaria->imagen_portada) }}" class="preview-img mt-2" style="max-height: 90px;">
                                @endif
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="galeria">Galería del jumbotron (varias imágenes = carrusel, máx. 10)</label>
                                <input type="file" name="galeria[]" id="galeria" class="form-control-file" accept="image/*" multiple>
                                @if(!empty($inmobiliaria->galeria))
                                    <div class="d-flex flex-wrap mt-2">
                                        @foreach($inmobiliaria->galeria as $img)
                                            <img src="{{ asset($img) }}" class="preview-img me-1 mb-1" style="height: 60px;">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mt-4 pt-2" style="border-top: 1px solid #eef0f6;">
                            <button type="submit" class="btn btn-guardar"><i class="fas fa-save me-1"></i> Guardar cambios</button>
                            <a href="{{ route('inmuebles.index') }}" class="btn btn-outline-secondary btn-volver ms-2">Ir a mis inmuebles</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // Geocoder (Nominatim, solo Argentina) con autocompletado
    var inputBusqueda = document.getElementById('busqueda-mapa');
    var btnBuscar = document.getElementById('btn-buscar-mapa');
    var resultados = document.getElementById('resultados-mapa');
    function buscarGeo() {
        var q = inputBusqueda.value.trim();
        if (q.length < 3) { resultados.style.display = 'none'; resultados.innerHTML = ''; return; }
        resultados.innerHTML = '<div class="list-group-item small text-muted py-2 px-3">Buscando...</div>';
        resultados.style.display = 'block';
        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&countrycodes=ar&limit=5&addressdetails=1&q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'Accept-Language': 'es' }
        })
            .then(function (r) { if (!r.ok) { throw new Error('HTTP ' + r.status); } return r.json(); })
            .then(function (data) {
                resultados.innerHTML = '';
                if (!data.length) {
                    resultados.innerHTML = '<div class="list-group-item small text-muted py-2 px-3">Sin resultados. Probá con más detalle (calle y número, ciudad).</div>';
                    return;
                }
                data.forEach(function (item) {
                    var b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'list-group-item list-group-item-action py-2 px-3 small text-start';
                    b.textContent = item.display_name;
                    b.onclick = function () {
                        var lat = parseFloat(item.lat), lng = parseFloat(item.lon);
                        map.flyTo([lat, lng], 16);
                        setMarker(lat, lng);
                        resultados.style.display = 'none';
                        resultados.innerHTML = '';
                        inputBusqueda.value = '';
                    };
                    resultados.appendChild(b);
                });
            })
            .catch(function (err) {
                resultados.innerHTML = '<div class="list-group-item small text-danger py-2 px-3">Error al buscar (' + err.message + '). Probá de nuevo o usá el clic en el mapa.</div>';
            });
    }
    var timer = null;
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(buscarGeo, 400);
        });
        inputBusqueda.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); clearTimeout(timer); buscarGeo(); }
        });
    }
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function (e) { e.preventDefault(); clearTimeout(timer); buscarGeo(); });
    }
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#busqueda-mapa') && !e.target.closest('#resultados-mapa')) {
            resultados.style.display = 'none';
        }
    });

    // Copiar enlace del perfil público para compartir
    var btnCopy = document.getElementById('btn-copy-perfil');
    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            var url = btnCopy.getAttribute('data-url');
            function ok() {
                btnCopy.innerHTML = '<i class="fas fa-check me-1"></i> ¡Copiado!';
                setTimeout(function () { btnCopy.innerHTML = '<i class="fas fa-link me-1"></i> Copiar enlace'; }, 2000);
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(ok).catch(ok);
            } else {
                var ta = document.createElement('textarea');
                ta.value = url; document.body.appendChild(ta); ta.select();
                try { document.execCommand('copy'); } catch (err) {}
                document.body.removeChild(ta); ok();
            }
        });
    }
});
</script>
@stop
