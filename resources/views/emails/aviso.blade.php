<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }} - {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1f2937; background: #f3f5f9; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 14px; padding: 28px; border: 1px solid #e5e7eb;">
        <p style="margin: 0 0 4px; font-size: 12px; font-weight: bold; color: #6366f1; text-transform: uppercase; letter-spacing: .06em;">{{ $tipoLabel }}</p>
        <h1 style="margin: 0 0 14px; font-size: 20px;">{{ $titulo }}</h1>
        <p style="white-space: pre-line; line-height: 1.55;">{{ $cuerpo }}</p>
        <p style="margin-top: 22px;">
            <a href="{{ $urlBandeja }}" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; text-decoration: none; font-weight: bold; padding: 10px 20px; border-radius: 999px; display: inline-block;">Ver en mi panel</a>
        </p>
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 22px 0;">
        <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ config('app.name') }} &middot; Este aviso también aparece en tu bandeja de mensajes del panel.</p>
    </div>
</body>
</html>
