<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PuntajeEntornoService
 *
 * Calcula un puntaje de entorno (0-100) para un inmueble según lo que lo
 * rodea, usando la Overpass API de OpenStreetMap (gratis, sin API key):
 *  - arteria: en qué tipo de calle está (avenida primary/secondary vs. calle interna)
 *  - transporte: estaciones de tren/subte y paradas de colectivo cercanas
 *  - comercio: densidad de comercios (shop=*) alrededor
 *  - servicios: bancos, farmacias, escuelas, supermercados, restaurantes, etc.
 *
 * Nunca rompe el alta/edición: si Overpass falla o tarda, devuelve null y
 * el inmueble se guarda igual (sin puntaje).
 */
class PuntajeEntornoService
{
    // Radio de análisis en metros
    const RADIO = 400;
    // Radio más chico para detectar la arteria (la calle que pasa por la puerta)
    const RADIO_ARTERIA = 150;

    // Peso de cada tipo de vía según "tránsito" estimado
    const ARTERIAS = [
        'motorway' => 35, 'trunk' => 35, 'primary' => 35, 'secondary' => 28,
        'tertiary' => 22, 'residential' => 8, 'unclassified' => 6, 'service' => 4, 'living_street' => 5,
    ];

    const ARTERIA_LABEL = [
        'motorway' => 'Autopista', 'trunk' => 'Ruta troncal', 'primary' => 'Avenida principal',
        'secondary' => 'Avenida secundaria', 'tertiary' => 'Calle asfaltada transitada',
        'residential' => 'Calle barrial', 'unclassified' => 'Calle sin clasificar',
        'service' => 'Calle interna / pasaje', 'living_street' => 'Calle peatonal',
    ];

    // Mirrors públicos de Overpass: si uno está caído/rate-limiteado/bloqueado, se prueba el siguiente.
    // OJO: en hosting compartido la salida a veces cuelga (firewall) -> timeouts cortos para no trabar el guardado;
    // el fallback posta desde el navegador (botón "Calcular desde el navegador" en la edición).
    const MIRRORS = [
        'https://overpass-api.de/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter',
        'https://overpass.private.coffee/api/interpreter',
    ];

    /**
     * @return array|null ['puntaje' => int 0-100, 'detalle' => array]
     */
    public static function calcular(?float $lat, ?float $lng): ?array
    {
        if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng)) {
            return null;
        }
        $q = self::queryOverpass((float) $lat, (float) $lng);
        $ultimoError = null;
        foreach (self::MIRRORS as $url) {
            try {
                $resp = Http::connectTimeout(5)->timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'ArumasoftPortal/1.0 (portal inmobiliario AR)',
                        'Accept' => 'application/json',
                    ])
                    ->get($url, ['data' => $q]);
                if ($resp->ok()) {
                    $elements = $resp->json('elements') ?? [];
                    return self::puntuar($elements);
                }
                $ultimoError = 'HTTP ' . $resp->status() . ' ' . substr((string) $resp->body(), 0, 200);
            } catch (\Throwable $e) {
                $ultimoError = get_class($e) . ': ' . $e->getMessage();
            }
            Log::warning('PuntajeEntorno: mirror falló', ['url' => $url, 'error' => $ultimoError]);
        }
        Log::warning('PuntajeEntorno: todos los mirrors fallaron', ['lat' => $lat, 'lng' => $lng, 'ultimo_error' => $ultimoError]);
        return null;
    }

    protected static function queryOverpass(float $lat, float $lng): string
    {
        $r = self::RADIO;
        $ra = self::RADIO_ARTERIA;
        return '[out:json][timeout:15];'
            . '('
            // arterias (ways) cerca de la puerta
            . "way(around:{$ra},{$lat},{$lng})[highway~\"^(motorway|trunk|primary|secondary|tertiary|residential|unclassified|service|living_street)$\"];"
            // transporte: tren/subte
            . "nwr(around:{$r},{$lat},{$lng})[railway~\"^(station|subway_entrance|halt)$\"];"
            // transporte: colectivos
            . "nwr(around:{$r},{$lat},{$lng})[public_transport~\"^(stop_position|station|platform)$\"];"
            . "nwr(around:{$r},{$lat},{$lng})[bus=yes];"
            // comercios
            . "nwr(around:{$r},{$lat},{$lng})[shop];"
            // servicios
            . "nwr(around:{$r},{$lat},{$lng})[amenity~\"^(restaurant|cafe|bar|fast_food|bank|pharmacy|hospital|clinic|school|kindergarten|university|marketplace|supermarket|convenience|fuel|bus_station|police|fire_station|post_office)$\"];"
            . ');'
            . 'out tags center 300;';
    }

    public static function puntuar(array $elements): array
    {
        $mejorArteria = null; // ['tipo'=>, 'puntos'=>]
        $estacionesTren = 0;   // railway station / subway_entrance
        $paradasBus = 0;       // public_transport stop / bus=yes
        $comercios = 0;
        $servicios = 0;

        foreach ($elements as $el) {
            $tags = $el['tags'] ?? [];
            if (($el['type'] ?? '') === 'way' && !empty($tags['highway'])) {
                $tipo = $tags['highway'];
                $pts = self::ARTERIAS[$tipo] ?? 0;
                if ($mejorArteria === null || $pts > $mejorArteria['puntos']) {
                    $mejorArteria = ['tipo' => $tipo, 'puntos' => $pts];
                }
            } elseif (!empty($tags['railway'])) {
                $estacionesTren++;
            } elseif (!empty($tags['shop'])) {
                $comercios++;
            } elseif (!empty($tags['amenity'])) {
                $servicios++;
            } elseif (!empty($tags['public_transport']) || !empty($tags['bus'])) {
                $paradasBus++;
            }
        }

        // Transporte: una estación de tren/subte pesa mucho; paradas de bus suman poco
        $ptsTransporte = min($estacionesTren > 0 ? 30 : 0, 30) + min($paradasBus * 3, 15);
        $ptsComercio = min($comercios * 2, 20);
        $ptsServicios = min($servicios * 2, 15);
        $ptsArteria = $mejorArteria['puntos'] ?? 0;

        $puntaje = max(0, min(100, $ptsArteria + $ptsTransporte + $ptsComercio + $ptsServicios));

        return [
            'puntaje' => $puntaje,
            'detalle' => [
                'arteria' => $mejorArteria ? [
                    'tipo' => $mejorArteria['tipo'],
                    'label' => self::ARTERIA_LABEL[$mejorArteria['tipo']] ?? $mejorArteria['tipo'],
                    'puntos' => $ptsArteria,
                ] : null,
                'transporte' => [
                    'estaciones_tren_subte' => $estacionesTren,
                    'paradas_bus' => $paradasBus,
                    'puntos' => $ptsTransporte,
                ],
                'comercio' => ['cantidad' => $comercios, 'puntos' => $ptsComercio],
                'servicios' => ['cantidad' => $servicios, 'puntos' => $ptsServicios],
                'radio_m' => self::RADIO,
            ],
        ];
    }
}
