<?php

namespace App\Http\Controllers;

use App\Models\IngresoManual;
use Illuminate\Http\Request;

class IngresoManualController extends Controller
{
    public function create()
    {
        return view('ingresos.create');
    }

    public function store(Request $request)
    {
        $monto = str_replace('.', '', $request->monto ?? '');
        $request->merge(['monto' => $monto]);

        $request->validate([
            'monto'     => 'required|numeric|min:0.01',
            'fecha'     => 'required|date',
            'categoria' => 'required|string|max:100',
        ], [
            'monto.required'     => 'Ingrese el monto.',
            'monto.min'          => 'El monto debe ser mayor a cero.',
            'fecha.required'     => 'Ingrese la fecha.',
            'categoria.required' => 'Seleccione una categoría.',
        ]);

        IngresoManual::create([
            'monto'       => $request->monto,
            'descripcion' => $request->descripcion,
            'fecha'       => $request->fecha,
            'categoria'   => $request->categoria,
        ]);

        return redirect()->route('gastos.index')
            ->with('success', 'Ingreso registrado correctamente.');
    }
}
