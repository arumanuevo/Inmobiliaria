<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto de Inmobiliaria</title>
</head>
<body>
    <h1>Nuevo Mensaje de Contacto</h1>
    <p><strong>Nombre:</strong> {{ $data['nombre'] }}</p>
    <p><strong>Correo Electrónico:</strong> {{ $data['email'] }}</p>
    @if(isset($data['telefono']))
        <p><strong>Teléfono:</strong> {{ $data['telefono'] }}</p>
    @endif
    <p><strong>Mensaje:</strong> {{ $data['mensaje'] }}</p>
</body>
</html>

