<!DOCTYPE html>
<html>
<head>
    <title>Enviar Mensaje de WhatsApp</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Enviar Mensaje de WhatsApp</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('sendpost') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="to">Número de WhatsApp (con código de país, ej. +5491112345678)</label>
                <input type="text" class="form-control" id="to" name="to" required>
            </div>
            <div class="form-group">
                <label for="message">Mensaje</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</body>
</html>

