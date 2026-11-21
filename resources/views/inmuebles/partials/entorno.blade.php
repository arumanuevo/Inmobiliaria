@php
  // Línea de "uso comercial" + puntaje de entorno para los modales de detalle.
  // Espera $inmueble.
  $peEnt = $inmueble->puntajeEntornoLabel();
  $peDet = is_array($inmueble->puntaje_entorno_detalle) ? $inmueble->puntaje_entorno_detalle : [];
  $bitsEnt = [];
  if ($peEnt && !empty($peDet['arteria'])) { $bitsEnt[] = $peDet['arteria']['label'] . ' (+' . $peDet['arteria']['puntos'] . ')'; }
  if ($peEnt && !empty($peDet['transporte']) && ($peDet['transporte']['puntos'] ?? 0) > 0) { $bitsEnt[] = 'transporte cerca (+' . $peDet['transporte']['puntos'] . ')'; }
  if ($peEnt && !empty($peDet['comercio']) && ($peDet['comercio']['cantidad'] ?? 0) > 0) { $bitsEnt[] = $peDet['comercio']['cantidad'] . ' comercios (+' . $peDet['comercio']['puntos'] . ')'; }
  if ($peEnt && !empty($peDet['servicios']) && ($peDet['servicios']['cantidad'] ?? 0) > 0) { $bitsEnt[] = $peDet['servicios']['cantidad'] . ' servicios (+' . $peDet['servicios']['puntos'] . ')'; }
@endphp
@if($inmueble->uso_detalle)
  <li class="detalle-item mb-1"><i class="fas fa-store detalle-ico me-2"></i>Uso: {{ $inmueble->uso_detalle }}</li>
@endif
@if($peEnt)
  <li class="detalle-item mb-1" title="{{ implode(' · ', $bitsEnt) }}">
    <i class="fas fa-chart-line me-2" style="color: {{ $peEnt['color'] }};"></i>Puntaje de entorno: <strong>{{ $inmueble->puntaje_entorno }}/100</strong> &middot; {{ $peEnt['texto'] }}
  </li>
@endif
