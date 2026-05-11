<?php

namespace App\Http\Controllers;

use App\Models\ConteoInventario;
use App\Models\DetalleConteo;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConteoInventarioController extends Controller
{
    public function index()
    {
        $conteos = ConteoInventario::withCount('detalles')
            ->orderByDesc('fecha')
            ->orderByDesc('id_conteo')
            ->get();

        return view('conteos.index', compact('conteos'));
    }

    public function create()
    {
        $insumos = Insumo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('conteos.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'    => 'required|date',
            'stock'    => 'required|array',
            'stock.*'  => 'nullable|numeric|min:0',
        ], [
            'fecha.required' => 'Ingrese la fecha del conteo.',
            'stock.*.numeric' => 'Las cantidades deben ser números.',
            'stock.*.min'     => 'Las cantidades no pueden ser negativas.',
        ]);

        // Al menos un campo debe haber sido llenado
        $tieneValores = collect($request->stock)->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty();
        if (!$tieneValores) {
            return back()->with('error', 'Ingresa al menos una cantidad contada.');
        }

        DB::beginTransaction();
        try {
            $conteo = ConteoInventario::create([
                'fecha'       => $request->fecha,
                'observacion' => $request->observacion,
            ]);

            foreach ($request->stock as $idInsumo => $stockNuevo) {
                if ($stockNuevo === null || $stockNuevo === '') {
                    continue; // Omitir insumos no contados
                }

                $insumo = Insumo::find($idInsumo);
                if (!$insumo) continue;

                $stockAnterior = $insumo->stock;
                $diferencia    = (float) $stockNuevo - $stockAnterior;

                DetalleConteo::create([
                    'id_conteo'     => $conteo->id_conteo,
                    'id_insumo'     => $idInsumo,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'   => $stockNuevo,
                    'diferencia'    => $diferencia,
                ]);

                $insumo->stock = (float) $stockNuevo;
                $insumo->save();
            }

            DB::commit();
            return redirect()->route('conteos.show', $conteo->id_conteo)
                ->with('success', 'Conteo registrado. El inventario fue actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar el conteo: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $conteo = ConteoInventario::with('detalles.insumo')->findOrFail($id);
        return view('conteos.show', compact('conteo'));
    }
}
