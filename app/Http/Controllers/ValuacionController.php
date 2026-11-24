<?php

namespace App\Http\Controllers;

use App\Models\Inmueble;
use App\Models\Valuacion;
use App\Support\Valuador;
use Illuminate\Http\Request;

class ValuacionController extends Controller
{
    /**
     * Gate de acceso: la valuación de mercado es SOLO para usuarios con plan
     * de pago (gratis/particular ven pantalla de upsell a /sumate).
     * Premium además desbloquea el reporte detallado y el valor por inmueble.
     */
    protected function gate(): ?\App\Models\Inmobiliaria
    {
        $inm = auth()->user()->inmobiliaria;
        if (! $inm || ! $inm->tieneValuacion()) {
            return null;
        }

        return $inm;
    }

    // GET /valuar — formulario (con mapa + geocoder). ?inmueble=ID precarga (Premium)
    public function form(Request $request)
    {
        $inm = $this->gate();
        if (! $inm) {
            return view('valuar.bloqueado');
        }

        $esPremium = $inm->tieneValuacionPremium();

        $prefill = null;
        if ($request->filled('inmueble') && $esPremium) {
            $inmueble = Inmueble::where('id', (int) $request->input('inmueble'))
                ->where('inmobiliaria_id', $inm->id)
                ->first();
            if ($inmueble) {
                $coords = array_map('trim', explode(',', (string) $inmueble->coordenadas));
                $prefill = [
                    'inmueble_id' => $inmueble->id,
                    'direccion' => trim($inmueble->direccion . ', ' . $inmueble->ciudad),
                    'lat' => (count($coords) === 2 && is_numeric($coords[0])) ? (float) $coords[0] : null,
                    'lng' => (count($coords) === 2 && is_numeric($coords[1])) ? (float) $coords[1] : null,
                    'ciudad' => $inmueble->ciudad,
                    'tipo_operacion' => $inmueble->tipo_operacion ?: 'venta',
                    'tipo_propiedad' => $inmueble->tipo_propiedad,
                    'ambientes' => $inmueble->ambientes,
                    'sup_cubierta' => $inmueble->superficie_cubierta,
                    'precio' => $inmueble->precio,
                ];
            }
        }

        $tipos = Inmueble::tiposPropiedad();

        return view('valuar', compact('inm', 'esPremium', 'prefill', 'tipos'));
    }

    // POST /valuar — calcula la estimación y guarda la valuación
    public function estimar(Request $request)
    {
        $inm = $this->gate();
        if (! $inm) {
            return redirect()->route('valuar')->with('error', 'La valuación de mercado está disponible solo en planes de pago. Mirá los planes en /sumate.');
        }
        $esPremium = $inm->tieneValuacionPremium();

        $data = $request->validate([
            'direccion' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'ciudad' => 'nullable|string|max:120',
            'tipo_operacion' => 'required|in:venta,alquiler',
            'tipo_propiedad' => 'nullable|string|max:40',
            'ambientes' => 'nullable|integer|min:0|max:30',
            'sup_cubierta' => 'required|numeric|min:5|max:100000',
            'inmueble_id' => 'nullable|integer',
            'comparables_ids' => 'nullable|array',
            'comparables_ids.*' => 'integer',
        ]);

        // El detalle con comparables elegibles es solo Premium: si no lo es, se ignora
        if (! $esPremium) {
            $data['comparables_ids'] = null;
        }

        // Si viene de un inmueble propio (botón Premium), validar que sea suyo
        $inmueble = null;
        if (! empty($data['inmueble_id']) && $esPremium) {
            $inmueble = Inmueble::where('id', (int) $data['inmueble_id'])->where('inmobiliaria_id', $inm->id)->first();
        }

        $res = Valuador::estimar($data);

        // Estadísticas de la zona (solo Premium ve el detalle; el resto, teaser)
        $zona = Valuador::estadisticasZona($data['ciudad'] ?? null, $data['tipo_operacion']);

        // Persistir (defensivo si falta la tabla)
        $valuacion = null;
        if ($res['ok'] && Valuacion::tablaExiste()) {
            $valuacion = Valuacion::create([
                'usuario_id' => auth()->id(),
                'inmobiliaria_id' => $inm->id,
                'inmueble_id' => $inmueble?->id,
                'direccion' => $data['direccion'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
                'ciudad' => $data['ciudad'] ?? null,
                'tipo_operacion' => $data['tipo_operacion'],
                'tipo_propiedad' => $data['tipo_propiedad'] ?? null,
                'ambientes' => $data['ambientes'] ?? null,
                'sup_cubierta' => $data['sup_cubierta'],
                'valor_medio' => $res['valor_medio'] ?? null,
                'valor_min' => $res['valor_min'] ?? null,
                'valor_max' => $res['valor_max'] ?? null,
                'pm2_mediana' => $res['pm2_mediana'] ?? null,
                'comparables' => $res['comparables'] ?? null,
                'detallada' => $esPremium,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        // Si es la valuación de un inmueble propio (Premium): guardar en el inmueble + desfase vs precio
        $desfase = null;
        if ($inmueble && $res['ok'] && Inmueble::columnaExiste('valuacion_valor')) {
            $inmueble->valuacion_valor = $res['valor_medio'];
            $inmueble->valuacion_min = $res['valor_min'];
            $inmueble->valuacion_max = $res['valor_max'];
            $inmueble->valuacion_at = now();
            $inmueble->save();
            if ((float) $inmueble->precio > 0) {
                $desfase = round((($res['valor_medio'] - (float) $inmueble->precio) / (float) $inmueble->precio) * 100, 1);
            }
        }

        return view('valuar.resultado', [
            'res' => $res,
            'datos' => $data,
            'esPremium' => $esPremium,
            'zona' => $zona,
            'valuacion' => $valuacion,
            'inmueble' => $inmueble,
            'desfase' => $desfase,
        ]);
    }

    // GET /valuar/reporte/{valuacion} — reporte imprimible (solo Premium, dueño o admin)
    public function reporte(Valuacion $valuacion)
    {
        abort_unless(Valuacion::tablaExiste(), 404); // defensivo si falta correr el SQL

        $user = auth()->user();
        $inm = $user->inmobiliaria;
        $esPremium = $inm && $inm->tieneValuacionPremium();
        abort_unless($esPremium || $user->hasRole('admin'), 403);
        abort_unless($valuacion->usuario_id === $user->id || ($inm && $valuacion->inmobiliaria_id === $inm->id) || $user->hasRole('admin'), 403);

        $zona = Valuador::estadisticasZona($valuacion->ciudad, $valuacion->tipo_operacion ?: 'venta');

        return view('valuar.reporte', compact('valuacion', 'zona'));
    }
}
