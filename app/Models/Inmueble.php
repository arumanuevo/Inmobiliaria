<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    protected $table = 'inmuebles';

    protected $fillable = [
        'usuario_id', 'tipo_operacion', 'titulo', 'descripcion', 'precio',
        'superficie_cubierta', 'habitaciones', 'banos', 'anio_construccion',
        'direccion', 'ciudad', 'estado', 'codigo_postal', 'coordenadas',
        'piscina', 'jardin', 'garaje', 'ascensor', 'fotos', 'destacado',
        'propdom', 'propnombre', 'propdni', 'propemail', 'proptel' // Nuevos campos
    ];

    protected $casts = [
        'fotos' => 'array',
        'destacado' => 'boolean',
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
}


