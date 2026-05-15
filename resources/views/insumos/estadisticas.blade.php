@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('insumos.index') }}" class="btn btn-secondary">← Inventario</a>
    </div>
    <h1>Estadísticas de insumos</h1>

    {{-- Filtro de fechas --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('insumos.estadisticas') }}">
            <div class="filter-grid-2">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="fecha_desde" value="{{ $filtroPorDefecto ? '' : $fechaDesde }}">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $filtroPorDefecto ? '' : $fechaHasta }}">
                </div>
            </div>
            <div class="top-actions" style="margin-top:14px;">
                <button type="submit" class="btn">Filtrar</button>
                <a href="{{ route('insumos.estadisticas') }}" class="btn btn-secondary">Esta semana</a>
            </div>
        </form>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="stat-cards">
        <div class="stat-card stat-success">
            <div class="stat-label">Insumo más consumido</div>
            <div class="stat-value" style="font-size:1.05rem; line-height:1.3;">
                {{ $masConsumido ? $masConsumido->nombre : '—' }}
            </div>
            @if($masConsumido)
                <div class="muted" style="font-size:0.82rem; margin-top:5px;">
                    {{ number_format($masConsumido->total_consumido, 1, ',', '.') }}
                    {{ $masConsumido->unidad_de_medida }}
                </div>
            @endif
        </div>

        <div class="stat-card stat-danger">
            <div class="stat-label">Insumo menos consumido</div>
            <div class="stat-value" style="font-size:1.05rem; line-height:1.3;">
                {{ $menosConsumido ? $menosConsumido->nombre : '—' }}
            </div>
            @if($menosConsumido && $menosConsumido !== $masConsumido)
                <div class="muted" style="font-size:0.82rem; margin-top:5px;">
                    {{ number_format($menosConsumido->total_consumido, 1, ',', '.') }}
                    {{ $menosConsumido->unidad_de_medida }}
                </div>
            @endif
        </div>

        <div class="stat-card stat-neutral">
            <div class="stat-label">Tipos de insumos usados</div>
            <div class="stat-value">{{ $totalTipos }}</div>
            <div class="muted" style="font-size:0.82rem; margin-top:5px;">
                @if($filtroPorDefecto)
                    esta semana
                @else
                    {{ $fechaDesde }} → {{ $fechaHasta }}
                @endif
            </div>
        </div>
    </div>

    @if($consumos->isEmpty())
        <div class="info-banner">
            No hay ventas registradas en este período, por lo que no hay consumo de insumos que mostrar.
        </div>
    @else

        {{-- Gráfica diaria --}}
        <div style="background:white; border-radius:12px; box-shadow:var(--shadow); padding:20px 24px; margin-bottom:28px;">
            <h2 style="margin-bottom:16px;">Consumo total por día
                <span class="muted" style="font-size:0.85rem; font-weight:400;">
                    @if($filtroPorDefecto)
                        (Esta semana)
                    @else
                        ({{ $fechaDesde }} → {{ $fechaHasta }})
                    @endif
                </span>
            </h2>
            <canvas id="consumoDiarioChart" height="90"></canvas>
        </div>

        {{-- Gráfica de ranking --}}
        <div style="background:white; border-radius:12px; box-shadow:var(--shadow); padding:20px 24px; margin-bottom:28px;">
            <h2 style="margin-bottom:16px;">Ranking de consumo por insumo</h2>
            @php $altoRanking = min($consumos->count() * 38 + 60, 520); @endphp
            <div style="position:relative; height:{{ $altoRanking }}px;">
                <canvas id="rankingChart"></canvas>
            </div>
        </div>

        {{-- Tabla de detalle --}}
        <div class="section-header">
            <h2>Detalle por insumo</h2>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Insumo</th>
                        <th>Unidad de medida</th>
                        <th style="text-align:center;">Total consumido</th>
                        <th style="text-align:center;" class="col-hide-mobile">Proporción</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalGeneral = $consumos->sum('total_consumido');
                        $pos = 0;
                    @endphp
                    @foreach($consumos as $consumo)
                        @php
                            $pos++;
                            $porcentaje = $totalGeneral > 0 ? ($consumo->total_consumido / $totalGeneral * 100) : 0;
                        @endphp
                        <tr>
                            <td class="muted">{{ $pos }}</td>
                            <td style="font-weight:{{ $pos <= 3 ? '700' : '400' }};">
                                {{ $consumo->nombre }}
                                @if($pos === 1)
                                    <span class="badge badge-success" style="margin-left:6px; font-size:0.75rem;">Mayor</span>
                                @endif
                            </td>
                            <td class="muted">{{ $consumo->unidad_de_medida }}</td>
                            <td style="text-align:center; font-weight:700; font-size:1.05rem;
                                       color:{{ $pos === 1 ? '#3a6642' : ($pos === $consumos->count() ? '#9b2335' : '#4b4b4b') }};">
                                {{ number_format($consumo->total_consumido, 1, ',', '.') }}
                            </td>
                            <td style="text-align:center;" class="col-hide-mobile">
                                <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                                    <div style="background:#eee; border-radius:999px; height:8px; width:80px; overflow:hidden;">
                                        <div style="background:{{ $pos === 1 ? '#588157' : '#d4a373' }};
                                                    height:100%; width:{{ round($porcentaje) }}%; border-radius:999px;">
                                        </div>
                                    </div>
                                    <span class="muted" style="font-size:0.82rem; min-width:36px;">
                                        {{ number_format($porcentaje, 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Gráfica diaria
    const labels   = @json($chartLabels);
    const consumo  = @json($chartConsumo);

    if (document.getElementById('consumoDiarioChart')) {
        new Chart(document.getElementById('consumoDiarioChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    type: 'bar',
                    label: 'Unidades consumidas',
                    data: consumo,
                    backgroundColor: 'rgba(212, 163, 115, 0.75)',
                    borderRadius: 4,
                }, {
                    type: 'line',
                    label: 'Tendencia',
                    data: consumo,
                    borderColor: '#588157',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    tension: 0.35,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${new Intl.NumberFormat('es-CO').format(ctx.parsed.y)} uds.`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => new Intl.NumberFormat('es-CO').format(v) }
                    }
                }
            }
        });
    }

    // Gráfica ranking horizontal
    const rankingLabels = @json($consumos->pluck('nombre'));
    const rankingData   = @json($consumos->pluck('total_consumido')->map(fn($v) => (float) $v));
    const n = rankingData.length;

    if (document.getElementById('rankingChart')) {
        const colors = rankingData.map((_, i) => {
            if (i === 0)       return 'rgba(88, 129, 87, 0.85)';
            if (i === n - 1)   return 'rgba(178, 58, 72, 0.70)';
            return 'rgba(212, 163, 115, 0.75)';
        });

        new Chart(document.getElementById('rankingChart'), {
            type: 'bar',
            data: {
                labels: rankingLabels,
                datasets: [{
                    label: 'Total consumido',
                    data: rankingData,
                    backgroundColor: colors,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${new Intl.NumberFormat('es-CO').format(ctx.parsed.x)} uds.`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { callback: v => new Intl.NumberFormat('es-CO').format(v) }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endsection
