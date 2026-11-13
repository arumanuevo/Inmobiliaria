@extends('adminlte::page')

@section('title', 'Agregar Fotos a Inmueble')

@section('content_header')
    <h1>Agregar Fotos a Inmueble: {{ $inmueble->titulo }}</h1>
@stop

@section('content')
<style>
    .image-preview {
        display: flex;
        flex-wrap: wrap;
        max-height: 150px; /* Altura máxima para habilitar el scroll vertical */
        overflow-y: auto; /* Habilita el scroll vertical si es necesario */
        overflow-x: hidden; /* Oculta el scroll horizontal por defecto */
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

    .santi {
        height: 45px;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Cargar Imágenes ') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="loader loader-default " id="spin" data-text="Consultando..."></div>
                    <div class="container">
                        <form id="imageUploadForm" method="POST" action="{{ route('guardar_imagenes') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="inmueble_id" value="{{ $inmueble->id }}">
                            <div class="d-flex justify-content-between mb-3">
                                <label for="imagenesInput" class="btn btn-primary">
                                    <i class="bi bi-cloud-arrow-up"></i> Elegir archivos
                                    <input type="file" name="imagenes[]" accept=".*" multiple id="imagenesInput" style="display: none;">
                                </label>
                                <div>
                                    <button type="submit" id="cargarImagenesButton" class="btn btn-success santi" disabled>
                                        <i class="bi bi-archive"></i> Subir Fotos
                                    </button>
                                    <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary santi">Cancelar</a>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <!-- Mostrar imágenes seleccionadas -->
                        <div id="previewContainer">
                            <h5>Imágenes Seleccionadas:</h5>
                            <div id="imagePreview" class="image-preview">
                            </div>
                        </div>
                        <!-- Modal para mostrar el spinner -->
                        <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p>Cargando imágenes...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        const progressModal = document.getElementById('progressModal');
        const progressBar = document.getElementById('progressBar');
        const cargarImagenesButton = document.getElementById('cargarImagenesButton');
        const inputImagenes = document.getElementById('imagenesInput');
        const imagePreview = document.getElementById('imagePreview');
        const selectedFiles = [];
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

        function mostrarModal() {
            $("#spin").addClass("is-active");
        }

        function ocultarModal() {
            $("#spin").removeClass("is-active");
        }

        // Habilitar el botón de "Cargar Imágenes" cuando se seleccionan imágenes
        inputImagenes.addEventListener('change', function () {
            cargarImagenesButton.disabled = inputImagenes.files.length === 0;
        });

        // Agregar un evento para escuchar cambios en el input de imágenes
        inputImagenes.addEventListener('change', function () {
            for (let i = 0; i < inputImagenes.files.length; i++) {
                selectedFiles.push(inputImagenes.files[i]);
            }

            updateImagePreview();
        });

        function updateImagePreview() {
            imagePreview.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imageItem = document.createElement('div');
                    imageItem.classList.add('image-item');
                    imageItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="remove-button" data-index="${index}">×</button>
                    `;
                    imagePreview.appendChild(imageItem);
                };
                reader.readAsDataURL(file);
            });
        }

        // Agregar un evento para escuchar el envío del formulario
        document.getElementById('imageUploadForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío tradicional del formulario

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('inmueble_id', document.querySelector('input[name="inmueble_id"]').value);

            selectedFiles.forEach(file => {
                formData.append('imagenes[]', file);
            });

            // Limpiar el input de archivos para evitar enviar archivos duplicados
            inputImagenes.value = '';

            // Mostrar el modal de carga
            loadingModal.show();

            // Enviar el formulario con los archivos seleccionados
            fetch(event.target.action, {
                method: event.target.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      alert('Imágenes cargadas con éxito.');
                      selectedFiles.length = 0; // Limpiar la lista de archivos seleccionados
                      updateImagePreview();
                      window.location.href = "{{ route('inmuebles.index') }}"; // Redirigir a la ruta de índice
                  } else {
                      alert('Error al cargar las imágenes.');
                  }
                  // Ocultar el modal de carga
                  loadingModal.hide();
              }).catch(error => {
                  console.error('Error:', error);
                  alert('Error al cargar las imágenes.');
                  // Ocultar el modal de carga
                  loadingModal.hide();
              });
        });

        // Agregar un evento para eliminar imágenes del stack
        imagePreview.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-button')) {
                const index = event.target.getAttribute('data-index');
                selectedFiles.splice(index, 1);
                updateImagePreview();
            }
        });
    </script>
@stop



