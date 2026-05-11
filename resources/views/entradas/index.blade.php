@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Entradas de inventario</h1>

    <div class="top-actions">
        <a href="{{ route('entradas.create') }}" class="btn">Registrar entrada</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Insumo</th>
                    <th>Unidad</th>
                    <th>Cantidad agregada</th>
                    <th>Fecha</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entradas as $entrada)
                    <tr>
                        <td>{{ $entrada->id_entrada }}</td>
                        <td>{{ $entrada->insumo->nombre ?? '—' }}</td>
                        <td>{{ $entrada->insumo->unidad_de_medida ?? '—' }}</td>
                        <td>{{ number_format($entrada->cantidad, 0, ',', '.') }}</td>
                        <td>{{ $entrada->fecha }}</td>
                        <td>{{ $entrada->observacion ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay entradas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
