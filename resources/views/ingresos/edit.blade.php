@extends('layouts.app')

@section('content')
    <h1>Editar ingreso manual</h1>

    <form action="{{ route('ingresos.update', $ingreso->id_ingreso) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid-two">
            <div class="form-group">
                <label>Monto</label>
                <input type="text" name="monto" id="monto"
                       value="{{ old('monto', number_format($ingreso->monto, 0, ',', '.')) }}"
                       placeholder="0">
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <option value="">Seleccione...</option>
                    @foreach(['Caja diaria', 'Transferencia', 'Otros'] as $cat)
                        <option value="{{ $cat }}"
                            {{ old('categoria', $ingreso->categoria) == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ old('fecha', $ingreso->fecha) }}">
            </div>

            <div class="form-group">
                <label>Descripción <span class="muted">(opcional)</span></label>
                <input type="text" name="descripcion" value="{{ old('descripcion', $ingreso->descripcion) }}"
                       placeholder="Ej: Cierre de caja del día">
            </div>
        </div>

        <div class="top-actions" style="margin-top: 10px;">
            <button type="submit" class="btn btn-success">Guardar cambios</button>
            <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.getElementById('monto').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            e.target.value = new Intl.NumberFormat('es-CO').format(v);
        });
    </script>
@endsection
