<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Insumo;
use App\Models\DetalleReceta;
use Illuminate\Http\Request;

class DetalleRecetaController extends Controller
{
    public function index($id)
    {
        $receta = Receta::with(['producto', 'detalles.insumo'])->findOrFail($id);
        $insumos = Insumo::where('activo', 1)->get();

        return view('recetas.insumos', compact('receta', 'insumos'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'id_insumo' => 'required|exists:insumos,id_insumo',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $existe = DetalleReceta::where('id_receta', $id)
            ->where('id_insumo', $request->id_insumo)
            ->exists();

        if ($existe) {
            return redirect()->route('recetas.insumos', $id)
                ->with('error', 'Ese insumo ya está agregado a la receta');
        }

        DetalleReceta::create([
            'id_receta' => $id,
            'id_insumo' => $request->id_insumo,
            'cantidad' => $request->cantidad,
        ]);

        return redirect()->route('recetas.insumos', $id)
            ->with('success', 'Insumo agregado correctamente');
    }

    public function destroy($id)
    {
        $detalle = DetalleReceta::findOrFail($id);
        $id_receta = $detalle->id_receta;
        $detalle->delete();

        return redirect()->route('recetas.insumos', $id_receta)
            ->with('success', 'Insumo eliminado de la receta');
    }
}