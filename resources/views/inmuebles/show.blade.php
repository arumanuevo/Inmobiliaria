@extends('adminlte::page')

@section('title', 'Detalles del Inmueble')

@section('content_header')
    <h1>Detalles del Inmueble</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $inmueble->titulo }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Tipo de Operación:</strong> {{ ucfirst($inmueble->tipo_operacion) }}</p>
            <p><strong>Descripción:</strong> {{ $inmueble->descripcion }}</p>
            <p><strong>Precio:</strong> ${{ number_format($inmueble->precio,2) }}</p>
            <p><strong>Superficie Cubierta:</strong> {{ $inmueble->superficie_cubierta }} m²</p>
            <p><strong>Habitaciones:</strong> {{ $inmueble->habitaciones }}</p>
            <p><strong>Baños:</strong> {{ $inmueble->banos }}</p>
            <p><strong>Dirección:</strong> {{ $inmueble->direccion ?? 'No definida' }}</p>
            <p><strong>Ciudad:</strong> {{ $inmueble->ciudad ?? 'No definida' }}</p>
            <p><strong>Coordenadas:</strong> {{ $inmueble->coordenadas ?? 'No definida' }}</p>
            <!-- Agrega más campos según necesidad -->
        </div>
        <div class="card-footer">
            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-warning">Editar</a>
            <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">Volver al listado</a>
        </div>
    </div>
</div>
@stop
