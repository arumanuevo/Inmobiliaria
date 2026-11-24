@extends('layouts.panel')

@section('title', 'Ayuda')

@section('content_header')
    <h1><i class="fas fa-circle-question me-2" style="color:var(--accent)"></i>Ayuda</h1>
@stop

@section('content')
@php
    $user = auth()->user();
    $esSuper = $user->esSuperAdmin();
@endphp

@if(!$emailVerificado)
<div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <i class="fas fa-envelope-open-text me-1"></i>
        <strong>Todavía no verificaste tu email</strong> ({{ $user->email }}). Es importante para que te lleguen los avisos y la recuperación de cuenta.
    </div>
    <form action="{{ route('verification.resend') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3"><i class="fas fa-paper-plane me-1"></i> Reenviar enlace</button>
    </form>
</div>
@endif

{{-- ===== Pasos rápidos ===== --}}
<div class="card mb-4">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-rocket me-2"></i>Empezá en 4 pasos</h3></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="help-step">
                    <span class="help-step-num">1</span>
                    <div>
                        <strong>Completá tu perfil público</strong>
                        <p class="help-step-txt mb-2">Logo cuadrado (1:1), foto de portada/jumbotron ancha (3:1), color de tu marca, zonas y redes. Se ve en tu página pública del portal.</p>
                        <a href="{{ route('inmobiliaria.perfil') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">{{ $esParticular ? 'Mi perfil público' : 'Mi Inmobiliaria' }} <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="help-step">
                    <span class="help-step-num">2</span>
                    <div>
                        <strong>Publicá un inmueble</strong>
                        <p class="help-step-txt mb-2">Dirección + punto en el mapa, fotos (se acomodan solas; portada recomendada 16:10), precio, comodidades y la declaración jurada de propietario.</p>
                        <a href="{{ route('inmuebles.create') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Nuevo inmueble <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="help-step">
                    <span class="help-step-num">3</span>
                    <div>
                        <strong>Gestioná tus publicaciones</strong>
                        <p class="help-step-txt mb-2">Editar, agregar o quitar fotos, destacar (según tu plan) y <em>cerrar la operación</em> cuando se concrete la venta/alquiler.</p>
                        <a href="{{ route('inmuebles.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mis inmuebles <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="help-step">
                    <span class="help-step-num">4</span>
                    <div>
                        <strong>Recibí avisos del portal</strong>
                        <p class="help-step-txt mb-2">El administrador te puede enviar avisos (cambios de planes, mantenimiento, etc.) que llegan a tu bandeja y por email.</p>
                        <a href="{{ route('mensajes.mias') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mensajes @if($noLeidos > 0)<span class="badge-super ms-1">{{ $noLeidos }}</span>@endif <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Tu plan y límites ===== --}}
@if(!empty($cuenta))
@php $i = $cuenta['inmobiliaria']; @endphp
<div class="card card-primary mb-4">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-layer-group me-2"></i>Tu plan y tus límites</h3></div>
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <small class="text-muted d-block">Plan actual</small>
                <strong class="fs-5">{{ $i->plan->nombre ?? 'Sin plan' }}</strong>
                <span class="text-muted">(@dinero($i->plan->precio_mensual ?? 0, 0)/mes)</span>
                <div class="small text-muted mt-1">Vencimiento: {{ $i->plan_vence_at ? $i->plan_vence_at->format('d/m/Y') : 'sin vencimiento' }}</div>
                @if($cuenta['boletos_pendientes'] > 0)
                    <div class="mt-1"><a href="{{ route('boletos.mios') }}" class="text-warning fw-bold"><i class="fas fa-exclamation-circle me-1"></i>{{ $cuenta['boletos_pendientes'] }} boleto(s) pendiente(s)</a></div>
                @endif
            </div>
            <div class="col-md-4">
                <small class="text-muted">Publicaciones vigentes: <strong>{{ $cuenta['publicadas'] }} / {{ $cuenta['limite_inmuebles'] }}</strong></small>
                <div class="progress mt-1" style="height: 8px;">
                    <div class="progress-bar {{ $cuenta['pct_pubs'] >= 100 ? 'bg-danger' : ($cuenta['pct_pubs'] >= 80 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ $cuenta['pct_pubs'] }}%"></div>
                </div>
                <small class="text-muted">Fotos por publicación: hasta {{ $cuenta['fotos_limite'] }}</small>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Destacadas: <strong>{{ $cuenta['destacadas'] }} / {{ $cuenta['limite_destacados'] }}</strong></small>
                <div class="progress mt-1" style="height: 8px;">
                    <div class="progress-bar {{ $cuenta['pct_dest'] >= 100 ? 'bg-danger' : ($cuenta['pct_dest'] >= 80 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ $cuenta['pct_dest'] }}%"></div>
                </div>
                <small class="text-muted">Aparecen primero en la home y en el directorio</small>
            </div>
        </div>
        @if($i->estado === 'pendiente')
            <div class="alert alert-warning mt-3 mb-0"><i class="fas fa-clock me-1"></i>Tu cuenta está <strong>pendiente de aprobación</strong> por el administrador del portal. Mientras tanto podés cargar tu perfil y tus primeras publicaciones.</div>
        @elseif($i->estado === 'suspendida')
            <div class="alert alert-danger mt-3 mb-0"><i class="fas fa-ban me-1"></i>Tu cuenta está <strong>suspendida</strong>. Contactá al administrador desde <a href="{{ route('contacto') }}">Contacto</a>.</div>
        @endif
        <hr>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="text-muted"><i class="fas fa-arrow-up me-1"></i>Los planes son escalonados: cuanto más pagás, más publicaciones y destacadas podés tener. Al cambiar de plan <strong>nunca borramos nada</strong>: lo que exceda el nuevo límite queda como borrador.</span>
            <a href="{{ route('sumate') }}" class="btn btn-primary btn-sm rounded-pill px-3 ms-auto"><i class="fas fa-layer-group me-1"></i> Ver todos los planes</a>
        </div>
    </div>
