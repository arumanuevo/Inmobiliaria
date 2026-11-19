<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCarga extends Model
{
    protected $table = 'solicitudes_carga';

    protected $fillable = ['inmobiliaria_id', 'titulo', 'detalle', 'estado', 'inmueble_id', 'atendida_at'];

    protected $casts = [
        'atendida_at' => 'datetime',
    ];

    public function inmobiliaria()
    {
        return $this->belongsTo(Inmobiliaria::class, 'inmobiliaria_id');
    }

    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class, 'inmueble_id');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
