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
    <link href="{{ asset('css/panel.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>
<div class="panel-wrapper">
    <aside class="panel-sidebar">
        <a class="brand" href="{{ route('inicio') }}">
            <img src="{{ asset('images/logoinmo.svg') }}" alt="Logo">
            <span>{{ config('app.name') }}</span>
        </a>
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
                <a href="{{ route('boletos.mios') }}" class="{{ request()->is('mis-boletos') || request()->is('boletos/*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i><span>Mis Boletos</span>
                </a>
                <a href="{{ route('busquedas.mias') }}" class="{{ request()->is('mis-busquedas*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i><span>Mis búsquedas</span>
                </a>
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
                @if(auth()->user()->esSuperAdmin())
                <a href="{{ route('admin.planes') }}" class="{{ request()->is('admin/planes*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i><span>Paquetes</span>
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

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('js')
</body>
</html>