</div>

{{-- ===== Verificación (solo inmobiliarias) ===== --}}
@if($esInmobiliaria && $cuenta['verificada_col'])
<div class="card card-success mb-4">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-shield-alt me-2"></i>Insignia "Verificada" 🛡</h3></div>
    <div class="card-body">
        @if($i->verificada)
            <p class="mb-0">✅ Tu inmobiliaria ya está <strong>verificada</strong>: aparecés con el badge 🛡 en el <a href="{{ route('inmobiliarias.index') }}">directorio</a> y en tu perfil público, y podés filtrar "solo verificadas". Mantené al día tu CUIT y matrícula.</p>
        @else
            <p class="mb-2">Cargá tu <strong>CUIT</strong> y <strong>matrícula de registro</strong> en <a href="{{ route('inmobiliaria.perfil') }}">Mi Inmobiliaria</a> y pedí la verificación al administrador del portal. Las inmobiliarias verificadas se muestran primero en el directorio y generan más confianza.</p>
            <p class="mb-0 text-muted small">La verificación acredita que tu matrícula está inscripta en el registro correspondiente de tu provincia (Ley 27.551 y normativa provincial).</p>
        @endif
    </div>
</div>
@endif
@endif

{{-- ===== Sección según el tipo de cuenta ===== --}}
<div class="row g-3 mb-4">
    @if($esParticular)
    <div class="col-md-6">
        <div class="card h-100 card-info">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-user me-2"></i>Si sos dueño directo</h3></div>
            <div class="card-body">
                <ul class="mb-0 help-list">
                    <li>Publicás <strong>gratis</strong> tu propiedad como <span class="badge badge-success">Dueño Directo</span> (aparece en las búsquedas y en el mapa).</li>
                    <li>La <strong>declaración jurada</strong> de que sos el propietario es obligatoria al publicar.</li>
                    <li>Los interesados te contactan directo por el botón de consulta o WhatsApp de tu ficha.</li>
                    <li>Tu perfil público muestra tus datos de contacto tal como los cargues.</li>
                    <li>Podés <strong>cerrar la operación</strong> cuando concretes, para llevar tu historial.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    @if($esInmobiliaria)
    <div class="col-md-6">
        <div class="card h-100 card-info">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-store me-2"></i>Si sos inmobiliaria</h3></div>
            <div class="card-body">
                <ul class="mb-0 help-list">
                    <li>Tu <strong>página pública</strong> (/inmobiliaria/tu-slug) funciona como tu propio espacio digital: mapa con todas tus propiedades, galería, zonas de cobertura y botón de consulta.</li>
                    <li>Personalizá logo, portada, <strong>color tema</strong> y redes desde Mi Inmobiliaria.</li>
                    <li>Con el plan <strong>Premium</strong> sumás SEO propio (título y descripción de tu página) para aparecer mejor en Google.</li>
                    <li>Pedí la <strong>carga asistida</strong>: el administrador puede cargar inmuebles por vos desde Mis solicitudes.</li>
                    <li>Las operaciones cerradas quedan en tu historial y en el panel del administrador.</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-lock me-2"></i>Tu cuenta y seguridad</h3></div>
            <div class="card-body">
                <ul class="mb-0 help-list">
                    <li>Entrá con tu email o con <strong>Google</strong> desde la página de acceso.</li>
                    <li><strong>Verificá tu email</strong> con el enlace que te enviamos (también sirve para recuperar la cuenta).</li>
                    <li>El registro está protegido con <strong>reCAPTCHA</strong>.</li>
                    <li>Nunca compartas tu contraseña; el portal jamás te la pide por email o teléfono.</li>
                    <li>¿Olvidaste la contraseña? Usá "¿Olvidaste tu contraseña?" en el login.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ===== FAQ ===== --}}
