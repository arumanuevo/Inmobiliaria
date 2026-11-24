<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración global del portal (tabla `configuracion`, clave/valor).
 * La administra el superadmin desde /admin/configuracion.
 *
 * Defensiva: si la tabla todavía no existe en la BD (falta correr
 * saas_configuracion.sql), devuelve los defaults sin tirar error.
 */
class Configuracion
{
    protected static ?bool $tablaExiste = null;

    /** @var array<string, mixed> cache por request */
    protected static array $cache = [];

    public static function tablaExiste(): bool
    {
        if (static::$tablaExiste === null) {
            try {
                static::$tablaExiste = Schema::hasTable('configuracion');
            } catch (\Throwable $e) {
                static::$tablaExiste = false;
            }
        }
        return static::$tablaExiste;
    }

    /**
     * Lee una clave; si no existe o está vacía devuelve $default.
     */
    public static function obtener(string $clave, $default = null)
    {
        if (! static::tablaExiste()) {
            return $default;
        }
        if (! array_key_exists($clave, static::$cache)) {
            try {
                $fila = DB::table('configuracion')->where('clave', $clave)->first();
                static::$cache[$clave] = $fila ? $fila->valor : null;
            } catch (\Throwable $e) {
                static::$cache[$clave] = null;
            }
        }
        $v = static::$cache[$clave];

        return ($v === null || $v === '') ? $default : $v;
    }

    /**
     * Crea o actualiza una clave (sin pisar created_at en updates).
     */
    public static function guardar(string $clave, $valor, ?string $descripcion = null): void
    {
        if (! static::tablaExiste()) {
            return;
        }
        try {
            $existe = DB::table('configuracion')->where('clave', $clave)->exists();
            if ($existe) {
                $datos = ['valor' => $valor, 'updated_at' => now()];
                if ($descripcion !== null) {
                    $datos['descripcion'] = $descripcion;
                }
                DB::table('configuracion')->where('clave', $clave)->update($datos);
            } else {
                DB::table('configuracion')->insert([
                    'clave' => $clave,
                    'valor' => $valor,
                    'descripcion' => $descripcion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            unset(static::$cache[$clave]);
        } catch (\Throwable $e) {
            // sin tabla / sin conexión: no romper el flujo
        }
    }

    // ---------- Comisión por operación cerrada ----------

    public static function comisionHabilitada(): bool
    {
        return (bool) (int) static::obtener('comision_habilitada', '1');
    }

    public static function comisionPctDefault(): float
    {
        $v = static::obtener('comision_pct_default', '3.00');
        if (! is_numeric($v)) {
            return 3.0;
        }
        return max(0.0, min(100.0, (float) $v));
    }

    public static function comisionNota(): string
    {
        return (string) static::obtener(
            'comision_nota',
            'En locaciones residenciales la comisión corresponde al locador, no al inquilino (Ley 27.551). El porcentaje se calcula sobre el precio declarado de la operación.'
        );
    }
}
