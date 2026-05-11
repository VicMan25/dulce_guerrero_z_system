@extends('layouts.app')

@section('content')
    <h1>Registrar gasto</h1>

    <form action="{{ route('gastos.store') }}" method="POST">
        @csrf

        <div class="grid-two">
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                       placeholder="Ej: Compra de ingredientes">
            </div>

            <div class="form-group">
                <label>Monto</label>
                <input type="text" name="monto" id="monto" value="{{ old('monto') }}"
                       placeholder="0">
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <option value="">Seleccione...</option>
                    @foreach(['Materia prima', 'Servicios', 'Personal', 'Mantenimiento', 'Otros'] as $cat)
                        <option value="{{ $cat }}" {{ old('categoria') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="top-actions" style="margin-top:10px;">
            <button type="submit" class="btn btn-success">Guardar gasto</button>
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
