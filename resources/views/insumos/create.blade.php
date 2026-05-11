@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Nuevo insumo</h1>

    <form action="{{ route('insumos.store') }}" method="POST">
        @csrf

        <div class="grid-two">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       placeholder="Ej: Vasos 7oz">
            </div>

            <div class="form-group">
                <label>Unidad de medida</label>
                <select name="unidad_de_medida">
                    <option value="">Seleccione...</option>
                    @foreach(['unidades', 'paquetes', 'gramos', 'kilogramos', 'mililitros', 'litros', 'porciones'] as $u)
                        <option value="{{ $u }}" {{ old('unidad_de_medida') === $u ? 'selected' : '' }}>
                            {{ ucfirst($u) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Stock inicial</label>
                <input type="text" name="stock" id="stock"
                       value="{{ old('stock', '0') }}" placeholder="0">
            </div>

            <div class="form-group">
                <label>Stock mínimo</label>
                <input type="text" name="stock_minimo" id="stock_minimo"
                       value="{{ old('stock_minimo', '0') }}" placeholder="0">
            </div>
        </div>

        <div class="top-actions" style="margin-top:10px;">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        ['stock', 'stock_minimo'].forEach(function(id) {
            document.getElementById(id).addEventListener('input', function(e) {
                let v = e.target.value.replace(/\D/g, '');
                e.target.value = new Intl.NumberFormat('es-CO').format(v);
            });
        });
    </script>
@endsection
