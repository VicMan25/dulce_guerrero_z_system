@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Inventario de insumos</h1>

    {{-- Alerta de stock bajo --}}
    @if($totalBajoStock > 0)
        <div style="background:#f8d7da; border:1px solid #f1aeb5; border-radius:10px;
                    padding:12px 16px; margin-bottom:20px; color:#9b2335; font-weight:600;">
            ⚠️ {{ $totalBajoStock }} {{ $totalBajoStock === 1 ? 'insumo tiene' : 'insumos tienen' }}
            stock igual o por debajo del mínimo.
        </div>
    @endif

    {{-- Buscador por nombre y unidad --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('insumos.index') }}">
            <div class="filter-grid-2">
                <div class="form-group">
                    <label>Buscar por nombre</label>
                    <input type="text" name="buscar" value="{{ $buscar ?? '' }}" placeholder="Ej: Azúcar">
                </div>
                <div class="form-group">
                    <label>Unidad de medida</label>
                    <select name="unidad">
                        <option value="">Todas las unidades</option>
                        @foreach(['unidades', 'paquetes', 'gramos', 'kilogramos', 'mililitros', 'litros', 'porciones'] as $u)
                            <option value="{{ $u }}" {{ ($unidad ?? '') === $u ? 'selected' : '' }}>
                                {{ ucfirst($u) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="top-actions" style="margin-top:12px;">
                <button type="submit" class="btn">Buscar</button>
                <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="top-actions">
        <a href="{{ route('conteos.create') }}" class="btn btn-success">📋 Hacer conteo</a>
        <a href="{{ route('conteos.index') }}" class="btn btn-secondary">Historial de conteos</a>
        @if(auth()->user()->esAdmin())
            <a href="{{ route('entradas.create') }}" class="btn">+ Registrar entrada</a>
            <a href="{{ route('insumos.create') }}" class="btn btn-secondary">+ Nuevo insumo</a>
        @endif
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Unidad</th>
                    <th style="text-align:center;">Cantidad actual</th>
                    <th style="text-align:center;" class="col-hide-mobile">Mínimo</th>
                    <th style="text-align:center;">Estado</th>
                    <th class="col-hide-mobile">Activo</th>
                    @if(auth()->user()->esAdmin())
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($insumos as $insumo)
                    @php $bajo = $insumo->stock <= $insumo->stock_minimo; @endphp
                    <tr style="{{ $bajo ? 'background-color:#fff0f1;' : '' }}">
                        <td style="font-weight:{{ $bajo ? '700' : '400' }};">
                            {{ $insumo->nombre }}
                        </td>
                        <td class="muted">{{ $insumo->unidad_de_medida }}</td>
                        <td style="text-align:center; font-size:1.05rem; font-weight:600;
                                   color:{{ $bajo ? '#9b2335' : '#3a6642' }};">
                            {{ number_format($insumo->stock, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;" class="muted col-hide-mobile">
                            {{ number_format($insumo->stock_minimo, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            @if($bajo)
                                <span class="badge badge-danger">Stock bajo</span>
                            @else
                                <span class="badge badge-success">Normal</span>
                            @endif
                        </td>
                        <td class="col-hide-mobile">
                            @if($insumo->activo)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-neutral">No</span>
                            @endif
                        </td>
                        @if(auth()->user()->esAdmin())
                            <td>
                                <a href="{{ route('insumos.edit', $insumo->id_insumo) }}"
                                   class="btn btn-secondary" style="padding:6px 12px; font-size:0.85rem;">
                                    Editar
                                </a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->esAdmin() ? 7 : 6 }}" class="muted">
                            No hay insumos registrados.
                            @if(auth()->user()->esAdmin())
                                <a href="{{ route('insumos.create') }}">Crear el primero</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
