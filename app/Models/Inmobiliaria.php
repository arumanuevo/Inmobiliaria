<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inmobiliaria extends Model
{
    protected $table = 'inmobiliarias';

    protected $fillable = [
        'usuario_id', 'plan_id', 'nombre_comercial', 'slug', 'logo',
        'descripcion', 'direccion', 'ciudad', 'provincia', 'codigo_postal',
        'telefono', 'whatsapp', 'email', 'sitio_web',
        'facebook', 'instagram', 'color_tema', 'estado', 'plan_vence_at',
    ];

    protected $casts = [
        'plan_vence_at' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function inmuebles()
    {
        return $this->hasMany(Inmueble::class, 'inmobiliaria_id');
    }

    public function inmueblesPublicados()
    {
        return $this->inmuebles()->where('publicado', 1);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function limiteInmuebles(): int
    {
        return (int) ($this->plan->limite_inmuebles ?? 5);
    }

    public function limiteDestacados(): int
    {
        return (int) ($this->plan->limite_destacados ?? 1);
    }

    public static function generarSlug(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'inmobiliaria';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
