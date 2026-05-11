<?php

namespace App\Http\Controllers;

use App\Models\EntradaInventario;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaInventarioController extends Controller
{
    public function index()
    {
        $entradas = EntradaInventario::with('insumo')->orderByDesc('fecha')->get();
        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        $insumos = Insumo::where('activo', 1)->orderBy('nombre')->get();
        return view('entradas.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $cantidad = str_replace('.', '', $request->cantidad ?? '');

        $request->merge(['cantidad' => $cantidad]);

        $request->validate([
            'id_insumo'   => 'required|exists:insumos,id_insumo',
            'cantidad'    => 'required|numeric|min:0.01',
            'fecha'       => 'required|date',
            'observacion' => 'nullable|string|max:500',
        ], [
            'id_insumo.required'  => 'Seleccione un insumo.',
            'cantidad.required'   => 'Ingrese la cantidad.',
            'cantidad.min'        => 'La cantidad debe ser mayor a cero.',
            'fecha.required'      => 'Ingrese la fecha.',
        ]);

        DB::beginTransaction();

        try {
            EntradaInventario::create([
                'id_insumo'   => $request->id_insumo,
                'cantidad'    => $request->cantidad,
                'fecha'       => $request->fecha,
                'observacion' => $request->observacion,
            ]);

            $insumo = Insumo::findOrFail($request->id_insumo);
            $insumo->stock += $request->cantidad;
            $insumo->save();

            DB::commit();

            return redirect()->route('entradas.index')
                ->with('success', 'Entrada registrada y stock actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('entradas.create')
                ->with('error', 'Error al registrar la entrada: ' . $e->getMessage());
        }
    }
}
