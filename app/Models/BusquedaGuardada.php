<?php
namespace App\Models;

use App\Mail\NuevaPropiedadMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BusquedaGuardada extends Model
{
    protected $table = 'busquedas_guardadas';

    protected $fillable = ['user_id', 'nombre', 'email', 'filtros', 'activa'];

    protected $casts = [
        'filtros' => 'array',
        'activa' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Resumen legible de los filtros guardados (para "Mis búsquedas")
    public function resumenFiltros(): string
    {
        $f = is_array($this->filtros) ? $this->filtros : [];
        $labels = [
            'tipo_operacion' => 'Operación',
            'tipo_propiedad' => 'Tipo',
            'moneda' => 'Moneda',
            'precio_min' => 'Precio mín',
            'precio_max' => 'Precio máx',
            'habitaciones' => 'Habitaciones',
            'banos' => 'Baños',
            'ambientes_min' => 'Ambientes mín',
            'cocheras_min' => 'Cocheras mín',
            'superficie_min' => 'Sup. mín',
            'superficie_max' => 'Sup. máx',
            'ciudad' => 'Ciudad',
            'inmobiliaria' => 'Inmobiliaria',
        ];
        $partes = [];
        foreach ($labels as $k => $lbl) {
            if (isset($f[$k]) && $f[$k] !== '' && $f[$k] !== null) {
                $v = $f[$k];
                if ($k === 'tipo_operacion') {
                    $v = ucfirst((string) $v);
                }
                if ($k === 'tipo_propiedad') {
                    $v = Inmueble::tiposPropiedad()[$v] ?? $v;
                }
                $partes[] = $lbl . ': ' . $v;
            }
        }
        return $partes ? implode(' · ', $partes) : 'Sin filtros (todas las propiedades nuevas)';
    }

    // Avisa por email a las búsquedas guardadas que coinciden con una propiedad recién publicada.
    // Sin cron: se ejecuta en el alta/edición de la propiedad. Nunca rompe el alta si falla el mail.
    public static function notificarNuevaPropiedad(Inmueble $inmueble): int
    {
        try {
            if (!$inmueble->publicado) {
                return 0;
            }
            $enviados = 0;
            static::where('activa', 1)->chunkById(100, function ($busquedas) use ($inmueble, &$enviados) {
                foreach ($busquedas as $b) {
                    if ($enviados >= 100) {
                        return false;
                    }
                    $coincide = Inmueble::publicados()
                        ->where('id', $inmueble->id)
                        ->whereHas('inmobiliaria', fn ($q) => $q->where('estado', 'activa'))
                        ->aplicarFiltros(is_array($b->filtros) ? $b->filtros : [])
                        ->exists();
                    if ($coincide) {
                        try {
                            Mail::to($b->email)->send(new NuevaPropiedadMail($inmueble));
                            $enviados++;
                        } catch (\Throwable $e) {
                            Log::warning('Alerta no enviada a ' . $b->email . ': ' . $e->getMessage());
                        }
                    }
                }
            });
            return $enviados;
        } catch (\Throwable $e) {
            Log::warning('Error al notificar nueva propiedad: ' . $e->getMessage());
            return 0;
        }
    }
}
