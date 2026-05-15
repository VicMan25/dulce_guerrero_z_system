@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('recetas.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Editar receta</h1>

    <form action="{{ route('recetas.update', $receta->id_receta) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid-two">
            <div class="form-group">
                <label>Nombre de la receta / producto</label>
                <input type="text" name="nombre" value="{{ old('nombre', $receta->nombre) }}">
            </div>
            <div class="form-group">
                <label>Precio de venta</label>
                <input type="text" name="precio" id="precio"
                    value="{{ old('precio', number_format($receta->producto->precio, 0, ',', '.')) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Activo</label>
            <select name="activo">
                <option value="1" {{ old('activo', $receta->producto->activo) == 1 ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('activo', $receta->producto->activo) == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <h3>Selecciona los insumos de la receta</h3>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">Usar</th>
                        <th>Insumo</th>
                        <th class="col-hide-mobile">Unidad</th>
                        <th class="col-hide-mobile">Stock actual</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insumos as $insumo)
                        <tr>
                            <td class="checkbox-cell" style="text-align:center;">
                                <input type="checkbox" name="insumos[]" value="{{ $insumo->id_insumo }}"
                                       style="width:20px; height:20px; cursor:pointer;"
                                       {{
                                           is_array(old('insumos'))
                                               ? (in_array($insumo->id_insumo, old('insumos')) ? 'checked' : '')
                                               : (in_array($insumo->id_insumo, $insumosSeleccionados) ? 'checked' : '')
                                       }}>
                            </td>
                            <td>{{ $insumo->nombre }}</td>
                            <td class="col-hide-mobile muted">{{ $insumo->unidad_de_medida }}</td>
                            <td class="col-hide-mobile muted">{{ number_format($insumo->stock, 0, ',', '.') }}</td>
                            <td>
                                <input type="number" class="small-input"
                                    name="cantidades[{{ $insumo->id_insumo }}]"
                                    min="0.01" step="0.01"
                                    value="{{ old('cantidades.' . $insumo->id_insumo, $cantidades[$insumo->id_insumo] ?? '') }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="top-actions" style="margin-top: 20px;">
            <button type="submit" class="btn btn-success">Actualizar receta</button>
            <a href="{{ route('recetas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

    <script>
        document.getElementById('precio').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            e.target.value = new Intl.NumberFormat('es-CO').format(v);
        });
    </script>
@endsection
