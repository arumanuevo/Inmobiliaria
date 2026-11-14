<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    protected $table = 'boletos';

    protected $fillable = [
        'inmobiliaria_id', 'plan_id', 'concepto', 'monto', 'estado',
        'vence_at', 'mp_preference_id', 'mp_payment_id', 'pagado_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'vence_at' => 'date',
        'pagado_at' => 'datetime',
    ];

    public function inmobiliaria()
    {
        return $this->belongsTo(Inmobiliaria::class, 'inmobiliaria_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
