<?php

namespace App\Support;

use App\Models\Inmueble;

/**
 * Valuador "AVM-lite": estima el valor de mercado de un inmueble a partir de
 * los comparables publicados en el propio portal (precio / m², mediana y
 * percentiles 25-75), geolocalizados por haversine (scope cercanosa).
 *
 * Es una ESTIMACIÓN ORIENTATIVA basada en avisos publicados: no ve estado de
 * conservación ni refacciones, y no reemplaza una tasación profesional.
 */
class Valuador
{
    const RADIO_KM = 25;          // radio de búsqueda de comparables
    const MIN_COMPARABLES = 5;    // mínimo para dar estimate
    const MAX_COMPARABLES = 40;   // tope de comparables considerados

    /**
     * Estima el valor de un inmueble.
     *
     * @param array $d lat, lng, ciudad, tipo_operacion, tipo_propiedad, ambientes, sup_cubierta, comparables_ids (opcional)
     * @return array{ok:bool, mensaje:?string, ...}
     */
    public static function estimar(array $d): array
    {
        $lat = isset($d['lat']) && is_numeric($d['lat']) ? (float) $d['lat'] : null;
        $lng = isset($d['lng']) && is_numeric($d['lng']) ? (float) $d['lng'] : null;
        $ciudad = trim((string) ($d['ciudad'] ?? ''));
        $op = ($d['tipo_operacion'] ?? 'venta') === 'alquiler' ? 'alquiler' : 'venta';
        $tipo = trim((string) ($d['tipo_propiedad'] ?? ''));
        $ambientes = isset($d['ambientes']) && is_numeric($d['ambientes']) ? (int) $d['ambientes'] : null;
        $sup = isset($d['sup_cubierta']) && is_numeric($d['sup_cubierta']) ? max(1.0, (float) $d['sup_cubierta']) : null;

        $base = Inmueble::publicados()
            ->where('cerrada', 0)
            ->where('tipo_operacion', $op)
            ->where('precio', '>', 0)
            ->where('superficie_cubierta', '>', 0)
            ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
            ->with('inmobiliaria');

        // Geolocalizado: comparables en un radio; si no, por ciudad
        if ($lat !== null && $lng !== null) {
            $base->cercanosA($lat, $lng, self::RADIO_KM);
        } elseif ($ciudad !== '') {
            $base->whereRaw('LOWER(ciudad) = ?', [mb_strtolower($ciudad)]);
        } else {
            return ['ok' => false, 'mensaje' => 'Indicá una dirección o ciudad para buscar comparables.'];
        }

        $items = $base->get();

        // Filtrar los que tengan coordenadas válidas y armar pm2
        $comp = [];
        foreach ($items as $i) {
            $pm2 = (float) $i->precio / max(1.0, (float) $i->superficie_cubierta);
            if ($pm2 <= 0) {
                continue;
            }
            $dist = null;
            $cLat = null;
            $cLng = null;
            $p = array_map('trim', explode(',', (string) $i->coordenadas));
            if (count($p) === 2 && is_numeric($p[0]) && is_numeric($p[1])) {
                $cLat = (float) $p[0];
                $cLng = (float) $p[1];
                if ($lat !== null && $lng !== null) {
                    $dist = self::haversineKm($lat, $lng, $cLat, $cLng);
                }
            } elseif ($lat !== null && $lng !== null) {
                continue; // geolocalizado: sin coordenadas no sirve
            }
            // ponderación: mismo tipo y ambientes cercanos pesan más (para ordenar)
            $score = ($dist ?? 999) * 1.0;
            if ($tipo !== '' && $i->tipo_propiedad === $tipo) { $score -= 5; }
            if ($ambientes !== null && $i->ambientes !== null && abs((int) $i->ambientes - $ambientes) <= 1) { $score -= 3; }
            $comp[] = [
                'id' => $i->id,
                'titulo' => $i->titulo,
                'tipo_propiedad' => $i->tipo_propiedad,
                'ambientes' => $i->ambientes,
                'sup' => (float) $i->superficie_cubierta,
                'precio' => (float) $i->precio,
                'pm2' => round($pm2, 2),
                'distancia_km' => $dist !== null ? round($dist, 2) : null,
                'lat' => $cLat,
                'lng' => $cLng,
                'ciudad' => $i->ciudad,
                'foto' => !empty($i->fotos) ? asset('images/fotosinmuebles/' . $i->fotos[0]) : null,
                'url' => $i->inmobiliaria ? route('propiedad.show', [$i->inmobiliaria->slug, $i->id]) : null,
                'score' => $score,
            ];
        }

        // Premium puede elegir comparables específicos: recalcular solo con esos
        if (!empty($d['comparables_ids']) && is_array($d['comparables_ids'])) {
            $ids = array_map('intval', $d['comparables_ids']);
            $comp = array_values(array_filter($comp, fn ($c) => in_array((int) $c['id'], $ids, true)));
        }

        usort($comp, fn ($a, $b) => $a['score'] <=> $b['score']);
        $comp = array_slice($comp, 0, self::MAX_COMPARABLES);

        if (count($comp) < self::MIN_COMPARABLES) {
            return [
                'ok' => false,
                'mensaje' => 'No hay suficientes propiedades comparables publicadas en la zona (se necesitan al menos ' . self::MIN_COMPARABLES . '). Probá agrandar el radio o usar la ciudad completa.',
                'comparables' => array_slice($comp, 0, 20),
                'n' => count($comp),
            ];
        }

        if ($sup === null) {
            return ['ok' => false, 'mensaje' => 'Indicá la superficie cubierta (m²) para calcular el valor estimado.'];
        }

        $pm2s = array_map(fn ($c) => $c['pm2'], $comp);
        sort($pm2s);
        $mediana = self::percentil($pm2s, 0.50);
        $p25 = self::percentil($pm2s, 0.25);
        $p75 = self::percentil($pm2s, 0.75);

        return [
            'ok' => true,
            'mensaje' => null,
            'n' => count($comp),
            'pm2_mediana' => round($mediana, 2),
            'pm2_p25' => round($p25, 2),
            'pm2_p75' => round($p75, 2),
            'valor_medio' => round($mediana * $sup / 1000) * 1000,
            'valor_min' => round($p25 * $sup / 1000) * 1000,
            'valor_max' => round($p75 * $sup / 1000) * 1000,
            'sup' => $sup,
            'comparables' => array_slice($comp, 0, 20),
        ];
    }

