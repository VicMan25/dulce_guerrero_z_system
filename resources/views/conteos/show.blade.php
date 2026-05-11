@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('conteos.index') }}" class="btn btn-secondary">← Historial</a>
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Ver inventario</a>
    </div>

    <h1>Conteo del {{ \Carbon\Carbon::parse($conteo->fecha)->format('d/m/Y') }}</h1>

    @if($conteo->observacion)
        <p class="muted" style="margin-top:-8px; margin-bottom:20px;">
            {{ $conteo->observacion }}
        </p>
    @endif

    {{-- Resumen --}}
    @php
        $consumidos = $conteo->detalles->filter(fn($d) => $d->diferencia < 0)->count();
        $agregados  = $conteo->detalles->filter(fn($d) => $d->diferencia > 0)->count();
        $sinCambio  = $conteo->detalles->filter(fn($d) => $d->diferencia == 0)->count();
        $bajosPost  = $conteo->detalles->filter(fn($d) => $d->insumo && $d->stock_nuevo <= $d->insumo->stock_minimo)->count();
    @endphp

    <div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); margin-bottom:24px;">
        <div class="stat-card stat-neutral">
            <div class="stat-label">Ítems revisados</div>
            <div class="stat-value">{{ $conteo->detalles->count() }}</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-label">Consumidos / bajaron</div>
            <div class="stat-value">{{ $consumidos }}</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-label">Subieron / ganaron</div>
            <div class="stat-value">{{ $agregados }}</div>
        </div>
        @if($bajosPost > 0)
            <div class="stat-card stat-danger">
                <div class="stat-label">Con stock bajo tras conteo</div>
                <div class="stat-value">{{ $bajosPost }}</div>
            </div>
        @endif
    </div>

    {{-- Tabla detalle --}}
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Unidad</th>
                    <th style="text-align:center;">Stock anterior</th>
                    <th style="text-align:center;">Contado</th>
                    <th style="text-align:center;">Diferencia</th>
                    <th style="text-align:center;">Estado post-conteo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conteo->detalles->sortBy(fn($d) => $d->insumo?->nombre) as $detalle)
                    @php
                        $diff     = $detalle->diferencia;
                        $bajoDes  = $detalle->insumo && $detalle->stock_nuevo <= $detalle->insumo->stock_minimo;
                    @endphp
                    <tr style="{{ $bajoDes ? 'background-color:#fff0f1;' : '' }}">
                        <td style="font-weight:{{ $bajoDes ? '700' : '400' }};">
                            {{ $detalle->insumo->nombre ?? '—' }}
                        </td>
                        <td class="muted">{{ $detalle->insumo->unidad_de_medida ?? '—' }}</td>
                        <td style="text-align:center; color:#7a7a7a;">
                            {{ number_format($detalle->stock_anterior, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center; font-weight:600;">
                            {{ number_format($detalle->stock_nuevo, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center; font-weight:700;
                                   color:{{ $diff < 0 ? '#9b2335' : ($diff > 0 ? '#3a6642' : '#7a7a7a') }};">
                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            @if($bajoDes)
                                <span class="badge badge-danger">Stock bajo</span>
                            @else
                                <span class="badge badge-success">Normal</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