<div class="card mb-4">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-question-circle me-2"></i>Preguntas frecuentes</h3></div>
    <div class="card-body">
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">¿Por qué no puedo publicar más propiedades?</button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Cada plan tiene un límite de publicaciones vigentes. Si lo alcanzaste, primero <strong>cerrá o despublicá</strong> una existente, o <a href="{{ route('sumate') }}">subí de plan</a>: los planes son escalonados y el cambio es inmediato.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">¿Cómo pago mi suscripción?</button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">El administrador te genera un <strong>boleto</strong> en <a href="{{ route('boletos.mios') }}">Mis Boletos</a> y lo pagás con Mercado Pago (tarjeta, dinero en cuenta o QR). El pago se acredita automáticamente por webhook y tu plan se activa/renueva solo.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">¿Qué pasa si bajo de plan o se me vence?</button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body"><strong>Nada se borra.</strong> Si tu plan baja o se vence, las publicaciones que excedan el nuevo límite pasan a <em>borrador</em> (no visibles públicamente) y las destacadas se desmarcan. Al renovar, volvé a publicarlas desde Mis Inmuebles.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">¿Las fotos se achican solas? ¿Qué proporciones conviene?</button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Sí: al subirlas, el sistema las <strong>normaliza automáticamente</strong> (tamaño y peso optimizados para la web) y te avisa si conviene ajustar la proporción. Recomendadas: <strong>foto portada de la propiedad 16:10</strong>, <strong>logo 1:1</strong> (cuadrado) y <strong>portada/jumbotron del perfil 3:1</strong> (bien ancha) para que cubra todo el ancho.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">¿Qué es "cerrar la operación" y qué comisión se registra?</button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Desde Mis Inmuebles, el botón <em>Cerrar</em> marca la propiedad como <strong>vendida/alquilada</strong>: deja de aparecer como publicada y queda en tu historial de operaciones <strong>pendiente de aprobación</strong> — el administrador lo aprueba cuando verifique el pago de la comisión, y recién ahí cuenta como operación concretada. Al cerrar se registra la <strong>comisión del portal</strong>: @if($comisionHabilitada)<strong>{{ rtrim(rtrim(number_format((float) $comisionPctDefault, 2, ',', '.'), '0'), ',') }}% del precio declarado</strong> por defecto (podés cambiar el % o poner un monto fijo; 0 = sin comisión) y queda <strong>pendiente de cobro</strong> hasta que el administrador la marca cobrada. {{ $comisionNota }}@elseactualmente está desactivada por el administrador.@endif</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">¿Quién ve mis datos de contacto?</button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Tu perfil público muestra únicamente los datos que vos cargaste (teléfono/WhatsApp, redes, email de contacto) para que los interesados te escriban. Tu email de acceso y tu contraseña nunca se muestran.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">¿Cómo recibo los avisos del administrador?</button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Por dos vías: la bandeja interna <a href="{{ route('mensajes.mias') }}">Mensajes</a> (con contador de no leídos en el menú) y un <strong>email</strong> a tu casilla verificada. Si no te llegan los emails, verificá tu email y revisá la carpeta de spam.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">¿Cómo me doy de baja o borro mi cuenta?</button>
                </h2>
                <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Escribinos desde <a href="{{ route('contacto') }}">Contacto</a> indicando tu email de acceso y te damos de baja. Tus publicaciones se despublican; el historial de operaciones cerradas se conserva por obligaciones legales (comisiones).</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Contacto ===== --}}
<div class="card card-warning">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-headset me-2"></i>¿Necesitás más ayuda?</h3></div>
    <div class="card-body d-flex flex-wrap align-items-center gap-2">
        <span class="text-muted">Escribinos a <strong>{{ $mailContacto }}</strong> o desde el formulario de contacto del portal. También podés revisar los <a href="{{ route('mensajes.mias') }}">avisos recibidos</a>.</span>
        <a href="{{ route('contacto') }}" class="btn btn-primary btn-sm rounded-pill px-3 ms-auto"><i class="fas fa-paper-plane me-1"></i> Contactar</a>
    </div>
</div>
@stop

@section('css')
<style>
.help-step { display: flex; gap: 12px; background: #f8fafc; border: 1px solid var(--card-border); border-radius: var(--radius); padding: 14px; height: 100%; }
.help-step-num { flex: 0 0 auto; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: #fff; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; }
.help-step-txt { color: var(--muted); font-size: 12.8px; line-height: 1.45; margin-top: 2px; }
.help-list { padding-left: 18px; display: flex; flex-direction: column; gap: 7px; font-size: 13.5px; }
.help-list li::marker { color: var(--accent); }
.accordion-button { font-weight: 600; font-size: 14px; }
.accordion-button:not(.collapsed) { background: #eef2ff; color: var(--accent-dark); }
.accordion-body { font-size: 13.5px; color: var(--text); }
</style>
@stop
