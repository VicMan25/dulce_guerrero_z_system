<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\DetalleReceta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsumoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $unidad = $request->unidad;

        $query = Insumo::query();

        if ($buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        }
        if ($unidad) {
            $query->where('unidad_de_medida', $unidad);
        }

        // Primero los de stock bajo, luego por nombre
        $insumos = $query->get()->sortBy([
            fn($a, $b) => ($a->stock <= $a->stock_minimo ? 0 : 1) <=> ($b->stock <= $b->stock_minimo ? 0 : 1),
            fn($a, $b) => strcmp($a->nombre, $b->nombre),
        ]);

        $totalBajoStock = $insumos->filter(fn($i) => $i->stock <= $i->stock_minimo)->count();

        return view('insumos.index', compact('insumos', 'totalBajoStock', 'buscar', 'unidad'));
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

    public function destroy($id)
    {
        $insumo = Insumo::findOrFail($id);

        $enRecetas = DetalleReceta::where('id_insumo', $id)->count();
        if ($enRecetas > 0) {
            $plural = $enRecetas === 1 ? 'receta' : 'recetas';
            return redirect()->route('insumos.index')
                ->with('error', "El insumo \"{$insumo->nombre}\" no puede eliminarse porque está asignado a {$enRecetas} {$plural}.");
        }

        try {
            $nombre = $insumo->nombre;
            $insumo->delete();
            return redirect()->route('insumos.index')
                ->with('success', "Insumo \"{$nombre}\" eliminado correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('insumos.index')
                ->with('error', "No se pudo eliminar el insumo porque tiene registros relacionados.");
        }
    }

    public function estadisticas(Request $request)
    {
        $fechaDesdeInput = $request->filled('fecha_desde') ? $request->fecha_desde : null;
        $fechaHastaInput = $request->filled('fecha_hasta') ? $request->fecha_hasta : null;
        $filtroPorDefecto = !$fechaDesdeInput && !$fechaHastaInput;

        if ($filtroPorDefecto) {
            $fechaDesde = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
            $fechaHasta = Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        } else {
            $fechaDesde = $fechaDesdeInput ?? Carbon::parse($fechaHastaInput)->subDays(6)->format('Y-m-d');
            $fechaHasta = $fechaHastaInput ?? Carbon::now()->format('Y-m-d');
            if ($fechaDesde > $fechaHasta) {
                [$fechaDesde, $fechaHasta] = [$fechaHasta, $fechaDesde];
            }
        }

        // Una sola receta activa por producto (igual que VentaController)
        $recetaActiva = DB::table('recetas')
            ->select('id_producto', DB::raw('MIN(id_receta) as id_receta'))
            ->where('activo', 1)
            ->groupBy('id_producto');

        $consumos = DB::table('ventas as v')
            ->join('detalle_ventas as dv', 'dv.id_venta', '=', 'v.id_venta')
            ->joinSub($recetaActiva, 'r', 'r.id_producto', '=', 'dv.id_producto')
            ->join('detalle_receta as dr', 'dr.id_receta', '=', 'r.id_receta')
            ->join('insumos as i', 'i.id_insumo', '=', 'dr.id_insumo')
            ->whereDate('v.fecha', '>=', $fechaDesde)
            ->whereDate('v.fecha', '<=', $fechaHasta)
            ->select(
                'i.id_insumo',
                'i.nombre',
                'i.unidad_de_medida',
                DB::raw('SUM(dv.cantidad * dr.cantidad) as total_consumido')
            )
            ->groupBy('i.id_insumo', 'i.nombre', 'i.unidad_de_medida')
            ->orderByDesc('total_consumido')
            ->get();

        $consumoPorDia = DB::table('ventas as v')
            ->join('detalle_ventas as dv', 'dv.id_venta', '=', 'v.id_venta')
            ->joinSub($recetaActiva, 'r', 'r.id_producto', '=', 'dv.id_producto')
            ->join('detalle_receta as dr', 'dr.id_receta', '=', 'r.id_receta')
            ->whereDate('v.fecha', '>=', $fechaDesde)
            ->whereDate('v.fecha', '<=', $fechaHasta)
            ->selectRaw('date(v.fecha) as dia, SUM(dv.cantidad * dr.cantidad) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $chartDesde  = Carbon::parse($fechaDesde);
        $chartHasta  = Carbon::parse($fechaHasta);
        $chartLabels = [];
        $chartConsumo = [];

        $current = $chartDesde->copy();
        while ($current->lte($chartHasta)) {
            $dateStr        = $current->format('Y-m-d');
            $chartLabels[]  = $current->format('d/m');
            $chartConsumo[] = (float) ($consumoPorDia[$dateStr] ?? 0);
            $current->addDay();
        }

        $consumidos    = $consumos->filter(fn($c) => $c->total_consumido > 0);
        $masConsumido  = $consumidos->first();
        $menosConsumido = $consumidos->last();
        $totalTipos    = $consumidos->count();

        return view('insumos.estadisticas', compact(
            'consumos', 'fechaDesde', 'fechaHasta', 'filtroPorDefecto',
            'masConsumido', 'menosConsumido', 'totalTipos',
            'chartLabels', 'chartConsumo'
        ));
    }
}
