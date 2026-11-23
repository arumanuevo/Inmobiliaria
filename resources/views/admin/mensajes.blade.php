@extends('layouts.panel')

@section('title', 'Enviar aviso a usuarios')

@section('content_header')
    <h1><i class="fas fa-bullhorn me-2"></i>Enviar aviso a usuarios</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-paper-plane me-2"></i>Nuevo aviso</h3></div>
                <div class="card-body">
                    <form action="{{ route('admin.mensajes.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="tipo">Tipo de aviso</label>
                            <select name="tipo" id="tipo" class="form-control">
                                @foreach(\App\Models\Mensaje::tipos() as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('tipo', 'aviso') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ej: "Planes y precios" para avisar una indexación de tarifas.</small>
                        </div>
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="150" required
                                   value="{{ old('titulo') }}" placeholder="Ej: Actualización de precios de los planes">
                        </div>
                        <div class="form-group">
                            <label for="cuerpo">Mensaje</label>
                            <textarea name="cuerpo" id="cuerpo" class="form-control" rows="6" maxlength="5000" required
                                      placeholder="Escribí el aviso…">{{ old('cuerpo') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="destinatarios">Destinatarios</label>
                            <select name="destinatarios" id="destinatarios" class="form-control">
                                <option value="todos" {{ old('destinatarios', 'todos') === 'todos' ? 'selected' : '' }}>Todos los usuarios</option>
                                <option value="inmobiliarias" {{ old('destinatarios') === 'inmobiliarias' ? 'selected' : '' }}>Solo inmobiliarias</option>
                                <option value="particulares" {{ old('destinatarios') === 'particulares' ? 'selected' : '' }}>Solo particulares (dueños directos)</option>
                                <option value="usuario" {{ old('destinatarios') === 'usuario' ? 'selected' : '' }}>Un usuario puntual</option>
                            </select>
                        </div>
                        <div class="form-group d-none" id="wrap-usuario">
                            <label for="usuario_id">Usuario</label>
                            <select name="usuario_id" id="usuario_id" class="form-control">
                                <option value="">Elegí…</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" {{ (int) old('usuario_id') === $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Enviar aviso</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-history me-2"></i>Avisos enviados</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Título</th>
                                <th class="text-center">Destinatarios</th>
                                <th class="text-center">Leídos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enviados as $e)
                            <tr>
                                <td class="text-nowrap small">{{ $e->created_at?->format('d/m/Y H:i') }}</td>
                                <td><span class="badge badge-secondary">{{ \App\Models\Mensaje::tipos()[$e->tipo] ?? $e->tipo }}</span></td>
                                <td>{{ $e->titulo }}</td>
                                <td class="text-center">{{ $e->destinatarios }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $e->leidos >= $e->destinatarios ? 'badge-success' : 'badge-warning' }}">
                                        {{ $e->leidos }}/{{ $e->destinatarios }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Todavía no se envió ningún aviso.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">{{ $enviados->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('destinatarios');
    var wrap = document.getElementById('wrap-usuario');
    function toggleUsuario() {
        wrap.classList.toggle('d-none', sel.value !== 'usuario');
        document.getElementById('usuario_id').required = (sel.value === 'usuario');
    }
    sel.addEventListener('change', toggleUsuario);
    toggleUsuario();
});
</script>
@stop
