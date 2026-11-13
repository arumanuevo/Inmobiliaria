@extends('adminlte::page')

@section('title', 'Borrar Fotos de Inmueble')

@section('content_header')
    <h1>Borrar Fotos de Inmueble: {{ $inmueble->titulo }}</h1>
@stop

@section('content')
<style>
    .image-preview-container {
        max-height: 400px; /* Altura máxima para habilitar el scroll vertical */
        overflow-y: auto; /* Habilita el scroll vertical si es necesario */
        overflow-x: hidden; /* Oculta el scroll horizontal por defecto */
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 20px;
    }

    .image-preview {
        display: flex;
        flex-wrap: wrap;
    }

    .image-preview .image-item {
        position: relative;
        flex: 0 0 20%; /* Define el ancho de cada elemento, 20% para 5 columnas */
        margin: 5px;
    }

    .image-preview .image-item img {
        width: 100%;
        height: auto;
    }

    .image-preview .image-item .remove-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .text-center {
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @if($inmueble->fotos)
                    <div class="image-preview-container">
                        <div id="imagePreview" class="image-preview">
                            @foreach($inmueble->fotos as $foto)
                                <div class="image-item">
                                    <img src="{{ asset('images/fotosinmuebles/' . $foto) }}" alt="{{ $foto }}">
                                    <form action="{{ route('inmuebles.destroyPhoto', $inmueble) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="foto" value="{{ $foto }}">
                                        <button type="submit" class="remove-button" onclick="return confirm('¿Seguro de borrar esta foto?')">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p>No hay fotos para borrar.</p>
                @endif
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">Volver al Índice</a>
            </div>
        </div>
    </div>
</div>
@stop


