<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\DetalleReceta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index()
    {
        $recetas = Receta::with(['producto', 'detalles.insumo'])->get();
        return view('recetas.index', compact('recetas'));
    }

    public function create()
    {
        $insumos = Insumo::where('activo', 1)->get();
        return view('recetas.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $precio = str_replace('.', '', $request->precio);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required',
            'activo' => 'required|boolean',
            'insumos' => 'required|array|min:1',
            'insumos.*' => 'exists:insumos,id_insumo',
        ]);

        DB::beginTransaction();

        try {
            $producto = Producto::create([
                'nombre' => $request->nombre,
                'precio' => $precio,
                'activo' => $request->activo,
            ]);

            $receta = Receta::create([
                'nombre' => $request->nombre,
                'id_producto' => $producto->id_producto,
                'activo' => $request->activo,
            ]);

            foreach ($request->insumos as $idInsumo) {
                $cantidad = $request->cantidades[$idInsumo] ?? 0;

                if ($cantidad <= 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Cada insumo seleccionado debe tener una cantidad mayor a 0.');
                }

                DetalleReceta::create([
                    'id_receta' => $receta->id_receta,
                    'id_insumo' => $idInsumo,
                    'cantidad' => $cantidad,
                ]);
            }

            DB::commit();

            return redirect()->route('recetas.index')
                ->with('success', 'Receta creada correctamente y producto generado automáticamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $receta = Receta::with(['producto', 'detalles'])->findOrFail($id);
        $insumos = Insumo::where('activo', 1)->get();

        $insumosSeleccionados = $receta->detalles->pluck('id_insumo')->toArray();
        $cantidades = $receta->detalles->pluck('cantidad', 'id_insumo')->toArray();

        return view('recetas.edit', compact(
            'receta',
            'insumos',
            'insumosSeleccionados',
            'cantidades'
        ));
    }

    public function update(Request $request, $id)
    {
        $precio = str_replace('.', '', $request->precio);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required',
            'activo' => 'required|boolean',
            'insumos' => 'required|array|min:1',
            'insumos.*' => 'exists:insumos,id_insumo',
        ]);

        DB::beginTransaction();

        try {
            $receta = Receta::with('producto')->findOrFail($id);

            $receta->update([
                'nombre' => $request->nombre,
                'activo' => $request->activo,
            ]);

            $receta->producto->update([
                'nombre' => $request->nombre,
                'precio' => $precio,
                'activo' => $request->activo,
            ]);

            DetalleReceta::where('id_receta', $receta->id_receta)->delete();

            foreach ($request->insumos as $idInsumo) {
                $cantidad = $request->cantidades[$idInsumo] ?? 0;

                if ($cantidad <= 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Cada insumo seleccionado debe tener una cantidad mayor a 0.');
                }

                DetalleReceta::create([
                    'id_receta' => $receta->id_receta,
                    'id_insumo' => $idInsumo,
                    'cantidad' => $cantidad,
                ]);
            }

            DB::commit();

            return redirect()->route('recetas.index')
                ->with('success', 'Receta actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $receta = Receta::with('producto')->findOrFail($id);
            $producto = $receta->producto;

            $tieneVentas = false;

            if ($producto) {
                $tieneVentas = DetalleVenta::where('id_producto', $producto->id_producto)->exists();
            }

            if ($tieneVentas) {
                $receta->activo = 0;
                $receta->save();

                if ($producto) {
                    $producto->activo = 0;
                    $producto->save();
                }

                DB::commit();

                return redirect()->route('recetas.index')
                    ->with('error', 'La receta no se eliminó porque el producto ya tiene ventas. Se desactivó junto con el producto.');
            }

            DetalleReceta::where('id_receta', $receta->id_receta)->delete();
            $receta->delete();

            if ($producto) {
                $producto->delete();
            }

            DB::commit();

            return redirect()->route('recetas.index')
                ->with('success', 'Receta eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('recetas.index')
                ->with('error', 'No se pudo eliminar la receta: ' . $e->getMessage());
        }
    }
}