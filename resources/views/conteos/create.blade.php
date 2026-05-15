@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">← Inventario</a>
    </div>
    <h1>Conteo de inventario</h1>

    <div class="info-banner">
        Ingresa la <strong>cantidad real que encontraste</strong> en bodega para cada insumo.
        Deja en blanco los que no revisaste — solo los que llenes serán actualizados.
        Al guardar, el sistema registra las diferencias y actualiza el stock automáticamente.
    </div>

    <form action="{{ route('conteos.store') }}" method="POST">
        @csrf

        <div class="grid-two" style="margin-bottom:20px;">
            <div class="form-group">
                <label>Fecha del conteo</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Observación <span class="muted">(opcional)</span></label>
                <input type="text" name="observacion" value="{{ old('observacion') }}"
                       placeholder="Ej: Conteo post-domingo">
            </div>
        </div>

        {{-- Buscador en tiempo real --}}
        @if($insumos->isNotEmpty())
        <div style="margin-bottom:14px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="position:relative; flex:1; min-width:200px;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                             font-size:1rem; color:#a5a58d; pointer-events:none;">🔍</span>
                <input type="text" id="buscador-insumo"
                       placeholder="Buscar insumo por nombre..."
                       autocomplete="off"
                       style="width:100%; max-width:100%; padding:11px 12px 11px 36px;
                              border:1.5px solid #d6d6d6; border-radius:10px;
                              background:#fffdf8; font-size:0.95rem; min-height:44px;">
            </div>
            <span id="contador-insumos" class="muted" style="font-size:0.88rem; white-space:nowrap;">
                {{ $insumos->count() }} insumos
            </span>
        </div>
        @endif

        <div class="table-responsive" style="margin-bottom:24px;">
            <table id="tabla-conteo">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th class="col-hide-mobile">Unidad</th>
                        <th style="text-align:center;" class="col-hide-mobile">
                            Actual<br><span style="font-weight:400;font-size:0.78rem;">(sistema)</span>
                        </th>
                        <th style="min-width:130px;">Cant. contada</th>
                        <th style="text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody id="tbody-insumos">
                    @forelse($insumos as $insumo)
                        @php $bajo = $insumo->stock <= $insumo->stock_minimo; @endphp
                        <tr data-nombre="{{ strtolower($insumo->nombre) }}"
                            style="{{ $bajo ? 'background-color:#fff0f1;' : '' }}">
                            <td>
                                <span style="font-weight:{{ $bajo ? '700' : '400' }};">
                                    {{ $insumo->nombre }}
                                </span>
                                <span class="muted info-mob" style="display:none;">
                                    · {{ $insumo->unidad_de_medida }}
                                    · actual: {{ number_format($insumo->stock, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="muted col-hide-mobile">{{ $insumo->unidad_de_medida }}</td>
                            <td style="text-align:center; font-weight:600;
                                       color:{{ $bajo ? '#9b2335' : '#4b4b4b' }};"
                                class="col-hide-mobile">
                                {{ number_format($insumo->stock, 0, ',', '.') }}
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="stock[{{ $insumo->id_insumo }}]"
                                    value="{{ old('stock.' . $insumo->id_insumo) }}"
                                    min="0"
                                    step="1"
                                    placeholder="—"
                                    style="width:100%; max-width:120px; padding:9px 10px;
                                           border:1.5px solid #d6d6d6; border-radius:8px;
                                           background:#fffdf8; font-size:1rem; text-align:center;
                                           min-height:44px;"
                                >
                            </td>
                            <td style="text-align:center;">
                                @if($bajo)
                                    <span class="badge badge-danger">Bajo</span>
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">No hay insumos activos registrados.</td>
                        </tr>
                    @endforelse

                    {{-- Fila "sin resultados" para búsqueda vacía --}}
                    <tr id="fila-sin-resultados" style="display:none;">
                        <td colspan="5" style="text-align:center; padding:24px 0; color:#a5a58d;">
                            Ningún insumo coincide con "<span id="texto-busqueda"></span>"
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($insumos->isNotEmpty())
            <div class="top-actions">
                <button type="submit" class="btn btn-success">Guardar conteo</button>
                <a href="{{ route('insumos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        @endif
    </form>
@endsection

@section('scripts')
<script>
    const buscador      = document.getElementById('buscador-insumo');
    const filas         = document.querySelectorAll('#tbody-insumos tr[data-nombre]');
    const sinResultados = document.getElementById('fila-sin-resultados');
    const textoBusqueda = document.getElementById('texto-busqueda');
    const contador      = document.getElementById('contador-insumos');
    const totalInsumos  = filas.length;

    function aplicarFiltro() {
        const q = buscador ? buscador.value.toLowerCase().trim() : '';
        let visibles = 0;

        filas.forEach(fila => {
            const coincide = fila.dataset.nombre.includes(q);
            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        // Fila "sin resultados"
        if (sinResultados) {
            sinResultados.style.display = (visibles === 0 && q !== '') ? 'table-row' : 'none';
            if (textoBusqueda) textoBusqueda.textContent = q;
        }

        // Contador
        if (contador) {
            contador.textContent = q === ''
                ? `${totalInsumos} insumos`
                : `${visibles} de ${totalInsumos} insumos`;
        }
    }

    if (buscador) {
        buscador.addEventListener('input', aplicarFiltro);
    }

    // En móvil mostrar info extra debajo del nombre
    function toggleMobileInfo() {
        const isMobile = window.innerWidth <= 600;
        document.querySelectorAll('.info-mob').forEach(el => {
            el.style.display = isMobile ? 'inline' : 'none';
        });
    }
    toggleMobileInfo();
    window.addEventListener('resize', toggleMobileInfo);
</script>
@endsection
