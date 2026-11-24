<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Valuacion extends Model
{
    protected $table = 'valuaciones';

    protected $fillable = [
        'usuario_id', 'inmobiliaria_id', 'inmueble_id',
        'direccion', 'lat', 'lng', 'ciudad', 'tipo_operacion', 'tipo_propiedad',
        'ambientes', 'sup_cubierta', 'sup_total',
        'valor_medio', 'valor_min', 'valor_max', 'pm2_mediana',
        'comparables', 'detallada', 'ip', 'user_agent',
    ];

    protected $casts = [
        'comparables' => 'array',
        'detallada' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
    ];

    /**
     * ¿Existe la tabla en la BD? (defensivo: permite deployar antes de correr el SQL)
     */
    public static function tablaExiste(): bool
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = Schema::hasTable('valuaciones');
            } catch (\Throwable $e) {
                $cache = false;
            }
        }

        return $cache;
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function inmobiliaria()
    {
        return $this->belongsTo(Inmobiliaria::class, 'inmobiliaria_id');
    }

    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class, 'inmueble_id');
    }
}
