<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function index()
    {
        // Primero los de stock bajo, luego por nombre
        $insumos = Insumo::all()->sortBy([
            fn($a, $b) => ($a->stock <= $a->stock_minimo ? 0 : 1) <=> ($b->stock <= $b->stock_minimo ? 0 : 1),
            fn($a, $b) => strcmp($a->nombre, $b->nombre),
        ]);

        $totalBajoStock = $insumos->filter(fn($i) => $i->stock <= $i->stock_minimo)->count();

        return view('insumos.index', compact('insumos', 'totalBajoStock'));
    }

    public function create()
    {
        return view('insumos.create');
    }

    public function store(Request $request)
    {
        $stock        = str_replace('.', '', $request->stock ?? '0');
        $stock_minimo = str_replace('.', '', $request->stock_minimo ?? '0');
        $request->merge(['stock' => $stock, 'stock_minimo' => $stock_minimo]);

        $request->validate([
            'nombre'           => 'required|string|max:255',
            'unidad_de_medida' => 'required|string|max:50',
            'stock'            => 'required|numeric|min:0',
            'stock_minimo'     => 'required|numeric|min:0',
        ], [
            'nombre.required'           => 'Ingrese el nombre del insumo.',
            'unidad_de_medida.required' => 'Seleccione la unidad de medida.',
            'stock.required'            => 'Ingrese el stock inicial.',
            'stock_minimo.required'     => 'Ingrese el stock mínimo.',
        ]);

        Insumo::create([
            'nombre'           => $request->nombre,
            'unidad_de_medida' => $request->unidad_de_medida,
            'stock'            => $request->stock,
            'stock_minimo'     => $request->stock_minimo,
            'activo'           => true,
        ]);

        return redirect()->route('insumos.index')
            ->with('success', 'Insumo creado correctamente.');
    }

    public function edit($id)
    {
        $insumo = Insumo::findOrFail($id);
        return view('insumos.edit', compact('insumo'));
    }

    public function update(Request $request, $id)
    {
        $stock_minimo = str_replace('.', '', $request->stock_minimo ?? '');
        $request->merge(['stock_minimo' => $stock_minimo]);

        $request->validate([
            'nombre'           => 'required|string|max:255',
            'unidad_de_medida' => 'required|string|max:50',
            'stock_minimo'     => 'required|numeric|min:0',
            'activo'           => 'required|boolean',
        ], [
            'nombre.required'           => 'Ingrese el nombre.',
            'unidad_de_medida.required' => 'Seleccione la unidad.',
            'stock_minimo.required'     => 'Ingrese el stock mínimo.',
        ]);

        $insumo = Insumo::findOrFail($id);
        $insumo->update([
            'nombre'           => $request->nombre,
            'unidad_de_medida' => $request->unidad_de_medida,
            'stock_minimo'     => $request->stock_minimo,
            'activo'           => $request->activo,
        ]);

        return redirect()->route('insumos.index')
            ->with('success', 'Insumo actualizado correctamente.');
    }
}
