<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'slug', 'precio_mensual', 'limite_inmuebles',
        'limite_destacados', 'limite_fotos', 'activo',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function inmobiliarias()
    {
        return $this->hasMany(Inmobiliaria::class, 'plan_id');
    }

    public static function gratis(): ?self
    {
        return static::where('slug', 'gratis')->where('activo', 1)->first();
    }
}
