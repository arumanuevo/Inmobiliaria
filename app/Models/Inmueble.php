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
        'carga_asistida', 'declaracion_propietario',
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
    ];

    public static function tiposPropiedad(): array
    {
        return [
            'casa' => 'Casa', 'departamento' => 'Departamento', 'ph' => 'PH', 'duplex' => 'Dúplex',
            'terreno' => 'Terreno', 'local' => 'Local comercial', 'oficina' => 'Oficina',
            'galpon' => 'Galpón', 'quinta' => 'Quinta', 'campo' => 'Campo', 'cochera' => 'Cochera',
        ];
    }

    public function getPrecioFormateadoAttribute(): string
    {
        $simbolo = (($this->moneda ?? 'ARS') === 'USD') ? 'U$S ' : '$';
        return $simbolo . number_format((float) $this->precio, 2);
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
        foreach (['tipo_operacion', 'tipo_propiedad', 'moneda'] as $k) {
            if (!empty($f[$k])) {
                $query->where($k, $f[$k]);
            }
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
        if (!empty($f['ciudad'])) {
            $query->where('ciudad', 'like', '%' . $f['ciudad'] . '%');
        }
        return $query;
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'inmueble_id');
    }
}


