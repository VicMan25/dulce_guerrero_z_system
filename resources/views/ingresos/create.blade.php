@extends('layouts.app')

@section('content')
    <h1>Registrar ingreso manual</h1>

    <div class="info-banner">
        Usa este formulario para registrar dinero recibido que no viene de ventas del sistema,
        como el total contado en caja al cierre del día, transferencias recibidas u otras entradas de dinero.
    </div>

    <form action="{{ route('ingresos.store') }}" method="POST">
        @csrf

        <div class="grid-two">
            <div class="form-group">
                <label>Monto</label>
                <input type="text" name="monto" id="monto" value="{{ old('monto') }}"
                       placeholder="0">
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <option value="">Seleccione...</option>
                    @foreach(['Caja diaria', 'Transferencia', 'Otros'] as $cat)
                        <option value="{{ $cat }}" {{ old('categoria') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label>Descripción <span class="muted">(opcional)</span></label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                       placeholder="Ej: Cierre de caja del día">
            </div>
        </div>

        <div class="top-actions" style="margin-top: 10px;">
            <button type="submit" class="btn btn-success">Guardar ingreso</button>
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
