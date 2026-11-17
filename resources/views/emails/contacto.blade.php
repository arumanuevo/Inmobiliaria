<!DOCTYPE html>
<html>
<head>
    <title>Nuevo mensaje de contacto - {{ config('app.name') }}</title>
</head>
<body>
    <h1>Nuevo mensaje de contacto</h1>
    @if(!empty($data['motivo']))
        <p><strong>Motivo:</strong> {{ $data['motivo'] }}</p>
    @endif
    @if(isset($data['inmobiliaria_nombre']))
        <p><strong>Anunciante:</strong> {{ $data['inmobiliaria_nombre'] }}</p>
    @endif
    @if(!empty($data['inmueble_id']))
        <p><strong>Propiedad #:</strong> {{ $data['inmueble_id'] }}</p>
    @endif
    <p><strong>Nombre:</strong> {{ $data['nombre'] }}</p>
    <p><strong>Correo Electrónico:</strong> {{ $data['email'] }}</p>
    @if(isset($data['telefono']))
        <p><strong>Teléfono:</strong> {{ $data['telefono'] }}</p>
    @endif
    <p><strong>Mensaje:</strong> {{ $data['mensaje'] }}</p>
</body>
</html>

