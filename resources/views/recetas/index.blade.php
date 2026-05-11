@extends('layouts.app')

@section('content')

    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            ← Dashboard
        </a>
    </div>
    <h1>Lista de recetas</h1>

    <div class="top-actions">
        <a href="{{ route('recetas.create') }}" class="btn">Crear receta</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID Receta</th>
                    <th>Nombre</th>
                    <th>Producto generado</th>
                    <th>Precio</th>
                    <th>Activa</th>
                    <th>Insumos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recetas as $receta)
                    <tr>
                        <td>{{ $receta->id_receta }}</td>
                        <td>{{ $receta->nombre }}</td>
                        <td>{{ $receta->producto->nombre ?? 'Sin producto' }}</td>
                        <td>$ {{ number_format($receta->producto->precio ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if ($receta->activo)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </td>
                        <td>
                            @forelse($receta->detalles as $detalle)
                                {{ $detalle->insumo->nombre ?? 'Sin insumo' }}
                                ({{ number_format($detalle->cantidad, 2, ',', '.') }})
                                <br>
                            @empty
                                <span class="badge badge-neutral">Sin insumos</span>
                            @endforelse
                        </td>
                        <td>
                            <a href="{{ route('recetas.edit', $receta->id_receta) }}" class="btn btn-secondary">Editar</a>

                            <form action="{{ route('recetas.destroy', $receta->id_receta) }}" method="POST"
                                class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar / Desactivar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No hay recetas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
