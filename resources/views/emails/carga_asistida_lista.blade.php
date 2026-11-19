<!DOCTYPE html>
<html lang="es">
<body style="font-family: Arial, sans-serif; color: #1d2939;">
    <h2 style="color: #101828;">¡Listo! Ya cargamos tu propiedad 🎉</h2>
    <p>Buenas noticias: nuestro equipo terminó de cargar y publicar tu propiedad en <strong>{{ config('app.name') }}</strong>:</p>
    <p style="font-size: 18px;"><strong>{{ $inmueble->titulo }}</strong></p>
    <p>{{ $inmueble->precio_formateado }} &bull; {{ ucfirst($inmueble->tipo_operacion) }}@if($inmueble->ciudad) &bull; {{ $inmueble->ciudad }}@endif</p>
    <p>
        <a href="{{ $url }}" style="background: #4f46e5; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold;">Ver mi propiedad publicada</a>
    </p>
    <p style="color:#667085;">También la vas a ver en tu panel ("Mis Inmuebles") con la marca de <em>carga asistida</em>. Si querés cambiar algo (precio, descripción, fotos), editála cuando quieras.</p>
    <hr style="border:none; border-top:1px solid #e5e7eb;">
    <p style="color:#98a2b3; font-size:12px;">{{ $inmobiliaria->nombre_comercial }} · {{ config('app.name') }}</p>
</body>
</html>
