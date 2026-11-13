<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
}
