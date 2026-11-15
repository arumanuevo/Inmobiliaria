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
        'propdom', 'propnombre', 'propdni', 'propemail', 'proptel'
    ];

    protected $casts = [
        'fotos' => 'array',
        'destacado' => 'boolean',
        'publicado' => 'boolean',
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
}


