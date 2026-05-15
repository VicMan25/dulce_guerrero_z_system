@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">← Historial</a>
    </div>

    <h1>Venta #{{ $venta->id_venta }}</h1>

    {{-- Resumen de la venta --}}
    <div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); margin-bottom:28px;">

        <div class="stat-card stat-neutral">
            <div class="stat-label">Fecha</div>
            <div class="stat-value" style="font-size:1.1rem;">
                {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
            </div>
            <div class="muted" style="font-size:0.82rem; margin-top:4px;">
                {{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }} hrs
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-label">Total de la venta</div>
            <div class="stat-value">$ {{ number_format($venta->total, 0, ',', '.') }}</div>
        </div>

        <div class="stat-card stat-neutral">
            <div class="stat-label">Tipos de producto</div>
            <div class="stat-value">{{ $venta->detalles->count() }}</div>
        </div>

        <div class="stat-card stat-neutral">
            <div class="stat-label">Unidades totales</div>
            <div class="stat-value">{{ $venta->detalles->sum('cantidad') }}</div>
        </div>

    </div>

    {{-- Tabla de productos --}}
    <h2 style="margin-bottom:14px;">Productos vendidos</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align:center;">Cantidad</th>
                    <th style="text-align:right;" class="col-hide-mobile">Precio unitario</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                    <tr>
                        <td style="font-weight:600;">{{ $detalle->nombre_producto }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-neutral">× {{ $detalle->cantidad }}</span>
                        </td>
                        <td style="text-align:right;" class="col-hide-mobile muted">
                            $ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right; font-weight:700; color:#3a6642; font-size:1.0rem;">
                            $ {{ number_format($detalle->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div style="text-align:right; margin-top:16px; padding:14px 20px;
                background:#f0f5f0; border-radius:10px; border:1px solid #ccd5ae;">
        <span class="muted" style="font-size:0.9rem; margin-right:12px;">Total de la venta:</span>
        <strong style="font-size:1.25rem; color:#3a6642;">
            $ {{ number_format($venta->total, 0, ',', '.') }}
        </strong>
    </div>
@endsection
