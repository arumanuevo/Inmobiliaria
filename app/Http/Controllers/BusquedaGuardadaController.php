<?php

namespace App\Http\Controllers;

use App\Models\BusquedaGuardada;
use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;

class BusquedaGuardadaController extends Controller
{
    protected static $clavesFiltros = [
        'inmobiliaria', 'tipo_operacion', 'tipo_propiedad', 'moneda',
        'ambientes_min', 'cocheras_min', 'precio_min', 'precio_max',
        'habitaciones', 'banos', 'superficie_min', 'superficie_max', 'ciudad',
    ];

    // Pública: cualquier visitante guarda la búsqueda actual y recibe alertas por email
    public function guardar(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'filtros_json' => 'nullable|string|max:4000',
            'g-recaptcha-response' => ['nullable', new RecaptchaRule],
        ]);

        $filtros = [];
        if (!empty($data['filtros_json'])) {
            $dec = json_decode($data['filtros_json'], true);
            if (is_array($dec)) {
                foreach (static::$clavesFiltros as $k) {
                    if (isset($dec[$k]) && is_scalar($dec[$k]) && $dec[$k] !== '') {
                        $filtros[$k] = (string) $dec[$k];
                    }
                }
            }
        }
        ksort($filtros);
        $filtrosJson = json_encode($filtros);

        $existente = BusquedaGuardada::where('email', $data['email'])
            ->where('filtros', $filtrosJson)
            ->where('activa', 1)
            ->first();

        if ($existente) {
            return back()->with('success', 'Ya tenés guardada esta búsqueda: te avisamos por email cuando aparezca algo nuevo.');
        }

        BusquedaGuardada::create([
            'user_id' => auth()->id(),
            'nombre' => $data['nombre'] ?? null,
            'email' => $data['email'],
            'filtros' => $filtros,
            'activa' => 1,
        ]);

        return back()->with('success', 'Búsqueda guardada. Te avisamos por email cuando publiquen una propiedad que coincida.');
    }

    // Listado de búsquedas guardadas del usuario logueado
    public function misBusquedas()
    {
        $user = auth()->user();
        $busquedas = BusquedaGuardada::where('activa', 1)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('email', $user->email);
            })
            ->latest()
            ->get();

        return view('busquedas.mis', compact('busquedas'));
    }

    public function destroy(BusquedaGuardada $busqueda)
    {
        $user = auth()->user();
        abort_unless($busqueda->user_id === $user->id || $busqueda->email === $user->email, 403);

        $busqueda->activa = 0;
        $busqueda->save();

        return back()->with('success', 'Búsqueda eliminada. Ya no recibirás alertas de ella.');
    }
}
