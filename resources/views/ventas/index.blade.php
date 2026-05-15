@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Historial de ventas</h1>

    @php
        $hoy         = \Carbon\Carbon::today()->format('Y-m-d');
        $semanaDesde = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
        $semanaHasta = \Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
    @endphp

    {{-- Filtros --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('ventas.index') }}">
            <div class="filter-grid-3">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="fecha_desde" value="{{ $fechaDesde }}">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}">
                </div>
                <div class="form-group">
                    <label>Producto</label>
                    <input type="text" name="producto" value="{{ $nombreProducto }}"
                           placeholder="Nombre del producto...">
                </div>
            </div>
            <div class="top-actions" style="margin-top:14px;">
                <button type="submit" class="btn">Filtrar</button>
                <a href="{{ route('ventas.index', ['fecha_desde' => $hoy, 'fecha_hasta' => $hoy]) }}"
                   class="btn btn-secondary">Hoy</a>
                <a href="{{ route('ventas.index', ['fecha_desde' => $semanaDesde, 'fecha_hasta' => $semanaHasta]) }}"
                   class="btn btn-secondary">Esta semana</a>
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Limpiar</a>
                <a href="{{ route('ventas.create') }}" class="btn btn-success">+ Registrar venta</a>
            </div>
        </form>
    </div>

    @php $totalGeneral = 0; @endphp

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-hide-mobile">ID</th>
                    <th>Fecha y hora</th>
                    <th style="text-align:center;" class="col-hide-mobile">Productos</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:center;">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    @php $totalGeneral += $venta->total; @endphp
                    <tr>
                        <td class="col-hide-mobile muted">#{{ $venta->id_venta }}</td>
                        <td style="font-size:0.9rem; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                            <span class="muted" style="font-size:0.82rem;">
                                {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}
                            </span>
                        </td>
                        <td style="text-align:center;" class="col-hide-mobile">
                            <span class="badge badge-neutral">
                                {{ $venta->detalles->count() }}
                                {{ $venta->detalles->count() === 1 ? 'producto' : 'productos' }}
                            </span>
                        </td>
                        <td style="text-align:right; font-weight:700; color:#3a6642; font-size:1.0rem;">
                            $ {{ number_format($venta->total, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ route('ventas.show', $venta->id_venta) }}"
                               class="btn btn-secondary" style="padding:6px 14px; font-size:0.85rem;">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted" style="text-align:center; padding:28px 0;">
                            No hay ventas registradas en este período.
                            <br>
                            <a href="{{ route('ventas.create') }}" style="color:var(--color-5);">
                                Registrar primera venta →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventas->isNotEmpty())
        <div style="text-align:right; margin-top:16px; font-size:1.05rem;">
            <strong>Total del período: $ {{ number_format($totalGeneral, 0, ',', '.') }}</strong>
            <span class="muted" style="font-size:0.88rem; margin-left:10px;">
                ({{ $ventas->count() }} {{ $ventas->count() === 1 ? 'venta' : 'ventas' }})
            </span>
        </div>
    @endif
@endsection
