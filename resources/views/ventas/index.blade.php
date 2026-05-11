@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Historial de ventas</h1>

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
            <div class="top-actions" style="margin-top: 14px;">
                <button type="submit" class="btn">Filtrar</button>
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
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    @foreach ($venta->detalles as $detalle)
                        @php $totalGeneral += $detalle->subtotal; @endphp
                        <tr>
                            <td>{{ $venta->id_venta }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                            <td>{{ $detalle->nombre_producto }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>$ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                            <td>$ {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6">No hay ventas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="margin-top: 22px;">
        Total vendido: $ {{ number_format($totalGeneral, 0, ',', '.') }}
    </h3>
@endsection
