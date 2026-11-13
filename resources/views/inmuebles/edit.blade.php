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
                    <input type="text" name="coordenadas" class="form-control" placeholder="Latitud, Longitud" value="{{ $inmueble->coordenadas }}">
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

