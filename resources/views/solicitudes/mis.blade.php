@extends('layouts.panel')

@section('title', 'Carga asistida')

@section('content_header')
    <h1>Carga asistida de tus propiedades</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card mb-4" style="border-left: 4px solid #6366f1;">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="fas fa-hands-helping me-1" style="color:#6366f1;"></i> ¿Cómo funciona?</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;background:#6366f1; font-weight:700;">1</div>
                        <div><strong>Dejanos los datos</strong><br><small class="text-muted">Completá el formulario de abajo (o mandalos por WhatsApp/email con las fotos).</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;background:#8b5cf6; font-weight:700;">2</div>
                        <div><strong>Nosotros armamos la publicación</strong><br><small class="text-muted">Fotos, mapa, descripción y precio, todo prolijo en tu vidriera.</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;background:#d946ef; font-weight:700;">3</div>
                        <div><strong>Te avisamos apenas está online</strong><br><small class="text-muted">Recibís un email con el enlace y la ves en "Mis Inmuebles" con la marca de carga asistida.</small></div>
                    </div>
                </div>
            </div>
            <p class="text-muted small mb-0 mt-3">Ideal para arrancar sin cargar una por una, o cuando tenés varias propiedades para publicar. Mientras tanto, tu perfil público ya está activo.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-plus-circle me-1"></i> Nueva solicitud</h3></div>
                <div class="card-body">
                    <form action="{{ route('solicitudes.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="titulo">¿Qué propiedad es? (opcional)</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="255" value="{{ old('titulo') }}" placeholder="Ej: Casa en venta, centro de Chivilcoy">
                        </div>
                        <div class="form-group mb-3">
                            <label for="detalle">Datos de la propiedad <span class="text-danger">*</span></label>
                            <textarea name="detalle" id="detalle" class="form-control" rows="6" required maxlength="2000" placeholder="Contanos todo: dirección, tipo (venta/alquiler), precio, ambientes, baños, superficie, cochera, y si tenés fotos (podés enviarlas por WhatsApp/email y las agregamos).">{{ old('detalle') }}</textarea>
                            @error('detalle')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold w-100"><i class="fas fa-paper-plane me-1"></i> Enviar solicitud</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-list me-1"></i> Mis solicitudes ({{ $solicitudes->count() }})</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Solicitud</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $s)
                            <tr>
                                <td>
                                    <strong>{{ $s->titulo ?: 'Sin título' }}</strong>
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($s->detalle, 90) }}</small>
                                </td>
                                <td>
                                    @if($s->estado === 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($s->estado === 'en_carga')
                                        <span class="badge badge-info">En carga</span>
                                    @elseif($s->estado === 'cargada')
                                        <span class="badge badge-success">Cargada</span>
                                    @else
                                        <span class="badge badge-secondary">Cancelada</span>
                                    @endif
                                </td>
                                <td>{{ $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-center">
                                    @if($s->estado === 'cargada' && $s->inmueble)
                                        @if($s->inmueble->publicado)
                                            <a href="{{ route('propiedad.show', [$inmobiliaria->slug, $s->inmueble->id]) }}" target="_blank" class="btn btn-success btn-sm rounded-pill"><i class="fas fa-eye"></i> Ver publicada</a>
                                        @else
                                            <a href="{{ route('inmuebles.edit', $s->inmueble) }}" class="btn btn-outline-primary btn-sm rounded-pill"><i class="fas fa-pen"></i> Editar</a>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Todavía no pediste ninguna carga asistida.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
