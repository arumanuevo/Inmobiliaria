<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'usuario_id', 'titulo', 'cuerpo', 'tipo', 'leido_at', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'leido_at' => 'datetime',
    ];

    public static function tipos(): array
    {
        return [
            'aviso' => 'Aviso general',
            'planes' => 'Planes y precios',
            'mantenimiento' => 'Mantenimiento',
            'otro' => 'Otro',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeNoLeidos($query)
    {
        return $query->whereNull('leido_at');
    }

    public function getLeidoAttribute(): bool
    {
        return ! is_null($this->leido_at);
    }
}
