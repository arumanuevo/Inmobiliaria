@extends('layouts.panel')

@section('title', isset($inmobiliariaAsistida) ? 'Cargar propiedad - ' . $inmobiliariaAsistida->nombre_comercial : 'Publicar Inmueble')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                {{ isset($inmobiliariaAsistida) ? 'Cargar propiedad para ' . $inmobiliariaAsistida->nombre_comercial : 'Publicar nueva propiedad' }}
            </h1>
            <p class="text-muted small mb-0">Completá los datos técnicos, fotos y ubicación para publicar en el portal.</p>
        </div>
        <div>
            <a href="{{ route('inmuebles.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver a mis propiedades
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    {{-- Banner motivacional para Particulares --}}
    @if(auth()->user()->hasRole('particular') && !isset($inmobiliariaAsistida))
        <div class="card mb-4 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="me-3 d-none d-sm-block">
                        <span class="badge bg-white text-primary p-3 rounded-circle" style="font-size: 24px;">
                            <i class="fas fa-home"></i>
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-success mb-2 px-3 py-1"><i class="fas fa-check-circle me-1"></i> Publicación Particular Gratuita</span>
                        <h4 class="fw-bold mb-1">¡Estás a punto de publicar tu casa o departamento gratis!</h4>
                        <p class="mb-0 small opacity-90">Tu aviso no paga comisión inmobiliaria y se publicará inmediatamente con el distintivo <strong>"Dueño Directo"</strong> para que compradores e inquilinos te contacten al instante.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($inmobiliariaAsistida))
        <div class="alert alert-info shadow-sm border-0 mb-4 d-flex align-items-center">
            <i class="fas fa-hands-helping fa-2x me-3 text-info"></i>
            <div>
                <strong>Modo Carga Asistida:</strong> esta propiedad se publicará a nombre de la inmobiliaria
                <strong>{{ $inmobiliariaAsistida->nombre_comercial }}</strong> (perfil público: <code>/inmobiliaria/{{ $inmobiliariaAsistida->slug }}</code>).
            </div>
        </div>
    @endif
    @if(isset($solicitud) && $solicitud)
        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
        <div class="alert alert-warning shadow-sm border-0 mb-4">
            <i class="fas fa-clipboard-list me-2"></i> Atendiendo <strong>solicitud #{{ $solicitud->id }}</strong>:
            “{{ $solicitud->titulo ?: 'sin título' }}” — {{ \Illuminate\Support\Str::limit($solicitud->detalle, 120) }}
        </div>
    @endif

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Por favor revisá los siguientes campos:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($inmobiliariaAsistida) ? route('admin.carga.store', $inmobiliariaAsistida->id) : route('inmuebles.store') }}" method="POST" enctype="multipart/form-data" id="form-inmueble">
        @csrf

        {{-- PASO 1: Tipo de Operación y Tipo de Propiedad --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                <h5 class="mb-0 fw-bold text-dark">¿Qué vas a publicar?</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Operación <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline p-0 m-0 flex-fill">
                                <input type="radio" class="btn-check" name="tipo_operacion" id="op_venta" value="venta" {{ old('tipo_operacion', 'venta') === 'venta' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="op_venta">
                                    <i class="fas fa-tag fa-lg d-block mb-1"></i> En Venta
                                </label>
                            </div>
                            <div class="form-check form-check-inline p-0 m-0 flex-fill">
                                <input type="radio" class="btn-check" name="tipo_operacion" id="op_alquiler" value="alquiler" {{ old('tipo_operacion') === 'alquiler' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="op_alquiler">
                                    <i class="fas fa-key fa-lg d-block mb-1"></i> En Alquiler
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_propiedad" class="form-label fw-bold">Tipo de Propiedad</label>
                        <select name="tipo_propiedad" id="tipo_propiedad" class="form-select form-select-lg">
                            <option value="">Seleccione el tipo de inmueble...</option>
                            @foreach(\App\Models\Inmueble::gruposTipos() as $grupo => $opciones)
                                <optgroup label="{{ $grupo }}">
                                    @foreach($opciones as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('tipo_propiedad') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="form-text small">Elegí la categoría más adecuada: vivienda, comercial/industrial (local, oficina, galpón) o terreno.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="uso_detalle" class="form-label fw-bold">Uso / habilitación <small class="text-muted fw-normal">(si es comercial: kiosco, tienda, gastronomía…)</small></label>
                        <input type="text" name="uso_detalle" id="uso_detalle" class="form-control" maxlength="120" list="uso-comercial-suggest" value="{{ old('uso_detalle') }}" placeholder="Ej: Kiosco, tienda de ropa, oficina...">
                        <datalist id="uso-comercial-suggest">
                            @foreach(\App\Models\Inmueble::usosComercialesSugeridos() as $u)
                                <option value="{{ $u }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 2: Título, Precios y Descripción --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                <h5 class="mb-0 fw-bold text-dark">Título, Precio y Descripción</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="titulo" class="form-label fw-bold">Título de la publicación <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo" class="form-control form-control-lg" required value="{{ old('titulo') }}" placeholder="Ej: Hermosa casa de 3 ambientes con patio y parrilla en excelente zona">
                        <div class="form-text small">Un título claro y atractivo aumenta las consultas de los interesados.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="moneda" class="form-label fw-bold">Moneda <span class="text-danger">*</span></label>
                        <select name="moneda" id="moneda" class="form-select form-select-lg">
                            <option value="ARS" {{ old('moneda', 'ARS') == 'ARS' ? 'selected' : '' }}>Pesos Argentinos (ARS $)</option>
                            <option value="USD" {{ old('moneda') == 'USD' ? 'selected' : '' }}>Dólares Estadounidenses (USD U$S)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="precio" class="form-label fw-bold">Precio <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light fw-bold" id="simbolo-moneda">$</span>
                            <input type="number" step="0.01" name="precio" id="precio" class="form-control" required value="{{ old('precio') }}" placeholder="0.00">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="expensas" class="form-label fw-bold">Expensas (opcional)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" step="0.01" name="expensas" id="expensas" class="form-control" min="0" value="{{ old('expensas') }}" placeholder="0.00">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label fw-bold">Descripción detallada <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="6" required placeholder="Contá con detalle las características de la propiedad: luminosidad, estado de conservación, distribución de ambientes, cercanía a transportes, colegios o parques...">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 3: Medidas, Distribución y Ambientes --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                <h5 class="mb-0 fw-bold text-dark">Medidas y Características</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <label for="superficie_cubierta" class="form-label fw-bold">Superficie Cubierta <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="superficie_cubierta" id="superficie_cubierta" class="form-control" required value="{{ old('superficie_cubierta') }}" placeholder="Ej: 85">
                            <span class="input-group-text bg-light">m²</span>
                        </div>
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="ambientes" class="form-label fw-bold">Ambientes</label>
                        <input type="number" name="ambientes" id="ambientes" class="form-control" min="0" value="{{ old('ambientes') }}" placeholder="Ej: 3">
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="habitaciones" class="form-label fw-bold">Dormitorios <span class="text-danger">*</span></label>
                        <input type="number" name="habitaciones" id="habitaciones" class="form-control" required min="0" value="{{ old('habitaciones') }}" placeholder="Ej: 2">
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="banos" class="form-label fw-bold">Baños <span class="text-danger">*</span></label>
                        <input type="number" name="banos" id="banos" class="form-control" required min="0" value="{{ old('banos') }}" placeholder="Ej: 1">
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="cocheras" class="form-label fw-bold">Cocheras</label>
                        <input type="number" name="cocheras" id="cocheras" class="form-control" min="0" value="{{ old('cocheras', 0) }}" placeholder="0">
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="estado_propiedad" class="form-label fw-bold">Estado</label>
                        <select name="estado_propiedad" id="estado_propiedad" class="form-select">
                            <option value="">Seleccione...</option>
                            @foreach(['a_estrenar'=>'A estrenar','usado'=>'Usado / muy bueno','a_refaccionar'=>'A refaccionar','en_construccion'=>'En construcción'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('estado_propiedad') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="orientacion" class="form-label fw-bold">Orientación</label>
                        <select name="orientacion" id="orientacion" class="form-select">
                            <option value="">-</option>
                            @foreach(['Norte','Noreste','Este','Sudeste','Sur','Suroeste','Oeste','Noroeste'] as $o)
                                <option value="{{ $o }}" {{ old('orientacion') == $o ? 'selected' : '' }}>{{ $o }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label for="anio_construccion" class="form-label fw-bold">Año de Construcción</label>
                        <input type="number" name="anio_construccion" id="anio_construccion" class="form-control" value="{{ old('anio_construccion') }}" placeholder="Ej: 2015">
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 4: Ubicación y Mapa Geográfico --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">4</span>
                <h5 class="mb-0 fw-bold text-dark">Ubicación y Mapa Geográfico</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="ciudad" class="form-label fw-bold">Ciudad o Pueblo <span class="text-danger">*</span></label>
                        <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{ old('ciudad') }}" placeholder="Ej: Chivilcoy, Alberti, Suipacha, etc.">
                    </div>
                    <div class="col-md-6">
                        <label for="direccion" class="form-label fw-bold">Dirección / Calle y Altura</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}" placeholder="Ej: Av. Soarez 250">
                    </div>
                </div>

                {{-- Buscador con Geocoder Nominatim --}}
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary"><i class="fas fa-search-location me-1"></i> Buscar en el mapa interactivo</label>
                    <div class="input-group">
                        <input type="text" id="busqueda-mapa" class="form-control" placeholder="Escribí una dirección o ciudad para ubicar el pin en el mapa... (ej: Sarmiento 150, Chivilcoy)">
                        <button type="button" id="btn-buscar-mapa" class="btn btn-primary"><i class="fas fa-search me-1"></i> Ubicar</button>
                    </div>
                    <div id="resultados-mapa" class="list-group mt-1 shadow-sm" style="max-height: 200px; overflow-y: auto;"></div>
                </div>

                {{-- Contenedor del Mapa Leaflet --}}
                <div class="mb-3">
                    <div id="mapa-inmueble" style="height: 340px; border-radius: 8px; border: 1px solid #dee2e6;"></div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Podés hacer clic en el mapa para ajustar la posición exacta del marcador.</small>
                        <div class="d-flex align-items-center">
                            <label for="coordenadas" class="small text-muted me-2 mb-0">Coordenadas:</label>
                            <input type="text" name="coordenadas" id="coordenadas" class="form-control form-control-sm text-center" style="width: 220px;" value="{{ old('coordenadas') }}" placeholder="Latitud, Longitud" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 5: Prestaciones y Comodidades --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">5</span>
                <h5 class="mb-0 fw-bold text-dark">Comodidades y Prestaciones</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Marcá las comodidades con las que cuenta el inmueble para que aparezcan en los filtros de búsqueda:</p>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="form-check p-3 border rounded bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="piscina" id="piscina" value="1" {{ old('piscina') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="piscina">
                                🏊 Piscina / Pileta
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-check p-3 border rounded bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="jardin" id="jardin" value="1" {{ old('jardin') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="jardin">
                                🌳 Jardín / Parque
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-check p-3 border rounded bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="garaje" id="garaje" value="1" {{ old('garaje') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="garaje">
                                🚗 Garaje cubierto
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-check p-3 border rounded bg-light">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="ascensor" id="ascensor" value="1" {{ old('ascensor') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="ascensor">
                                🛗 Ascensor
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 6: Fotos de la Publicación --}}
        <div class="card shadow-sm border-0 mb-4" x-data="imageUploader()">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">6</span>
                    <h5 class="mb-0 fw-bold text-dark">Fotos de la Propiedad</h5>
                </div>
                <span class="badge bg-light text-muted border">JPG, PNG o WEBP</span>
            </div>
            <div class="card-body p-4 text-center">
                <div class="p-4 border-2 border-dashed rounded bg-light mb-3" style="border: 2px dashed #0d6efd; cursor: pointer;" @click="$refs.fileInput.click()">
                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                    <h5 class="fw-bold mb-1">Hacé clic aquí para elegir las fotos de tu propiedad</h5>
                    <p class="text-muted small mb-0">Podés seleccionar varias fotos a la vez desde tu celular o computadora. (Límite: hasta 5 fotos para cuentas particulares).</p>
                    <input type="file" name="imagenes[]" class="d-none" multiple accept="image/*" @change="handleFileSelect($event)" x-ref="fileInput">
                </div>

                {{-- Vista previa de fotos seleccionadas --}}
                <div class="d-flex flex-wrap gap-3 justify-content-center" x-show="images.length > 0">
                    <template x-for="(image, index) in images" :key="index">
                        <div class="position-relative shadow-sm rounded overflow-hidden" style="width: 130px; height: 110px;">
                            <img :src="image" class="w-100 h-100 object-fit-cover" alt="Preview">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 rounded-circle" style="width: 24px; height: 24px; line-height: 22px;" @click="removeImage(index)" title="Quitar foto">
                                &times;
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- PASO 7: Datos de Contacto del Propietario --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <span class="badge bg-primary rounded-circle me-2 p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">7</span>
                <h5 class="mb-0 fw-bold text-dark">Datos de Contacto y Seguridad</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Estos datos permiten verificar la titularidad del aviso y asegurar un portal confiable:</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="propnombre" class="form-label fw-bold">Nombre del Titular</label>
                        <input type="text" name="propnombre" id="propnombre" class="form-control" value="{{ old('propnombre', auth()->user()->name) }}" placeholder="Nombre y Apellido">
                    </div>
                    <div class="col-md-4">
                        <label for="propdni" class="form-label fw-bold">DNI del Titular</label>
                        <input type="text" name="propdni" id="propdni" class="form-control" value="{{ old('propdni') }}" placeholder="Número de documento">
                    </div>
                    <div class="col-md-4">
                        <label for="proptel" class="form-label fw-bold">Teléfono / WhatsApp de contacto</label>
                        <input type="text" name="proptel" id="proptel" class="form-control" value="{{ old('proptel', auth()->user()->telefono) }}" placeholder="Ej: 11 1234-5678">
                    </div>
                    <div class="col-md-6">
                        <label for="propemail" class="form-label fw-bold">Email de contacto</label>
                        <input type="email" name="propemail" id="propemail" class="form-control" value="{{ old('propemail', auth()->user()->email) }}" placeholder="email@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label for="propdom" class="form-label fw-bold">Domicilio legal del titular</label>
                        <input type="text" name="propdom" id="propdom" class="form-control" value="{{ old('propdom') }}" placeholder="Calle, número, localidad">
                    </div>
                </div>

                {{-- Proximidades / ventajas del entorno --}}
                @include('inmuebles.partials.proximidades_form')

                {{-- Declaración Jurada Legal --}}
                <div class="p-3 bg-light border border-primary rounded mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="declaracion_propietario" value="1" id="declaracion_propietario" {{ old('declaracion_propietario') ? 'checked' : '' }} required>
                        <label class="form-check-label fw-bold text-dark" for="declaracion_propietario">
                            Declaro bajo juramento que soy propietario/a de esta propiedad o que cuento con la correspondiente autorización formal para su publicación, y que los datos y fotos ingresados son veraces y actualizados. (Ver <a href="{{ route('aviso-legal') }}" target="_blank">Aviso Legal</a>).
                        </label>
                    </div>
                    @error('declaracion_propietario')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Visibilidad pública --}}
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="publicado" value="1" id="publicado" checked>
                    <label class="form-check-label fw-bold" for="publicado">Publicar inmediatamente en el portal público</label>
                </div>
            </div>
        </div>

        {{-- Botones de Publicación --}}
        <div class="card shadow-sm border-0 mb-5 bg-white">
            <div class="card-body p-4 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow-sm me-2">
                    <i class="fas fa-check-circle me-2"></i> Publicar Propiedad Ahora
                </button>
                <a href="{{ route('inmuebles.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-3">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .object-fit-cover { object-fit: cover; }
    .border-dashed { border-style: dashed !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cambio visual de símbolo de moneda
    var selectMoneda = document.getElementById('moneda');
    var simbolo = document.getElementById('simbolo-moneda');
    if (selectMoneda && simbolo) {
        selectMoneda.addEventListener('change', function() {
            simbolo.textContent = this.value === 'USD' ? 'U$S' : '$';
        });
    }

    // Inicialización del Mapa
    var contenedor = document.getElementById('mapa-inmueble');
    var inputCoord = document.getElementById('coordenadas');
    var inputCiudad = document.getElementById('ciudad');
    if (!contenedor) { return; }
    if (typeof L === 'undefined') {
        contenedor.innerHTML = '<div class="p-3 text-danger small">No se pudo cargar el mapa. Recargá la página.</div>';
        return;
    }

    var centro = [-34.897, -60.019], zoom = 7;
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
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    var satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });
    L.control.layers({ 'Callejera': osm, 'Satelital': satelite }, null, { position: 'topright' }).addTo(map);

    var marker = null;
    function setCoords(lat, lng) {
        if (inputCoord) { inputCoord.value = lat.toFixed(6) + ', ' + lng.toFixed(6); }
        if (marker) { map.removeLayer(marker); }
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            setCoords(pos.lat, pos.lng);
        });
    }

    if (!isNaN(lat0) && !isNaN(lng0)) { setCoords(lat0, lng0); }
    map.on('click', function (e) { setCoords(e.latlng.lat, e.latlng.lng); });

    // Geocodificación Nominatim para ubicar dirección
    var inputBusqueda = document.getElementById('busqueda-mapa');
    var btnBuscar = document.getElementById('btn-buscar-mapa');
    var resultados = document.getElementById('resultados-mapa');

    function buscar() {
        var q = inputBusqueda.value.trim();
        if (q.length < 3) { resultados.innerHTML = ''; return; }
        resultados.innerHTML = '<div class="list-group-item small text-muted py-2 px-3">Buscando dirección...</div>';
        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&countrycodes=ar&limit=5&addressdetails=1&q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'Accept-Language': 'es' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            resultados.innerHTML = '';
            if (!data.length) {
                resultados.innerHTML = '<div class="list-group-item small text-muted py-2 px-3">No se encontraron resultados para esa dirección.</div>';
                return;
            }
            data.forEach(function (item) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'list-group-item list-group-item-action py-2 px-3 small text-start';
                b.innerHTML = '<i class="fas fa-map-marker-alt text-primary me-2"></i>' + item.display_name;
                b.onclick = function () {
                    var lat = parseFloat(item.lat), lng = parseFloat(item.lon);
                    map.flyTo([lat, lng], 16);
                    setCoords(lat, lng);
                    resultados.innerHTML = '';
                    inputBusqueda.value = '';
                    // Si el campo ciudad está vacío y Nominatim devolvió ciudad/pueblo, completarlo
                    if (inputCiudad && !inputCiudad.value) {
                        var ciudadDetectada = item.address.city || item.address.town || item.address.village || item.address.municipality || '';
                        if (ciudadDetectada) { inputCiudad.value = ciudadDetectada; }
                    }
                };
                resultados.appendChild(b);
            });
        })
        .catch(function () {
            resultados.innerHTML = '<div class="list-group-item small text-danger py-2 px-3">Error al consultar el servicio geográfico. Podés hacer clic en el mapa.</div>';
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
        btnBuscar.addEventListener('click', function (e) { e.preventDefault(); buscar(); });
    }
});

function imageUploader() {
    return {
        images: [],
        handleFileSelect(event) {
            let files = event.target.files;
            Array.from(files).forEach(file => {
                let reader = new FileReader();
                reader.onload = e => {
                    this.images.push(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        },
        removeImage(index) {
            this.images.splice(index, 1);
        }
    };
}
</script>
@include('inmuebles.partials.proximidades_js')
@stop
