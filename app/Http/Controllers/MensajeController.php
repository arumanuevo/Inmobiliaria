<?php

namespace App\Http\Controllers;

use App\Mail\AvisoMail;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MensajeController extends Controller
{
    // ---------- Bandeja del usuario logueado ----------

    public function misMensajes(Request $request)
    {
        $soloNoLeidos = $request->boolean('no_leidos');

        $query = auth()->user()->mensajes()->latest();
        if ($soloNoLeidos) {
            $query->noLeidos();
        }

        $mensajes = $query->paginate(15)->withQueryString();
        $noLeidos = auth()->user()->mensajesNoLeidos()->count();

        return view('mensajes.index', compact('mensajes', 'noLeidos', 'soloNoLeidos'));
    }

    public function marcarLeido(Mensaje $mensaje)
    {
        abort_if((int) $mensaje->usuario_id !== (int) auth()->id(), 403);

        if (! $mensaje->leido_at) {
            $mensaje->leido_at = now();
            $mensaje->save();
        }

        return back()->with('success', 'Mensaje marcado como leído.');
    }

    public function marcarTodosLeidos()
    {
        auth()->user()->mensajesNoLeidos()->update(['leido_at' => now()]);

        return back()->with('success', 'Todos los mensajes quedaron como leídos.');
    }

    // ---------- Envío de avisos (solo superadmin) ----------

    public function adminIndex()
    {
        abort_unless(auth()->user()->esSuperAdmin(), 403, 'Solo el superadmin puede enviar avisos.');

        // Resumen de envíos: agrupado por (titulo, tipo, created_at) con cantidad de destinatarios y leídos
        $enviados = Mensaje::query()
            ->select('titulo', 'tipo', 'created_at')
            ->selectRaw('COUNT(*) as destinatarios')
            ->selectRaw('SUM(CASE WHEN leido_at IS NOT NULL THEN 1 ELSE 0 END) as leidos')
            ->groupBy('titulo', 'tipo', 'created_at')
            ->latest('created_at')
            ->paginate(15);

        // Detalle de destinatarios de los envíos de esta página (para el desplegable "ver destinatarios")
        $detalles = collect();
        if ($enviados->count()) {
            $detalles = Mensaje::with('usuario:id,name,email')
                ->whereIn('created_at', $enviados->pluck('created_at')->all())
                ->orderBy('leido_at')
                ->get()
                ->groupBy(fn ($m) => (string) $m->created_at);
        }

        $usuarios = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.mensajes', compact('enviados', 'usuarios', 'detalles'));
    }

    public function adminStore(Request $request)
    {
        abort_unless(auth()->user()->esSuperAdmin(), 403, 'Solo el superadmin puede enviar avisos.');

        $data = $request->validate([
            'titulo' => 'required|string|max:150',
            'cuerpo' => 'required|string|max:5000',
            'tipo' => 'required|in:aviso,planes,mantenimiento,otro',
            'destinatarios' => 'required|in:todos,inmobiliarias,particulares,usuario',
            'usuario_id' => 'required_if:destinatarios,usuario|nullable|integer|exists:users,id',
        ]);

        // Resolver destinatarios según el segmento elegido
        $destinatarios = match ($data['destinatarios']) {
            'todos' => User::query()->get(['id', 'name', 'email']),
            'inmobiliarias' => User::whereHas('roles', fn ($q) => $q->where('name', 'inmobiliaria'))->get(['id', 'name', 'email']),
            'particulares' => User::whereHas('roles', fn ($q) => $q->where('name', 'particular'))->get(['id', 'name', 'email']),
            'usuario' => User::where('id', (int) $data['usuario_id'])->get(['id', 'name', 'email']),
        };

        if ($destinatarios->isEmpty()) {
            return back()->with('error', 'No hay usuarios en ese segmento para enviar el aviso.')->withInput();
        }

        // Mismo created_at en todas las filas del envío para poder agruparlas bien en el historial.
        // Por cada destinatario: 1 fila en la bandeja interna del panel + 1 email (SMTP, en el mismo request).
        $now = now();
        $mailsOk = 0;
        $mailsFallaron = 0;
        foreach ($destinatarios as $u) {
            Mensaje::create([
                'usuario_id' => $u->id,
                'titulo' => $data['titulo'],
                'cuerpo' => $data['cuerpo'],
                'tipo' => $data['tipo'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($u->email) {
                try {
                    Mail::to($u->email)->send(new AvisoMail($data['titulo'], $data['cuerpo'], $data['tipo']));
                    $mailsOk++;
                } catch (\Throwable $e) {
                    $mailsFallaron++;
                    Log::warning("Aviso no enviado por email a {$u->email}: " . $e->getMessage());
                }
            }
        }

        $total = $destinatarios->count();
        $mensaje = "Aviso enviado a {$total} " . \Illuminate\Support\Str::plural('usuario', $total) . ' (bandeja interna del panel).';
        $mensaje .= " Emails: {$mailsOk} enviados" . ($mailsFallaron > 0 ? ", {$mailsFallaron} fallaron (ver storage/logs/laravel.log)" : '') . '.';

        return redirect()->route('admin.mensajes')->with('success', $mensaje);
    }
}
