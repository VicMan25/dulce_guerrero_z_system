<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Insumo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today()->format('Y-m-d');

        $ventasHoy         = Venta::whereDate('fecha', $hoy)->get();
        $totalHoy          = (float) $ventasHoy->sum('total');
        $cantidadVentasHoy = $ventasHoy->count();

        $inicioSemana = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $finSemana    = Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        $totalSemana  = (float) Venta::whereDate('fecha', '>=', $inicioSemana)
                            ->whereDate('fecha', '<=', $finSemana)
                            ->sum('total');

        $insumosBajoStock = Insumo::whereRaw('stock <= stock_minimo')->count();

        return view('dashboard', compact(
            'totalHoy', 'cantidadVentasHoy',
            'totalSemana', 'insumosBajoStock'
        ));
    }
}
