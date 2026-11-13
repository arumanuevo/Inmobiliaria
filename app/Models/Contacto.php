<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $table = 'contactos';

    protected $fillable = [
        'nombre', 'email', 'telefono', 'mensaje', 'inmobiliaria_id', 'inmueble_id'
    ];

    public $timestamps = false;
}

