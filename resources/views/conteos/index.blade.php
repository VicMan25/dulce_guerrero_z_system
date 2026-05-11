@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">← Inventario</a>
    </div>
    <h1>Historial de conteos</h1>

    <div class="top-actions">
        <a href="{{ route('conteos.create') }}" class="btn btn-success">📋 Hacer conteo</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Observación</th>
                    <th style="text-align:center;">Ítems revisados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conteos as $conteo)
                    <tr>
                        <td style="font-weight:600;">
                            {{ \Carbon\Carbon::parse($conteo->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="muted">{{ $conteo->observacion ?? '—' }}</td>
                        <td style="text-align:center;">{{ $conteo->detalles_count }}</td>
                        <td>
                            <a href="{{ route('conteos.show', $conteo->id_conteo) }}"
                               class="btn btn-secondary" style="padding:6px 12px; font-size:0.85rem;">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">
                            Aún no hay conteos registrados.
                            <a href="{{ route('conteos.create') }}">Hacer el primero</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