    /**
     * Estadísticas de mercado de una zona (para el reporte y /admin/valuaciones):
     * cantidad de avisos, mediana $/m² y tendencia (últimos 90 días vs 90-180 días previos).
     */
    public static function estadisticasZona(?string $ciudad, string $op = 'venta'): array
    {
        $q = Inmueble::publicados()
            ->where('cerrada', 0)
            ->where('tipo_operacion', $op)
            ->where('precio', '>', 0)
            ->where('superficie_cubierta', '>', 0)
            ->whereHas('inmobiliaria', fn ($q2) => $q2->where('estado', 'activa'));
        if ($ciudad) {
            $q->whereRaw('LOWER(ciudad) = ?', [mb_strtolower($ciudad)]);
        }
        $items = $q->get(['precio', 'superficie_cubierta', 'created_at']);
        $pm2 = $items->map(fn ($i) => (float) $i->precio / max(1.0, (float) $i->superficie_cubierta))->sort()->values()->all();

        $ahora = now();
        $hace90 = $ahora->copy()->subDays(90);
        $hace180 = $ahora->copy()->subDays(180);
        $recientes = $items->filter(fn ($i) => $i->created_at && $i->created_at >= $hace90)
            ->map(fn ($i) => (float) $i->precio / max(1.0, (float) $i->superficie_cubierta))->sort()->values()->all();
        $previos = $items->filter(fn ($i) => $i->created_at && $i->created_at >= $hace180 && $i->created_at < $hace90)
            ->map(fn ($i) => (float) $i->precio / max(1.0, (float) $i->superficie_cubierta))->sort()->values()->all();

        $medRec = count($recientes) ? self::percentil($recientes, 0.5) : null;
        $medPrev = count($previos) ? self::percentil($previos, 0.5) : null;
        $tendencia = ($medRec && $medPrev) ? round((($medRec - $medPrev) / $medPrev) * 100, 1) : null;

        return [
            'avisos' => $items->count(),
            'pm2_mediana' => count($pm2) ? round(self::percentil($pm2, 0.5), 2) : null,
            'pm2_promedio' => count($pm2) ? round(array_sum($pm2) / count($pm2), 2) : null,
            'tendencia_pct' => $tendencia,
            'avisos_90d' => count($recientes),
        ];
    }

    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /** Percentil con interpolación lineal sobre una lista ordenada */
    public static function percentil(array $sorted, float $p): float
    {
        $n = count($sorted);
        if ($n === 0) {
            return 0.0;
        }
        if ($n === 1) {
            return (float) $sorted[0];
        }
        $k = ($n - 1) * $p;
        $f = (int) floor($k);
        $c = (int) ceil($k);
        if ($f === $c) {
            return (float) $sorted[$f];
        }

        return (float) $sorted[$f] + ($k - $f) * ((float) $sorted[$c] - (float) $sorted[$f]);
    }
}
