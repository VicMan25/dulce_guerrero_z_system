<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insumos de la receta</title>
</head>
<body>
    <h1>Insumos de la receta</h1>

    <p><strong>Receta:</strong> {{ $receta->nombre }}</p>
    <p><strong>Producto:</strong> {{ $receta->producto->nombre ?? 'Sin producto' }}</p>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Agregar insumo</h2>

    <form action="{{ route('recetas.insumos.store', $receta->id_receta) }}" method="POST">
        @csrf

        <label>Insumo:</label>
        <select name="id_insumo">
            <option value="">Seleccione un insumo</option>
            @foreach($insumos as $insumo)
                <option value="{{ $insumo->id_insumo }}">
                    {{ $insumo->nombre }}
                </option>
            @endforeach
        </select>
        <br><br>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" min="1" step="1">
        <br><br>

        <button type="submit">Agregar</button>
    </form>

    <hr>

    <h2>Insumos asociados</h2>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Insumo</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->id_detalle }}</td>
                    <td>{{ $detalle->insumo->nombre ?? 'Sin insumo' }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>
                        <form action="{{ route('detalle_receta.destroy', $detalle->id_detalle) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay insumos asociados a esta receta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <a href="{{ route('recetas.index') }}">Volver a recetas</a>
</body>
</html>