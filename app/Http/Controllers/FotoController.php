<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Foto;
use App\Models\Inmueble;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    public function store(Request $request, $inmuebleId)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'descripcion' => 'nullable|string'
        ]);

        $inmueble = Inmueble::findOrFail($inmuebleId);
        $path = $request->file('foto')->store('fotos', 'public');

        Foto::create([
            'inmueble_id' => $inmueble->id,
            'ruta' => $path,
            'tipo' => $request->file('foto')->getClientOriginalExtension(),
            'descripcion' => $request->descripcion
        ]);

        return back()->with('success', 'Foto subida correctamente');
    }

    public function destroy($id)
    {
        $foto = Foto::findOrFail($id);
        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();

        return back()->with('success', 'Foto eliminada correctamente');
    }
}
