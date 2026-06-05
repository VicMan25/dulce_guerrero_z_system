@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Finanzas</h1>

    {{-- Filtro por período --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('gastos.index') }}">
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
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Esta semana</a>
            </div>
        </form>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));">
        <div class="stat-card stat-neutral">
            <div class="stat-label">Ventas del sistema</div>
            <div class="stat-value">$ {{ number_format($totalVentas, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card stat-neutral">
            <div class="stat-label">Ingresos manuales</div>
            <div class="stat-value">$ {{ number_format($totalManuales, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-label">Total ingresos</div>
            <div class="stat-value">$ {{ number_format($totalIngresos, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-label">Total gastos</div>
            <div class="stat-value">$ {{ number_format($totalGastos, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card {{ $balance >= 0 ? 'stat-success' : 'stat-danger' }}">
            <div class="stat-label">Balance neto</div>
            <div class="stat-value">
                {{ $balance >= 0 ? '+' : '' }}$ {{ number_format($balance, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Gráfica principal: Evolución financiera --}}
    <div style="background:white; border-radius:12px; box-shadow:var(--shadow); padding:22px 24px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:16px;">
            <h2 style="margin:0;">Evolución financiera</h2>
            <span class="badge badge-neutral">
                @if($filtroPorDefecto)
                    Esta semana
                @else
                    {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}
                    →
                    {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                @endif
            </span>
        </div>
        <canvas id="finanzasChart" height="90"></canvas>
    </div>

    {{-- Gráficas secundarias: Distribuciones --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:28px;">

        {{-- Donut: Gastos por categoría --}}
        <div style="background:white; border-radius:12px; box-shadow:var(--shadow); padding:22px 24px;">
            <h2 style="margin-bottom:16px;">Gastos por categoría</h2>
            @if($totalGastos > 0)
                <div style="position:relative; height:230px;">
                    <canvas id="categoriasChart"></canvas>
                </div>
            @else
                <div class="muted" style="text-align:center; padding:40px 0; font-size:0.93rem;">
                    Sin gastos en este período.
                </div>
            @endif
        </div>

        {{-- Donut: Composición de ingresos --}}
        <div style="background:white; border-radius:12px; box-shadow:var(--shadow); padding:22px 24px;">
            <h2 style="margin-bottom:16px;">Composición de ingresos</h2>
            @if($totalIngresos > 0)
                <div style="position:relative; height:230px;">
                    <canvas id="ingresosChart"></canvas>
                </div>
            @else
                <div class="muted" style="text-align:center; padding:40px 0; font-size:0.93rem;">
                    Sin ingresos en este período.
                </div>
            @endif
        </div>

    </div>

    {{-- Ingresos manuales --}}
    <div class="section-header">
        <h2>Ingresos manuales</h2>
        <a href="{{ route('ingresos.create') }}" class="btn btn-success">+ Registrar ingreso</a>
    </div>

    <div class="table-responsive" style="margin-bottom:28px;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresosManuales as $ingreso)
                    <tr>
                        <td>{{ $ingreso->id_ingreso }}</td>
                        <td>{{ $ingreso->descripcion ?? '—' }}</td>
                        <td><span class="badge badge-success">{{ $ingreso->categoria }}</span></td>
                        <td>$ {{ number_format($ingreso->monto, 0, ',', '.') }}</td>
                        <td>{{ $ingreso->fecha }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('ingresos.edit', $ingreso->id_ingreso) }}"
                               class="btn btn-secondary"
                               style="font-size:0.82rem; padding:7px 11px; min-height:36px;">
                                Editar
                            </a>
                            <form action="{{ route('ingresos.destroy', $ingreso->id_ingreso) }}"
                                  method="POST" class="inline-form"
                                  onsubmit="return confirm('¿Eliminar este ingreso?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        style="font-size:0.82rem; padding:7px 11px; min-height:36px;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No hay ingresos manuales en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Gastos --}}
    <div class="section-header">
        <h2>Registro de gastos</h2>
        <a href="{{ route('gastos.create') }}" class="btn">+ Registrar gasto</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                    <tr>
                        <td>{{ $gasto->id_gasto }}</td>
                        <td>{{ $gasto->descripcion }}</td>
                        <td><span class="badge badge-neutral">{{ $gasto->categoria }}</span></td>
                        <td>$ {{ number_format($gasto->monto, 0, ',', '.') }}</td>
                        <td>{{ $gasto->fecha }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('gastos.edit', $gasto->id_gasto) }}"
                               class="btn btn-secondary"
                               style="font-size:0.82rem; padding:7px 11px; min-height:36px;">
                                Editar
                            </a>
                            <form action="{{ route('gastos.destroy', $gasto->id_gasto) }}"
                                  method="POST" class="inline-form"
                                  onsubmit="return confirm('¿Eliminar este gasto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        style="font-size:0.82rem; padding:7px 11px; min-height:36px;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No hay gastos registrados en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const labels   = @json($chartLabels);
    const ventas   = @json($chartVentas);
    const manuales = @json($chartManuales);
    const gastos   = @json($chartGastos);
    const balances = labels.map((_, i) => ventas[i] + manuales[i] - gastos[i]);

    const fmt = v => '$' + new Intl.NumberFormat('es-CO').format(Math.round(v));

    // ── Gráfica principal: Evolución ──────────────────────────────
    new Chart(document.getElementById('finanzasChart'), {
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Ventas del sistema',
                    data: ventas,
                    backgroundColor: 'rgba(88, 129, 87, 0.78)',
                    borderRadius: 5,
                    borderSkipped: 'bottom',
                    stack: 'ingresos',
                    order: 2,
                },
                {
                    type: 'bar',
                    label: 'Ingresos manuales',
                    data: manuales,
                    backgroundColor: 'rgba(212, 163, 115, 0.88)',
                    borderRadius: 5,
                    borderSkipped: 'bottom',
                    stack: 'ingresos',
                    order: 2,
                },
                {
                    type: 'bar',
                    label: 'Gastos',
                    data: gastos,
                    backgroundColor: 'rgba(178, 58, 72, 0.72)',
                    borderRadius: 5,
                    borderSkipped: 'bottom',
                    stack: 'gastos',
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Balance neto',
                    data: balances,
                    borderColor: '#3a5a40',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    borderDash: [6, 3],
                    pointRadius: 5,
                    pointBackgroundColor: balances.map(v => v >= 0 ? '#588157' : '#b23a48'),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    order: 1,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 18,
                        font: { size: 12 },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(50, 50, 50, 0.93)',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 13,
                    callbacks: {
                        label: ctx => {
                            const v = ctx.parsed.y;
                            const sign = v < 0 ? '-' : '';
                            return `  ${ctx.dataset.label}: ${sign}${fmt(Math.abs(v))}`;
                        },
                        afterBody: items => {
                            const i = items[0].dataIndex;
                            const b = balances[i];
                            const sign = b >= 0 ? '+' : '';
                            return ['', `  Balance: ${sign}${fmt(b)}`];
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } },
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.06)' },
                    ticks: {
                        font: { size: 11 },
                        callback: v => {
                            const abs = Math.abs(v);
                            if (abs >= 1_000_000) return (v < 0 ? '-' : '') + '$' + (abs / 1_000_000).toFixed(1) + 'M';
                            if (abs >= 1_000)     return (v < 0 ? '-' : '') + '$' + (abs / 1_000).toFixed(0) + 'k';
                            return '$' + v;
                        },
                    },
                },
            },
        },
    });

    // ── Donut: Gastos por categoría ───────────────────────────────
    @if($totalGastos > 0)
    const catLabels = @json($gastosPorCategoria->keys());
    const catData   = @json($gastosPorCategoria->values()->map(fn($v) => (float)$v));
    const catTotal  = catData.reduce((a, b) => a + b, 0);

    const catColors = [
        'rgba(212, 163, 115, 0.90)',
        'rgba(88,  129,  87, 0.85)',
        'rgba(178,  58,  72, 0.80)',
        'rgba(165, 165, 141, 0.85)',
        'rgba(204, 213, 174, 0.90)',
    ];

    new Chart(document.getElementById('categoriasChart'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catData,
                backgroundColor: catColors.slice(0, catLabels.length),
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, font: { size: 11 }, usePointStyle: true, pointStyle: 'circle' },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const pct = (ctx.parsed / catTotal * 100).toFixed(1);
                            return ` ${fmt(ctx.parsed)}  (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
    @endif

    // ── Donut: Composición de ingresos ────────────────────────────
    @if($totalIngresos > 0)
    const ingTotal = {{ $totalIngresos }};

    new Chart(document.getElementById('ingresosChart'), {
        type: 'doughnut',
        data: {
            labels: ['Ventas del sistema', 'Ingresos manuales'],
            datasets: [{
                data: [{{ $totalVentas }}, {{ $totalManuales }}],
                backgroundColor: [
                    'rgba(88, 129, 87, 0.82)',
                    'rgba(212, 163, 115, 0.88)',
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, font: { size: 11 }, usePointStyle: true, pointStyle: 'circle' },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const pct = (ctx.parsed / ingTotal * 100).toFixed(1);
                            return ` ${fmt(ctx.parsed)}  (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
    @endif
</script>
@endsection
