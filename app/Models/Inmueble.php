<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    protected $table = 'inmuebles';

    protected $fillable = [
        'usuario_id', 'inmobiliaria_id', 'tipo_operacion', 'titulo', 'descripcion', 'precio',
        'superficie_cubierta', 'habitaciones', 'banos', 'anio_construccion',
        'direccion', 'ciudad', 'estado', 'codigo_postal', 'coordenadas',
        'piscina', 'jardin', 'garaje', 'ascensor', 'fotos', 'destacado', 'publicado', 'visitas',
        'propdom', 'propnombre', 'propdni', 'propemail', 'proptel' // Nuevos campos
    ];

    protected $casts = [
        'fotos' => 'array',
        'destacado' => 'boolean',
        'publicado' => 'boolean',
    ];

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


