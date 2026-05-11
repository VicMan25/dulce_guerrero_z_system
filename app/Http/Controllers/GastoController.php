<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\IngresoManual;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;

        $gastosQuery   = Gasto::query();
        $ventasQuery   = Venta::query();
        $ingresosQuery = IngresoManual::query();

        if ($fechaDesde) {
            $gastosQuery->whereDate('fecha', '>=', $fechaDesde);
            $ventasQuery->whereDate('fecha', '>=', $fechaDesde);
            $ingresosQuery->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $gastosQuery->whereDate('fecha', '<=', $fechaHasta);
            $ventasQuery->whereDate('fecha', '<=', $fechaHasta);
            $ingresosQuery->whereDate('fecha', '<=', $fechaHasta);
        }

        $totalGastos   = (float) $gastosQuery->sum('monto');
        $totalVentas   = (float) $ventasQuery->sum('total');
        $totalManuales = (float) $ingresosQuery->sum('monto');
        $totalIngresos = $totalVentas + $totalManuales;
        $balance       = $totalIngresos - $totalGastos;

        $gastos           = $gastosQuery->orderByDesc('fecha')->get();
        $ingresosManuales = $ingresosQuery->orderByDesc('fecha')->get();

        // Rango para la gráfica: el período filtrado o los últimos 30 días
        $chartDesde = $fechaDesde ? Carbon::parse($fechaDesde) : Carbon::now()->subDays(29);
        $chartHasta = $fechaHasta ? Carbon::parse($fechaHasta) : Carbon::now();

        $ventasPorDia = Venta::whereBetween(DB::raw('date(fecha)'), [
            $chartDesde->format('Y-m-d'),
            $chartHasta->format('Y-m-d'),
        ])->selectRaw('date(fecha) as dia, SUM(total) as total')
          ->groupBy('dia')
          ->pluck('total', 'dia');

        $manualesPorDia = IngresoManual::whereBetween('fecha', [
            $chartDesde->format('Y-m-d'),
            $chartHasta->format('Y-m-d'),
        ])->selectRaw('fecha as dia, SUM(monto) as total')
          ->groupBy('dia')
          ->pluck('total', 'dia');

        $gastosPorDia = Gasto::whereBetween('fecha', [
            $chartDesde->format('Y-m-d'),
            $chartHasta->format('Y-m-d'),
        ])->selectRaw('fecha as dia, SUM(monto) as total')
          ->groupBy('dia')
          ->pluck('total', 'dia');

        $chartLabels   = [];
        $chartVentas   = [];
        $chartManuales = [];
        $chartGastos   = [];

        $current = $chartDesde->copy();
        while ($current->lte($chartHasta)) {
            $dateStr         = $current->format('Y-m-d');
            $chartLabels[]   = $current->format('d/m');
            $chartVentas[]   = (float) ($ventasPorDia[$dateStr]   ?? 0);
            $chartManuales[] = (float) ($manualesPorDia[$dateStr] ?? 0);
            $chartGastos[]   = (float) ($gastosPorDia[$dateStr]   ?? 0);
            $current->addDay();
        }

        return view('gastos.index', compact(
            'gastos', 'ingresosManuales',
            'totalGastos', 'totalIngresos', 'totalVentas', 'totalManuales', 'balance',
            'fechaDesde', 'fechaHasta',
            'chartLabels', 'chartVentas', 'chartManuales', 'chartGastos'
        ));
    }

    public function create()
    {
        return view('gastos.create');
    }

    public function store(Request $request)
    {
        $monto = str_replace('.', '', $request->monto ?? '');
        $request->merge(['monto' => $monto]);

        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0.01',
            'fecha'       => 'required|date',
            'categoria'   => 'required|string|max:100',
        ], [
            'descripcion.required' => 'Ingrese una descripción.',
            'monto.required'       => 'Ingrese el monto.',
            'monto.min'            => 'El monto debe ser mayor a cero.',
            'fecha.required'       => 'Ingrese la fecha.',
            'categoria.required'   => 'Seleccione una categoría.',
        ]);

        Gasto::create([
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'fecha'       => $request->fecha,
            'categoria'   => $request->categoria,
        ]);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }
}
