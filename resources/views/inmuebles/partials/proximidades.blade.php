@php
  // Muestra las proximidades del inmueble (ventajas del entorno) con icono según tipo.
  // Espera $inmueble. El icono usa la clase .prox-ico (cada contexto puede darle color).
  $prox = $inmueble->proximidadesLista();
@endphp
@if(!empty($prox))
<style>.prox-ico { color: #6366f1; }</style>
<div class="prox-bloque mb-3">
    <h6 class="fw-bold mb-2" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; color:#667085;">
        <i class="fas fa-location-arrow me-1 prox-ico"></i> Proximidades y ventajas
    </h6>
    <ul class="list-unstyled mb-0">
        @foreach($prox as $p)
            <li class="detalle-item mb-1">
                <i class="fas {{ \App\Models\Inmueble::iconoProximidad($p['nombre']) }} prox-ico me-2" style="width:18px; text-align:center;"></i>{{ $p['nombre'] }}
                @if($p['metros'] !== null)
                    <span class="text-muted">&middot; a {{ number_format($p['metros'], 0, ',', '.') }} m</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif
