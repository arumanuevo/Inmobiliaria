<!DOCTYPE html>
<html lang="es">
<body style="font-family: Arial, sans-serif; color: #1d2939;">
    <h2 style="color: #101828;">Nueva propiedad que puede interesarte</h2>
    <p>Se acaba de publicar una propiedad que coincide con una búsqueda que guardaste en <strong>{{ config('app.name') }}</strong>:</p>
    <p style="font-size: 18px;"><strong>{{ $inmueble->titulo }}</strong></p>
    <p>
        {{ $inmueble->precio_formateado }}
        &bull; {{ ucfirst($inmueble->tipo_operacion) }}
        @if($inmueble->ciudad) &bull; {{ $inmueble->ciudad }} @endif
        @if($inmueble->habitaciones) &bull; {{ $inmueble->habitaciones }} hab. @endif
        @if($inmueble->superficie_cubierta) &bull; {{ $inmueble->superficie_cubierta }} m&sup2; @endif
    </p>
    <p style="color: #667085;">{{ \Illuminate\Support\Str::limit(strip_tags($inmueble->descripcion), 200) }}</p>
    @if($inmueble->inmobiliaria)
    <p style="color: #667085; font-size: 13px;">Publicado por: <strong>{{ $inmueble->inmobiliaria->nombre_comercial }}</strong></p>
    @endif
    <p>
        <a href="{{ $url }}" style="background: #4f46e5; color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold;">Ver la propiedad</a>
    </p>
    <hr style="border: none; border-top: 1px solid #e5e7eb;">
    <p style="color: #98a2b3; font-size: 12px;">
        Recibís este aviso porque guardaste esta búsqueda en {{ config('app.name') }}.
        Si ya no querés recibir alertas, entrá a tu cuenta y eliminá la búsqueda en "Mis búsquedas".
    </p>
</body>
</html>
