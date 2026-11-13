<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('path/to/adminlte.css') }}">
    <!-- Otros estilos -->
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('partials.navbar')

        <!-- Main Sidebar Container (oculto en esta página) -->
        <!-- <aside class="main-sidebar sidebar-dark-primary elevation-4">
            @include('partials.sidebar')
        </aside> -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        @include('partials.footer')
    </div>

    <!-- AdminLTE JS -->
    <script src="{{ asset('path/to/adminlte.js') }}"></script>
    <!-- Otros scripts -->
</body>
</html>
