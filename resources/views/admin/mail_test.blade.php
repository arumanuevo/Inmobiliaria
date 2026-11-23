@extends('layouts.panel')

@section('title', 'Prueba de email')

@section('content_header')
    <h1><i class="fas fa-envelope-open-text me-2"></i>Prueba de envío de email</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Resultado del envío de prueba</h3></div>
                <div class="card-body">
                    <p>Se intentó enviar un mail de prueba a <strong>{{ $to }}</strong>.</p>
                    @if($resultado['ok'])
                        <div class="alert alert-success mb-2"><i class="fas fa-check-circle me-1"></i> <strong>Enviado OK.</strong> Si no llega, revisá spam; si no está ni en el outbox del SMTP, el transporte no es SMTP.</div>
                    @else
                        <div class="alert alert-danger mb-2"><i class="fas fa-times-circle me-1"></i> <strong>Falló:</strong><br><code>{{ $resultado['error'] }}</code></div>
                    @endif
                    <p class="text-muted small mb-2">Si falla, el detalle queda en <code>storage/logs/laravel.log</code>. Con <code>MAIL_MAILER=log</code> los mails nunca salen (van al log).</p>
                    <a href="{{ route('admin.mail-test') }}" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-redo me-1"></i> Reintentar</a>
                    <a href="{{ route('admin.mensajes') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 ms-1">Volver a avisos</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Configuración efectiva (del .env del servidor)</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <tbody>
                            @foreach($config as $k => $v)
                            <tr>
                                <td class="text-muted small" style="width: 45%;">{{ $k }}</td>
                                <td><code>{{ is_null($v) ? '(null)' : $v }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <small class="text-muted d-block mt-2">Valores esperados en el hosting: <code>MAIL_MAILER=smtp</code>, <code>MAIL_HOST=mail.tudominio</code>, <code>MAIL_PORT=465</code> + <code>MAIL_ENCRYPTION=ssl</code> (o 587/tls), <code>QUEUE_CONNECTION=sync</code> (la app fuerza sync igual). Alternativa sin SMTP: <code>MAIL_MAILER=sendmail</code>.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
