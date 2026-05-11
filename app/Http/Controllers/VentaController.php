<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde    = $request->fecha_desde;
        $fechaHasta    = $request->fecha_hasta;
        $nombreProducto = $request->producto;

        $query = Venta::with('detalles');

        if ($fechaDesde) {
            $query->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $query->whereDate('fecha', '<=', $fechaHasta);
        }
        if ($nombreProducto) {
            $query->whereHas('detalles', function ($q) use ($nombreProducto) {
                $q->where('nombre_producto', 'like', '%' . $nombreProducto . '%');
            });
        }

        $ventas = $query->orderByDesc('fecha')->get();

        return view('ventas.index', compact('ventas', 'fechaDesde', 'fechaHasta', 'nombreProducto'));
    }

    public function create()
    {
        $productos = Producto::where('activo', 1)->get();
        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productos'                => 'required|array|min:1',
            'productos.*.id_producto'  => 'required|exists:productos,id_producto',
            'productos.*.cantidad'     => 'required|integer|min:1',
        ], [
            'productos.required'              => 'Debe agregar al menos un producto.',
            'productos.*.id_producto.required' => 'Seleccione un producto.',
            'productos.*.cantidad.min'         => 'La cantidad debe ser al menos 1.',
        ]);

        DB::beginTransaction();

        try {
            $items = $request->productos;

            // Collect total stock needed per insumo across all products
            $stockRequerido = [];
            $productosData  = [];

            foreach ($items as $item) {
                $producto     = Producto::findOrFail($item['id_producto']);
                $cantidadVenta = (int) $item['cantidad'];

                if (!$producto->activo) {
                    DB::rollBack();
                    return redirect()->route('ventas.create')
                        ->with('error', "El producto '{$producto->nombre}' está inactivo.");
                }

                $receta = Receta::with('detalles.insumo')
                    ->where('id_producto', $producto->id_producto)
                    ->where('activo', 1)
                    ->first();

                if (!$receta) {
                    DB::rollBack();
                    return redirect()->route('ventas.create')
                        ->with('error', "El producto '{$producto->nombre}' no tiene receta activa.");
                }

                if ($receta->detalles->isEmpty()) {
                    DB::rollBack();
                    return redirect()->route('ventas.create')
                        ->with('error', "La receta de '{$producto->nombre}' no tiene insumos.");
                }

                foreach ($receta->detalles as $detalle) {
                    $insumo = $detalle->insumo;

                    if (!$insumo || !$insumo->activo) {
                        DB::rollBack();
                        return redirect()->route('ventas.create')
                            ->with('error', "La receta de '{$producto->nombre}' tiene insumos inactivos.");
                    }

                    $id = $insumo->id_insumo;
                    $stockRequerido[$id] = ($stockRequerido[$id] ?? 0) + ($detalle->cantidad * $cantidadVenta);
                }

                $productosData[] = [
                    'producto'      => $producto,
                    'cantidadVenta' => $cantidadVenta,
                    'subtotal'      => $producto->precio * $cantidadVenta,
                ];
            }

            // Validate stock for all insumos at once
            foreach ($stockRequerido as $id_insumo => $cantidadNecesaria) {
                $insumo = Insumo::find($id_insumo);
                if ($insumo->stock < $cantidadNecesaria) {
                    DB::rollBack();
                    return redirect()->route('ventas.create')
                        ->with('error', "Stock insuficiente para el insumo: {$insumo->nombre}");
                }
            }

            // Deduct stock
            foreach ($stockRequerido as $id_insumo => $cantidadNecesaria) {
                $insumo = Insumo::find($id_insumo);
                $insumo->stock -= $cantidadNecesaria;
                $insumo->save();
            }

            // Create sale
            $total = array_sum(array_column($productosData, 'subtotal'));

            $venta = Venta::create([
                'fecha' => now(),
                'total' => $total,
            ]);

            foreach ($productosData as $data) {
                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_producto'     => $data['producto']->id_producto,
                    'nombre_producto' => $data['producto']->nombre,
                    'cantidad'        => $data['cantidadVenta'],
                    'precio_unitario' => $data['producto']->precio,
                    'subtotal'        => $data['subtotal'],
                ]);
            }

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada correctamente y stock descontado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.create')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
