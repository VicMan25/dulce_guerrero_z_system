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

        <div class="table-responsive" style="margin-bottom:24px;">
            <table>
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
                <tbody>
                    @forelse($insumos as $insumo)
                        @php $bajo = $insumo->stock <= $insumo->stock_minimo; @endphp
                        <tr style="{{ $bajo ? 'background-color:#fff0f1;' : '' }}">
                            <td>
                                <span style="font-weight:{{ $bajo ? '700' : '400' }};">
                                    {{ $insumo->nombre }}
                                </span>
                                {{-- En móvil mostramos la unidad y el stock debajo del nombre --}}
                                <span class="muted" style="display:none;" id="info-mob-{{ $insumo->id_insumo }}">
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
    // En móvil mostrar info extra debajo del nombre
    function toggleMobileInfo() {
        const isMobile = window.innerWidth <= 600;
        document.querySelectorAll('[id^="info-mob-"]').forEach(el => {
            el.style.display = isMobile ? 'inline' : 'none';
        });
    }
    toggleMobileInfo();
    window.addEventListener('resize', toggleMobileInfo);
</script>
@endsection
