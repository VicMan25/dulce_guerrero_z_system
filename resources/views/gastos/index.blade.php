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
                    <input type="date" name="fecha_desde" value="{{ $fechaDesde }}">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}">
                </div>
            </div>
            <div class="top-actions" style="margin-top: 14px;">
                <button type="submit" class="btn">Filtrar</button>
                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Limpiar filtros</a>
            </div>
        </form>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
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
            <div class="stat-label">Balance</div>
            <div class="stat-value">$ {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Gráfica de evolución --}}
    <div style="background: white; border-radius: 12px; box-shadow: var(--shadow); padding: 20px 24px; margin-bottom: 28px;">
        <h2 style="margin-bottom: 16px;">Evolución financiera
            <span class="muted" style="font-size: 0.85rem; font-weight: 400;">
                ({{ $fechaDesde ? $fechaDesde : 'últimos 30 días' }}
                {{ $fechaHasta ? '→ ' . $fechaHasta : '' }})
            </span>
        </h2>
        <canvas id="finanzasChart" height="90"></canvas>
    </div>

    {{-- Ingresos manuales --}}
    <div class="section-header">
        <h2>Ingresos manuales</h2>
        <a href="{{ route('ingresos.create') }}" class="btn btn-success">+ Registrar ingreso</a>
    </div>

    <div class="table-responsive" style="margin-bottom: 28px;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Fecha</th>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No hay ingresos manuales en este período.</td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No hay gastos registrados en este período.</td>
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

        new Chart(document.getElementById('finanzasChart'), {
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Ventas del sistema',
                        data: ventas,
                        backgroundColor: 'rgba(88, 129, 87, 0.75)',
                        borderRadius: 4,
                        stack: 'ingresos',
                    },
                    {
                        type: 'bar',
                        label: 'Ingresos manuales',
                        data: manuales,
                        backgroundColor: 'rgba(212, 163, 115, 0.85)',
                        borderRadius: 4,
                        stack: 'ingresos',
                    },
                    {
                        type: 'bar',
                        label: 'Gastos',
                        data: gastos,
                        backgroundColor: 'rgba(178, 58, 72, 0.7)',
                        borderRadius: 4,
                        stack: 'gastos',
                    },
                    {
                        type: 'line',
                        label: 'Balance',
                        data: balances,
                        borderColor: '#3a6642',
                        backgroundColor: 'rgba(58, 102, 66, 0.08)',
                        borderWidth: 2.5,
                        pointRadius: 3,
                        tension: 0.3,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const val = new Intl.NumberFormat('es-CO').format(ctx.parsed.y);
                                return `${ctx.dataset.label}: $${val}`;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: v => '$' + new Intl.NumberFormat('es-CO').format(v)
                        }
                    }
                }
            }
        });
    </script>
@endsection
