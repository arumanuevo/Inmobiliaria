<!DOCTYPE html>
<html lang="es">
<body style="font-family: Arial, sans-serif; color: #1d2939;">
    <h2 style="color: #101828;">Nueva solicitud de carga asistida</h2>
    <p><strong>{{ $solicitud->inmobiliaria->nombre_comercial ?? 'Inmobiliaria' }}</strong>
       ({{ $solicitud->inmobiliaria->email ?? ($solicitud->inmobiliaria->usuario->email ?? '-') }})
       pidió que le cargues una propiedad:</p>
    @if($solicitud->titulo)
        <p><strong>Propiedad:</strong> {{ $solicitud->titulo }}</p>
    @endif
    <p><strong>Detalle:</strong></p>
    <p style="background:#f6f7fb; border-left: 3px solid #6366f1; padding: 10px 14px;">{{ $solicitud->detalle }}</p>
    <p>Atendela desde el panel: <a href="{{ route('admin.solicitudes') }}">{{ route('admin.solicitudes') }}</a></p>
    <p style="color:#98a2b3; font-size:12px;">Solicitud #{{ $solicitud->id }} · {{ config('app.name') }}</p>
</body>
</html>
