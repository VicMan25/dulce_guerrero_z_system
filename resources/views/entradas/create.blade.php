@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Registrar entrada de inventario</h1>

    <form action="{{ route('entradas.store') }}" method="POST">
        @csrf

        <div class="grid-two">
            <div class="form-group">
                <label>Insumo</label>
                <select name="id_insumo" class="select2" style="width: 100%;">
                    <option value="">Seleccione un insumo</option>
                    @foreach($insumos as $insumo)
                        <option value="{{ $insumo->id_insumo }}"
                            {{ old('id_insumo') == $insumo->id_insumo ? 'selected' : '' }}>
                            {{ $insumo->nombre }} ({{ $insumo->unidad_de_medida }})
                            — Stock actual: {{ number_format($insumo->stock, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad agregada</label>
                <input type="text" name="cantidad" id="cantidad"
                       value="{{ old('cantidad') }}" placeholder="0">
            </div>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Observación <span style="font-weight:400; color:#aaa;">(opcional)</span></label>
                <input type="text" name="observacion" value="{{ old('observacion') }}"
                       placeholder="Ej: Compra a proveedor X">
            </div>
        </div>

        <div class="top-actions" style="margin-top: 10px;">
            <button type="submit" class="btn btn-success">Registrar entrada</button>
            <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: 'Buscar insumo...',
                allowClear: true,
                width: 'resolve'
            });
        });

        document.getElementById('cantidad').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            e.target.value = new Intl.NumberFormat('es-CO').format(v);
        });
    </script>
@endsection
