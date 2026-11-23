@extends('layouts.panel')

@section('title', 'Mis mensajes')

@section('content_header')
    <h1><i class="fas fa-envelope me-2"></i>Mis mensajes</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            @if($noLeidos > 0)
                <span class="badge badge-danger">{{ $noLeidos }} sin leer</span>
            @else
                <span class="badge badge-success">Bandeja al día</span>
            @endif
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($soloNoLeidos)
                <a href="{{ route('mensajes.mias') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Ver todos</a>
            @else
                <a href="{{ route('mensajes.mias', ['no_leidos' => 1]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Solo no leídos</a>
            @endif
            @if($noLeidos > 0)
                <form action="{{ route('mensajes.leerTodos') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-check-double me-1"></i> Marcar todos como leídos</button>
                </form>
            @endif
        </div>
    </div>

    @forelse($mensajes as $mensaje)
        <div class="card {{ $mensaje->leido ? '' : 'miss-noleido' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            @if(! $mensaje->leido)
                                <span class="badge badge-danger me-1">Nuevo</span>
                            @endif
                            {{ $mensaje->titulo }}
                        </h5>
                        <span class="badge badge-secondary">{{ \App\Models\Mensaje::tipos()[$mensaje->tipo] ?? ucfirst($mensaje->tipo) }}</span>
                        <small class="text-muted ms-2">{{ $mensaje->created_at?->format('d/m/Y H:i') }}</small>
                    </div>
                    @if(! $mensaje->leido)
                        <form action="{{ route('mensajes.leer', $mensaje) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                <i class="fas fa-check me-1"></i> Marcar leído
                            </button>
                        </form>
                    @else
                        <small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i>Leído {{ $mensaje->leido_at?->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
                <p class="mb-0 mt-2" style="white-space: pre-line;">{{ $mensaje->cuerpo }}</p>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p class="mb-0">No tenés mensajes. Cuando el administrador envíe un aviso (por ejemplo, cambios en los planes), aparece acá.</p>
            </div>
        </div>
    @endforelse

    <div class="d-flex justify-content-center mt-3">
        {{ $mensajes->links() }}
    </div>
</div>
@stop

@section('css')
<style>
    .miss-noleido { border-left: 4px solid #dc2626; background: #fffafa; }
</style>
@stop
