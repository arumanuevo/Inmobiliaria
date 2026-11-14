<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleto;
use App\Models\Inmobiliaria;
use App\Models\Plan;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function inmobiliarias()
    {
        $inmobiliarias = Inmobiliaria::with(['usuario', 'plan'])->latest()->paginate(15);
        $planes = Plan::where('activo', 1)->orderBy('precio_mensual')->get();

        return view('admin.inmobiliarias', compact('inmobiliarias', 'planes'));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,activa,suspendida',
        ]);

        $inmobiliaria = Inmobiliaria::findOrFail($id);
        $inmobiliaria->estado = $request->input('estado');
        $inmobiliaria->save();

        return back()->with('success', 'Estado de la inmobiliaria actualizado.');
    }

    public function cambiarPlan(Request $request, $id)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:planes,id',
            'plan_vence_at' => 'nullable|date',
        ]);

        $inmobiliaria = Inmobiliaria::findOrFail($id);
        $inmobiliaria->plan_id = $request->input('plan_id');
        $inmobiliaria->plan_vence_at = $request->input('plan_vence_at');
        $inmobiliaria->save();

        return back()->with('success', 'Plan de la inmobiliaria actualizado.');
    }

    // Panel de boletos de suscripción
    public function boletos()
    {
        $boletos = Boleto::with(['inmobiliaria', 'plan'])->latest()->paginate(20);
        $inmobiliarias = Inmobiliaria::orderBy('nombre_comercial')->get();
        $planes = Plan::where('activo', 1)->orderBy('precio_mensual')->get();

        return view('admin.boletos', compact('boletos', 'inmobiliarias', 'planes'));
    }

    public function crearBoleto(Request $request)
    {
        $data = $request->validate([
            'inmobiliaria_id' => 'required|integer|exists:inmobiliarias,id',
            'plan_id' => 'nullable|integer|exists:planes,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'vence_at' => 'nullable|date',
        ]);

        Boleto::create($data + ['estado' => 'pendiente']);

        return back()->with('success', 'Boleto creado correctamente.');
    }

    public function cambiarEstadoBoleto(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,vencido,cancelado',
        ]);

        $boleto = Boleto::findOrFail($id);
        $boleto->estado = $request->input('estado');

        if ($boleto->estado === 'pagado') {
            $boleto->pagado_at = now();

            if ($boleto->plan_id) {
                $inmobiliaria = $boleto->inmobiliaria;
                $inmobiliaria->plan_id = $boleto->plan_id;
                $inmobiliaria->plan_vence_at = now()->addMonth()->toDateString();
                if ($inmobiliaria->estado === 'pendiente') {
                    $inmobiliaria->estado = 'activa';
                }
                $inmobiliaria->save();
            }
        }

        $boleto->save();

        return back()->with('success', 'Estado del boleto actualizado.');
    }
}
