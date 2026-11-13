@extends('adminlte::page')

@section('title', 'Agregar Fotos a Inmueble')

@section('content_header')
    <h1>Agregar Fotos a Inmueble: {{ $inmueble->titulo }}</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('inmuebles.storePhotos', $inmueble->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="imagenes">Seleccionar Fotos (Máximo 6MB por imagen):</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*">
                            </div>
                        </div>
                        @error('imagenes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('imagenes.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="preview" class="row">
                    </div>

                    <button type="submit" class="btn btn-primary">Subir Fotos</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#imagenes').on('change', function () {
                if (this.files && this.files.length > 0) {
                    for (let i = 0; i < this.files.length; i++) {
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            $('#preview').append(`
                                <div class="col-md-3 mb-3">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            `);
                        }
                        reader.readAsDataURL(this.files[i]);
                    }
                }
            });
        });
    </script>
@stop