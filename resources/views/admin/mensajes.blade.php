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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enviados as $e)
                            @php
                                $clave = (string) $e->created_at;
                                $grupo = $detalles[$clave] ?? collect();
                                $collapseId = 'dest-' . md5($clave . $e->titulo);
                            @endphp
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
                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                                        <i class="fas fa-users me-1"></i> Ver destinatarios
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="p-0">
                                  <div class="collapse" id="{{ $collapseId }}">
                                    <div class="bg-light p-2 border-top">
                                    @if($grupo->isEmpty())
                                        <small class="text-muted">Sin detalle de destinatarios.</small>
                                    @else
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($grupo as $m)
                                                <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                    <span>
                                                        <i class="fas fa-user me-1 text-muted"></i>
                                                        {{ $m->usuario?->name ?? ('Usuario #' . $m->usuario_id) }}
                                                        <small class="text-muted">({{ $m->usuario?->email ?? 'sin email' }})</small>
                                                    </span>
                                                    @if($m->leido)
                                                        <span class="badge badge-success">leído {{ $m->leido_at?->format('d/m H:i') }}</span>
                                                    @else
                                                        <span class="badge badge-warning">sin leer</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    </div>
                                  </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Todavía no se envió ningún aviso.</td></tr>
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
