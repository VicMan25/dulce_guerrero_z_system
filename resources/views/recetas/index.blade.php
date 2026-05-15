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
                    <th class="col-hide-mobile">ID</th>
                    <th>Nombre</th>
                    <th class="col-hide-mobile">Producto generado</th>
                    <th>Precio</th>
                    <th>Activa</th>
                    <th class="col-hide-mobile">Insumos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recetas as $receta)
                    <tr>
                        <td class="col-hide-mobile">{{ $receta->id_receta }}</td>
                        <td style="font-weight:600;">{{ $receta->nombre }}</td>
                        <td class="col-hide-mobile">{{ $receta->producto->nombre ?? 'Sin producto' }}</td>
                        <td>$ {{ number_format($receta->producto->precio ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if ($receta->activo)
                                <span class="badge badge-success">Sí</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </td>
                        <td class="col-hide-mobile">
                            @forelse($receta->detalles as $detalle)
                                {{ $detalle->insumo->nombre ?? 'Sin insumo' }}
                                ({{ number_format($detalle->cantidad, 2, ',', '.') }})
                                <br>
                            @empty
                                <span class="badge badge-neutral">Sin insumos</span>
                            @endforelse
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('recetas.edit', $receta->id_receta) }}"
                               class="btn btn-secondary" style="padding:8px 12px; font-size:0.85rem;">
                                Editar
                            </a>
                            <form action="{{ route('recetas.destroy', $receta->id_receta) }}" method="POST"
                                  style="display:inline; margin-left:4px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        style="padding:8px 12px; font-size:0.85rem;"
                                        onclick="return confirm('¿Eliminar la receta «{{ $receta->nombre }}»? Si tiene ventas asociadas solo se desactivará, de lo contrario se eliminará permanentemente.')">
                                    Eliminar
                                </button>
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
