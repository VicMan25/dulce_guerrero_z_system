@extends('layouts.app')

@section('content')
    <h1>Registrar venta</h1>

    <form action="{{ route('ventas.store') }}" method="POST" id="form-venta">
        @csrf

        <div id="productos-container">
            <div class="producto-row" style="border:1px solid #e5e0d5; border-radius:12px; padding:16px; margin-bottom:14px; background:#fffdf8;">
                <div class="grid-two">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Producto</label>
                        <select name="productos[0][id_producto]" class="select2" style="width:100%;">
                            <option value="">Seleccione un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id_producto }}"
                                        data-precio="{{ $producto->precio }}">
                                    {{ $producto->nombre }} — $ {{ number_format($producto->precio, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Cantidad</label>
                        <input type="number" name="productos[0][cantidad]" min="1" value="1" class="cantidad-input">
                    </div>
                </div>
            </div>
        </div>

        <div class="top-actions" style="margin-bottom:20px;">
            <button type="button" id="btn-agregar" class="btn btn-secondary">+ Agregar otro producto</button>
        </div>

        <div style="background:#fffdf8; border-radius:12px; padding:16px 20px; margin-bottom:20px; font-size:1.05rem;">
            <strong>Total estimado: $ <span id="total-display">0</span></strong>
        </div>

        <div class="top-actions">
            <button type="submit" class="btn btn-success">Confirmar venta</button>
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Ver ventas</a>
        </div>
    </form>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
    <script>
        const productosOpciones = @json($productos->map(fn($p) => ['id' => $p->id_producto, 'nombre' => $p->nombre, 'precio' => $p->precio]));

        let index = 1;

        function inicializarSelect2(fila) {
            $(fila).find('.select2').select2({
                placeholder: 'Buscar producto...',
                allowClear: true,
                width: 'resolve'
            }).on('change', calcularTotal);
        }

        function calcularTotal() {
            let total = 0;
            document.querySelectorAll('.producto-row').forEach(function (fila) {
                const select = fila.querySelector('select');
                const cantidadInput = fila.querySelector('.cantidad-input');
                const opcion = select ? select.options[select.selectedIndex] : null;
                const precio = opcion ? parseFloat(opcion.getAttribute('data-precio') || 0) : 0;
                const cantidad = parseInt(cantidadInput ? cantidadInput.value : 0) || 0;
                total += precio * cantidad;
            });
            document.getElementById('total-display').textContent = new Intl.NumberFormat('es-CO').format(total);
        }

        document.getElementById('btn-agregar').addEventListener('click', function () {
            const opcionesHTML = productosOpciones.map(p =>
                `<option value="${p.id}" data-precio="${p.precio}">${p.nombre} — $ ${new Intl.NumberFormat('es-CO').format(p.precio)}</option>`
            ).join('');

            const fila = document.createElement('div');
            fila.className = 'producto-row';
            fila.style = 'border:1px solid #e5e0d5; border-radius:12px; padding:16px; margin-bottom:14px; background:#fffdf8;';
            fila.innerHTML = `
                <div class="grid-two">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Producto</label>
                        <select name="productos[${index}][id_producto]" class="select2" style="width:100%;">
                            <option value="">Seleccione un producto</option>
                            ${opcionesHTML}
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Cantidad</label>
                        <input type="number" name="productos[${index}][cantidad]" min="1" value="1" class="cantidad-input">
                    </div>
                </div>
                <div style="margin-top:10px; text-align:right;">
                    <button type="button" class="btn btn-danger btn-eliminar" style="padding:6px 12px; font-size:0.85rem;">Eliminar</button>
                </div>
            `;

            fila.querySelector('.btn-eliminar').addEventListener('click', function () {
                fila.remove();
                calcularTotal();
            });

            fila.querySelector('.cantidad-input').addEventListener('input', calcularTotal);
            document.getElementById('productos-container').appendChild(fila);
            inicializarSelect2(fila);
            index++;
        });

        $(document).ready(function () {
            inicializarSelect2(document.querySelector('.producto-row'));
            document.querySelector('.cantidad-input').addEventListener('input', calcularTotal);
        });
    </script>
@endsection
