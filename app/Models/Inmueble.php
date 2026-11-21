<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    protected $table = 'inmuebles';

    protected $fillable = [
        'usuario_id', 'inmobiliaria_id', 'tipo_operacion', 'tipo_propiedad', 'moneda', 'titulo', 'descripcion', 'precio',
        'superficie_cubierta', 'habitaciones', 'ambientes', 'banos', 'cocheras', 'expensas',
        'anio_construccion', 'orientacion', 'estado_propiedad',
        'direccion', 'ciudad', 'estado', 'codigo_postal', 'coordenadas',
        'piscina', 'jardin', 'garaje', 'ascensor', 'fotos', 'destacado', 'publicado', 'visitas',
        'cerrada', 'tipo_cierre', 'cerrada_at',
        'comision_porcentaje', 'comision_monto', 'comision_estado', 'comision_cobrada_at',
        'carga_asistida', 'declaracion_propietario', 'proximidades', 'uso_detalle',
        'puntaje_entorno', 'puntaje_entorno_detalle', 'puntaje_entorno_at',
        'propdom', 'propnombre', 'propdni', 'propemail', 'proptel'
    ];

    protected $casts = [
        'fotos' => 'array',
        'destacado' => 'boolean',
        'publicado' => 'boolean',
        'cerrada' => 'boolean',
        'cerrada_at' => 'datetime',
        'comision_porcentaje' => 'decimal:2',
        'comision_monto' => 'decimal:2',
        'comision_cobrada_at' => 'datetime',
        'carga_asistida' => 'boolean',
        'declaracion_propietario' => 'boolean',
        'proximidades' => 'array',
        'puntaje_entorno' => 'integer',
        'puntaje_entorno_detalle' => 'array',
        'puntaje_entorno_at' => 'datetime',
    ];

    public static function tiposPropiedad(): array
    {
        return [
            'casa' => 'Casa', 'departamento' => 'Departamento', 'ph' => 'PH', 'duplex' => 'Dúplex',
            'terreno' => 'Terreno', 'local' => 'Local comercial', 'oficina' => 'Oficina',
            'galpon' => 'Galpón', 'quinta' => 'Quinta', 'campo' => 'Campo', 'cochera' => 'Cochera',
        ];
    }

    // Tipos de uso comercial / industrial (para el filtro "uso comercial")
    public static function tiposComerciales(): array
    {
        return ['local', 'oficina', 'galpon'];
    }

    // Tipos agrupados para los <select> con <optgroup> (vivienda / comercial / otros)
    public static function gruposTipos(): array
    {
        return [
            'Vivienda' => [
                'casa' => 'Casa', 'departamento' => 'Departamento', 'ph' => 'PH',
                'duplex' => 'Dúplex', 'quinta' => 'Quinta',
            ],
            'Comercial e industrial' => [
                'local' => 'Local comercial', 'oficina' => 'Oficina',
                'galpon' => 'Galpón', 'cochera' => 'Cochera',
            ],
            'Terrenos y otros' => [
                'terreno' => 'Terreno', 'campo' => 'Campo',
            ],
        ];
    }

    public function esComercial(): bool
    {
        return in_array($this->tipo_propiedad, static::tiposComerciales(), true);
    }

    // Sugerencias de uso/habilitación para el datalist del formulario (comerciales)
    public static function usosComercialesSugeridos(): array
    {
        return [
            'Kiosco', 'Tienda de ropa / calzado', 'Gastronomía / bar', 'Cafetería', 'Panadería',
            'Carnicería / verdulería', 'Farmacia', 'Peluquería', 'Gimnasio', 'Consultorio',
            'Oficina administrativa', 'Estudio profesional', 'Showroom', 'Depósito / logística',
            'Industria liviana', 'Taller mecánico', 'Lavadero de autos', 'Salón de eventos', 'Almacén',
        ];
    }

    // Etiqueta/color del puntaje de entorno (0-100) para mostrar en fichas y tarjetas
    public function puntajeEntornoLabel(): ?array
    {
        if ($this->puntaje_entorno === null) { return null; }
        $p = (int) $this->puntaje_entorno;
        if ($p >= 75) { return ['texto' => 'Ubicación premium', 'color' => '#059669', 'bg' => '#ecfdf5', 'borde' => '#a7f3d0']; }
        if ($p >= 50) { return ['texto' => 'Buena ubicación', 'color' => '#2563eb', 'bg' => '#eff6ff', 'borde' => '#bfdbfe']; }
        if ($p >= 25) { return ['texto' => 'Ubicación regular', 'color' => '#b45309', 'bg' => '#fffbeb', 'borde' => '#fde68a']; }
        return ['texto' => 'Baja circulación', 'color' => '#6b7280', 'bg' => '#f3f4f6', 'borde' => '#e5e7eb'];
    }

    public function getPrecioFormateadoAttribute(): string
    {
        $simbolo = (($this->moneda ?? 'ARS') === 'USD') ? 'U$S ' : '$';
        return $simbolo . number_format((float) $this->precio, 2);
    }

    // Proximidades / ventajas del entorno: lista normalizada [{nombre, metros}] ordenada por cercanía
    public function proximidadesLista(): array
    {
        $raw = $this->proximidades;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $p) {
            if (!is_array($p)) { continue; }
            $nombre = trim((string) ($p['nombre'] ?? ''));
            if ($nombre === '') { continue; }
            $metros = (isset($p['metros']) && is_numeric($p['metros'])) ? (int) $p['metros'] : null;
            $out[] = ['nombre' => mb_substr($nombre, 0, 80), 'metros' => $metros];
        }
        usort($out, function ($a, $b) {
            return ($a['metros'] ?? PHP_INT_MAX) <=> ($b['metros'] ?? PHP_INT_MAX);
        });
        return $out;
    }

    // Icono Font Awesome según palabras clave del nombre del punto de interés
    public static function iconoProximidad(string $nombre): string
    {
        $n = mb_strtolower($nombre);
        $mapa = [
            'tren|ferrocarril' => 'fa-train',
            'subte|metro' => 'fa-subway',
            'colectivo|bus|omnibus|ómnibus' => 'fa-bus',
            'autopista|ruta|peaje' => 'fa-road',
            'supermercado|almacen|almacén' => 'fa-basket-shopping',
            'shopping|local|comercial|negocio' => 'fa-store',
            'plaza|parque|verde' => 'fa-tree',
            'escuela|colegio|jardín de infantes|jardin de infantes' => 'fa-school',
            'universidad|instituto|facultad' => 'fa-graduation-cap',
            'hospital|clínica|clinica|salud|centro de salud' => 'fa-hospital',
            'farmacia|droguería|drogueria' => 'fa-pills',
            'banco|cajero' => 'fa-landmark',
            'comisaría|comisaria|policía|policia|seguridad' => 'fa-shield-halved',
            'gimnasio|gym|club' => 'fa-dumbbell',
            'deportivo|fútbol|futbol|cancha' => 'fa-futbol',
            'gastron|restaurant|bar|café|cafe' => 'fa-utensils',
            'playa|lago|rio|río|costanera' => 'fa-umbrella-beach',
            'iglesia|parroquia|templo|capilla' => 'fa-church',
            'transporte' => 'fa-bus',
        ];
        foreach ($mapa as $regex => $icono) {
            if (preg_match('/' . $regex . '/u', $n)) {
                return $icono;
            }
        }
        return 'fa-star';
    }

    // Sugerencias para el datalist del formulario de proximidades
    public static function sugerenciasProximidad(): array
    {
        return [
            'Estación de tren', 'Boca de subte', 'Parada de colectivo', 'Acceso a autopista',
            'Ruta nacional / provincial', 'Supermercado', 'Centro comercial / shopping', 'Plaza o parque',
            'Escuela', 'Universidad', 'Hospital o clínica', 'Farmacia', 'Banco', 'Comisaría',
            'Gimnasio', 'Club deportivo', 'Zona gastronómica', 'Playa / río / costanera', 'Iglesia',
        ];
    }

    // Relación: un inmueble tiene muchas fotos
    public function fotos()
    {
        return $this->hasMany(Foto::class, 'inmueble_id');
    }

    // Relación: un inmueble pertenece a un usuario (dueño de la inmobiliaria)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación: un inmueble pertenece a una inmobiliaria (tenant del SaaS)
    public function inmobiliaria()
    {
        return $this->belongsTo(Inmobiliaria::class, 'inmobiliaria_id');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', 1);
    }

    public function scopeDeInmobiliaria($query, $inmobiliariaId)
    {
        return $query->where('inmobiliaria_id', $inmobiliariaId);
    }

    // Scope geográfico GIS: busca propiedades en un radio (en km) alrededor de unas coordenadas (lat, lng)
    public function scopeCercanosA($query, float $lat, float $lng, float $radioKm = 10)
    {
        $metros = (float) $radioKm * 1000.0;

        $query->whereNotNull('coordenadas')
            ->where('coordenadas', '!=', '')
            ->where('coordenadas', 'like', '%,%');

        $driver = $query->getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return $query;
        }

        $sqlLng = "CAST(TRIM(SUBSTRING_INDEX(coordenadas, ',', -1)) AS DECIMAL(10,6))";
        $sqlLat = "CAST(TRIM(SUBSTRING_INDEX(coordenadas, ',', 1)) AS DECIMAL(10,6))";

        $distanciaFormula = "ST_Distance_Sphere(POINT({$sqlLng}, {$sqlLat}), POINT(?, ?))";

        return $query
            ->select('inmuebles.*')
            ->selectRaw("({$distanciaFormula} / 1000) AS distancia_km", [$lng, $lat])
            ->whereRaw("{$distanciaFormula} <= ?", [$lng, $lat, $metros])
            ->orderBy('distancia_km', 'asc');
    }

    // Filtros compartidos entre la búsqueda pública y las alertas de búsquedas guardadas
    public function scopeAplicarFiltros($query, array $f)
    {
        if (!empty($f['inmobiliaria'])) {
            $query->whereHas('inmobiliaria', fn ($q) => $q->where('slug', $f['inmobiliaria']));
        }
        if (!empty($f['tipo_anunciante'])) {
            if ($f['tipo_anunciante'] === 'particular') {
                $query->whereHas('usuario.roles', fn ($q) => $q->where('name', 'particular'));
            } elseif ($f['tipo_anunciante'] === 'inmobiliaria') {
                $query->whereHas('usuario.roles', fn ($q) => $q->where('name', 'inmobiliaria'));
            }
        }
        // Filtrado geográfico GIS por radio o por ciudad/pueblo
        if (isset($f['geo_lat']) && isset($f['geo_lng']) && is_numeric($f['geo_lat']) && is_numeric($f['geo_lng'])) {
            $radio = (isset($f['radio_km']) && is_numeric($f['radio_km'])) ? (float) $f['radio_km'] : 10;
            $query->cercanosA((float) $f['geo_lat'], (float) $f['geo_lng'], $radio);
        } elseif (!empty($f['ciudad'])) {
            $query->where('ciudad', 'like', '%' . $f['ciudad'] . '%');
        }
        foreach (['tipo_operacion', 'tipo_propiedad', 'moneda'] as $k) {
            if (!empty($f[$k])) {
                $query->where($k, $f[$k]);
            }
        }
        // Filtro rápido "uso comercial" (local / oficina / galpón)
        if (!empty($f['uso_comercial'])) {
            $query->whereIn('tipo_propiedad', self::tiposComerciales());
        }
        $minimos = [
            'ambientes_min' => 'ambientes',
            'cocheras_min' => 'cocheras',
            'precio_min' => 'precio',
            'superficie_min' => 'superficie_cubierta',
        ];
        foreach ($minimos as $k => $col) {
            if (isset($f[$k]) && is_numeric($f[$k])) {
                $query->where($col, '>=', $f[$k]);
            }
        }
        $maximos = [
            'precio_max' => 'precio',
            'superficie_max' => 'superficie_cubierta',
        ];
        foreach ($maximos as $k => $col) {
            if (isset($f[$k]) && is_numeric($f[$k])) {
                $query->where($col, '<=', $f[$k]);
            }
        }
        foreach (['habitaciones', 'banos'] as $k) {
            if (isset($f[$k]) && is_numeric($f[$k])) {
                $query->where($k, $f[$k]);
            }
        }
        return $query;
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'inmueble_id');
    }
}


