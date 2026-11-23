<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', config('app.name'))</title>
  @yield('meta')
  <!-- Bootstrap CSS (versión 5) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome (iconos usados en todas las vistas públicas) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/logoinmo.svg') }}?v=1">
  @yield('css')
</head>
<body>
  @yield('content')
  
  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Fix universal de modales: los que viven dentro de contenedores con transform/overflow/sticky
    // (lista de resultados AJAX, tarjetas con hover, celdas de tabla) quedan tapados por el backdrop
    // y no reciben clics. Al abrirse se mueven al <body>, donde el z-index de Bootstrap funciona bien.
    document.addEventListener('show.bs.modal', function (e) {
      if (e.target && e.target.parentElement !== document.body) {
        document.body.appendChild(e.target);
      }
    });
  </script>
  @yield('js')
</body>
</html>


