<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Inmobiliaria extends Model
{
    protected $table = 'inmobiliarias';

    protected $fillable = [
        'usuario_id', 'plan_id', 'nombre_comercial', 'slug', 'logo', 'imagen_portada', 'galeria',
        'descripcion', 'direccion', 'ciudad', 'provincia', 'codigo_postal', 'zonas_cobertura', 'lat', 'lng',
        'telefono', 'whatsapp', 'email', 'sitio_web',
        'facebook', 'instagram', 'color_tema', 'estado', 'plan_vence_at', 'cuit', 'matricula_registro', 'verificada',
        'tema', 'mensaje_destacado', 'mostrar_operaciones', 'mostrar_mapa', 'mostrar_redes',
        'meta_titulo', 'meta_descripcion',
    ];

    protected $casts = [
        'plan_vence_at' => 'date',
        'galeria' => 'array',
        'lat' => 'float',
        'lng' => 'float',
        'mostrar_operaciones' => 'boolean',
        'mostrar_mapa' => 'boolean',
        'mostrar_redes' => 'boolean',
        'verificada' => 'boolean',
    ];

    // Presets de look del perfil público (optativos)
    public static function temas(): array
    {
        return [
            'moderno' => 'Moderno (sans, esquinas redondeadas)',
            'elegante' => 'Elegante (títulos serif, sobrio)',
            'minimal' => 'Minimal (pills, ultra liviano)',
            'corporativo' => 'Corporativo (clásico, confianza)',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function inmuebles()
    {
        return $this->hasMany(Inmueble::class, 'inmobiliaria_id');
    }

    public function inmueblesPublicados()
    {
        return $this->inmuebles()->where('publicado', 1);
    }

    public function boletos()
    {
        return $this->hasMany(Boleto::class, 'inmobiliaria_id');
    }

    // Operaciones concretadas (vendidas/alquiladas): social proof del perfil
    public function operaciones()
    {
        // operaciones concretadas: solo cierres aprobados por el superadmin
        // (defensivo: si falta la columna, cuenta todas las cerradas)
        $q = $this->inmuebles()->where('cerrada', 1);
        if (\App\Models\Inmueble::columnaExiste('cierre_estado')) {
            $q->where('cierre_estado', 'aprobado');
        }

        return $q;
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    // ¿El plan actual es Premium? (gating de features como el SEO custom del perfil)
    public function esPremium(): bool
    {
        return $this->plan?->slug === 'premium';
    }

    // ¿El plan actual es de PAGO? (precio_mensual > 0: inicial/básico/profesional/premium)
    public function planEsPago(): bool
    {
        return (float) ($this->plan->precio_mensual ?? 0) > 0;
    }

    // ¿El plan puede usar la valuación de mercado básica? Solo planes pagos (no gratis/particular)
    public function tieneValuacion(): bool
    {
        return $this->planEsPago();
    }

    // ¿El plan incluye la valuación COMPLETA (reporte PDF, comparables elegibles, valor por inmueble)?
    public function tieneValuacionPremium(): bool
    {
        return $this->esPremium();
    }

    /**
     * ¿Existe la columna en la BD? (cacheada por request). Permite deployar el código
     * ANTES de correr el SQL nuevo en phpMyAdmin sin que las páginas tiren error 1054.
     */
    public static function columnaExiste(string $columna): bool
    {
        static $cache = [];
        if (! array_key_exists($columna, $cache)) {
            $cache[$columna] = Schema::hasColumn((new static)->getTable(), $columna);
        }

        return $cache[$columna];
    }

    /**
     * Color para navbar/botones con texto blanco: si el color tema es claro (mal contraste
     * con blanco), se oscurece automáticamente mezclándolo con negro.
     */
    public function colorNavbar(): string
    {
        return self::oscurecerSiClaro($this->color_tema ?: '#0d6efd');
    }

    public static function oscurecerSiClaro(string $hex): string
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '#0d6efd';
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $lum = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
        if ($lum > 0.45) { // claro: mezclar con negro al 45% para que el texto blanco se lea bien
            $r = (int) round($r * 0.55);
            $g = (int) round($g * 0.55);
            $b = (int) round($b * 0.55);
        }

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public function limiteInmuebles(): int
    {
        return (int) ($this->plan->limite_inmuebles ?? 5);
    }

    public function limiteDestacados(): int
    {
        return (int) ($this->plan->limite_destacados ?? 1);
    }

    // Degradación de plan: despublica/desmarca lo que exceda los límites del plan actual.
    // No borra nada: los excedentes quedan como borrador (publicado=0) o sin destacar.
    public function aplicarLimitesDelPlan(): array
    {
        $plan = $this->plan ?: Plan::gratis();
        $resumen = ['despublicados' => 0, 'destacados_quitados' => 0];

        $limiteInmuebles = (int) ($plan->limite_inmuebles ?? 5);
        $publicados = $this->inmuebles()->where('publicado', 1)->orderByDesc('updated_at')->get();
        if ($publicados->count() > $limiteInmuebles) {
            foreach ($publicados->slice($limiteInmuebles) as $inmueble) {
                $inmueble->publicado = 0;
                $inmueble->save();
                $resumen['despublicados']++;
            }
        }

        $limiteDestacados = (int) ($plan->limite_destacados ?? 0);
        $destacados = $this->inmuebles()->where('destacado', 1)->orderBy('updated_at')->get();
        if ($destacados->count() > $limiteDestacados) {
            foreach ($destacados->slice($limiteDestacados) as $inmueble) {
                $inmueble->destacado = 0;
                $inmueble->save();
                $resumen['destacados_quitados']++;
            }
        }

        return $resumen;
    }

    public static function generarSlug(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'inmobiliaria';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
