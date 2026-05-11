@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Editar insumo</h1>

    <div class="info-banner">
        Stock actual: <strong>{{ number_format($insumo->stock, 0, ',', '.') }} {{ $insumo->unidad_de_medida }}</strong>
        — Para agregar stock usa
        <a href="{{ route('entradas.create') }}">Registrar entrada de inventario</a>.
    </div>

    <form action="{{ route('insumos.update', $insumo->id_insumo) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid-two">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $insumo->nombre) }}">
            </div>
            <div class="form-group">
                <label>Unidad de medida</label>
                <select name="unidad_de_medida">
                    @foreach(['gramos', 'mililitros', 'unidades', 'kilogramos', 'litros', 'porciones'] as $u)
                        <option value="{{ $u }}"
                            {{ old('unidad_de_medida', $insumo->unidad_de_medida) === $u ? 'selected' : '' }}>
                            {{ ucfirst($u) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Stock mínimo</label>
                <input type="text" name="stock_minimo" id="stock_minimo"
                       value="{{ old('stock_minimo', number_format($insumo->stock_minimo, 0, ',', '.')) }}">
            </div>
            <div class="form-group">
                <label>Activo</label>
                <select name="activo">
                    <option value="1" {{ $insumo->activo ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ !$insumo->activo ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>

        <div class="top-actions">
            <button type="submit" class="btn btn-success">Actualizar</button>
            <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

    <script>
        document.getElementById('stock_minimo').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            e.target.value = new Intl.NumberFormat('es-CO').format(v);
        });
    </script>
@endsection
