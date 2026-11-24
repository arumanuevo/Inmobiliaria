<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logoinmo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/panel.css') }}?v=6" rel="stylesheet">
    @yield('css')
</head>
<body>
<div class="panel-wrapper">
    <aside class="panel-sidebar">
        <a class="brand" href="{{ route('inicio') }}">
            <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo">
            <span>{{ config('app.name') }}</span>
        </a>
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menú" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="panel-nav">
            <a href="{{ route('inicio') }}" class="{{ request()->is('/') || request()->is('inicio') ? 'active' : '' }}">
                <i class="fas fa-globe"></i><span>Portal</span>
            </a>
            @if(auth()->user()->hasAnyRole(['admin', 'inmobiliaria', 'particular']))
                <a href="{{ route('inmuebles.index') }}" class="{{ request()->is('inmuebles*') ? 'active' : '' }}">
                    <i class="fas fa-key"></i><span>Mis Inmuebles</span>
                </a>
                <a href="{{ route('inmuebles.create') }}" class="{{ request()->is('inmuebles/create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i><span>Nuevo Inmueble</span>
                </a>
                <a href="{{ route('inmobiliaria.perfil') }}" class="{{ request()->is('mi-inmobiliaria') ? 'active' : '' }}">
                    <i class="fas fa-store"></i><span>{{ auth()->user()->hasRole('particular') ? 'Mi perfil público' : 'Mi Inmobiliaria' }}</span>
                </a>
                @if(auth()->user()->inmobiliaria)
                <a href="{{ route('inmobiliaria.preview') }}" target="_blank" class="{{ request()->is('mi-inmobiliaria/preview') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i><span>Vista previa</span>
                </a>
                @endif
                <a href="{{ route('boletos.mios') }}" class="{{ request()->is('mis-boletos') || request()->is('boletos/*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i><span>Mis Boletos</span>
                </a>
                <a href="{{ route('busquedas.mias') }}" class="{{ request()->is('mis-busquedas*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i><span>Mis búsquedas</span>
                </a>
                <a href="{{ route('solicitudes.mias') }}" class="{{ request()->is('mis-solicitudes*') ? 'active' : '' }}">
                    <i class="fas fa-hands-helping"></i><span>Carga asistida</span>
                </a>
                <a href="{{ route('mensajes.mias') }}" class="{{ request()->is('mensajes*') && ! request()->is('admin/*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i><span>Mensajes</span>
                    @php $noLeidos = \App\Models\Mensaje::where('usuario_id', auth()->id())->whereNull('leido_at')->count(); @endphp
                    @if($noLeidos > 0)<span class="badge-super">{{ $noLeidos }}</span>@endif
                </a>
                @if(! auth()->user()->esSuperAdmin())
                <a href="{{ route('ayuda') }}" class="{{ request()->is('ayuda') ? 'active' : '' }}">
                    <i class="fas fa-circle-question"></i><span>Ayuda</span>
                </a>
                @endif
            @endif
            @if(auth()->user()->hasRole('admin'))
                <div class="menu-section">Administración</div>
                <a href="{{ route('admin.inmobiliarias') }}" class="{{ request()->is('admin/inmobiliarias*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i><span>Inmobiliarias</span>
                </a>
                <a href="{{ route('admin.boletos') }}" class="{{ request()->is('admin/boletos*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i><span>Boletos</span>
                </a>
                <a href="{{ route('admin.operaciones') }}" class="{{ request()->is('admin/operaciones*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i><span>Operaciones</span>
                </a>
                <a href="{{ route('admin.solicitudes') }}" class="{{ request()->is('admin/solicitudes*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i><span>Solicitudes</span>
                    @php $pendSoli = \App\Models\SolicitudCarga::where('estado', 'pendiente')->count(); @endphp
                    @if($pendSoli > 0)<span class="badge-super">{{ $pendSoli }}</span>@endif
                </a>
                @if(auth()->user()->esSuperAdmin())
                <a href="{{ route('admin.planes') }}" class="{{ request()->is('admin/planes*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i><span>Paquetes</span>
                </a>
                <a href="{{ route('admin.mensajes') }}" class="{{ request()->is('admin/mensajes*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i><span>Enviar aviso</span>
                </a>
                <a href="{{ route('admin.configuracion') }}" class="{{ request()->is('admin/configuracion*') ? 'active' : '' }}">
                    <i class="fas fa-sliders"></i><span>Configuración</span>
                </a>
                @endif
            @endif
        </nav>
        <div class="sidebar-footer">
            <span class="user-name">{{ auth()->user()->name }}</span>
            @if(auth()->user()->esSuperAdmin())
                <span class="badge-super">superadmin</span>
            @endif
        </div>
    </aside>

    <div class="panel-main">
        <header class="panel-topbar">
            <div class="page-title">@yield('content_header')</div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</button>
            </form>
        </header>

        <main class="panel-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Revisá el formulario:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="fas fa-envelope-open-text me-1"></i>
                        <strong>Verificá tu email:</strong> te enviamos un enlace a <strong>{{ auth()->user()->email }}</strong> para confirmar tu cuenta.
                    </div>
                    <form action="{{ route('verification.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3"><i class="fas fa-paper-plane me-1"></i> Reenviar enlace</button>
                    </form>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mismos fix que en el layout público: modales dentro de contenedores con transform/sticky
// se mueven al <body> al abrirse para que el backdrop no los tape ni bloquee los clics
document.addEventListener('show.bs.modal', function (e) {
    if (e.target && e.target.parentElement !== document.body) {
        document.body.appendChild(e.target);
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sidebar: en pantallas angostas (o celular aunque el viewport reporte ancho, ej. "sitio de
    // escritorio") el sidebar es barra superior + drawer. Se fuerza con body.panel-mobile porque
    // las media queries no aplican cuando el celu reporta viewport de escritorio.
    var sidebar = document.querySelector('.panel-sidebar');
    var toggle = document.getElementById('sidebarToggle');
    if (!sidebar || !toggle) { return; }

    function esMobile() {
        if (window.innerWidth <= 1200) { return true; }
        var uaMobile = /Android|iPhone|iPad|iPod|Mobile|webOS|BlackBerry|IEMobile/i.test(navigator.userAgent);
        var tactilChica = (navigator.maxTouchPoints > 0 || 'ontouchstart' in window) && window.screen.width <= 900;
        return uaMobile || tactilChica;
    }

    function cerrarMenu() {
        sidebar.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.style.overflow = '';
    }

    function aplicarModo() {
        var mobile = esMobile();
        document.body.classList.toggle('panel-mobile', mobile);
        toggle.style.display = mobile ? 'inline-flex' : '';
        if (!mobile) { cerrarMenu(); }
    }

    toggle.addEventListener('click', function () {
        var abierto = sidebar.classList.toggle('open');
        toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        toggle.innerHTML = abierto ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        document.body.style.overflow = abierto ? 'hidden' : ''; // con el menú abierto no se scrollea la página
    });

    sidebar.querySelectorAll('.panel-nav a').forEach(function (a) {
        a.addEventListener('click', cerrarMenu);
    });

    // Tocar el fondo oscuro del drawer (fuera de los enlaces) también lo cierra
    var nav = sidebar.querySelector('.panel-nav');
    if (nav) {
        nav.addEventListener('click', function (e) { if (e.target === nav) { cerrarMenu(); } });
    }

    aplicarModo();
    window.addEventListener('resize', aplicarModo);
    window.addEventListener('orientationchange', aplicarModo);
});
</script>
@yield('js')
</body>
</html>
