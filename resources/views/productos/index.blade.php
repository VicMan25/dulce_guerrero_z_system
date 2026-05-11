@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            ← Dashboard
        </a>
    </div>
    <h1>Lista de productos</h1>

    <p class="muted">Los productos se generan automáticamente al crear una receta.</p>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td>{{ $producto->id_producto }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>$ {{ number_format($producto->precio, 0, ',', '.') }}</td>
                        <td>
                            @if ($producto->activo)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
