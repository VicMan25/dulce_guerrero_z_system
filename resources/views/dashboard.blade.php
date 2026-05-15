@extends('layouts.app')

@section('content')

<div style="margin-bottom:20px;">
    <h2 style="margin-bottom:4px;">
        {{ auth()->user()->esAdmin() ? '👑 Panel de Administrador' : '👤 Panel de Empleado' }}
    </h2>
    <p class="muted">Bienvenido, <strong>{{ auth()->user()->name }}</strong>
        — {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p>
</div>

{{-- Resumen del día --}}
<div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); margin-bottom:28px;">

    <div class="stat-card {{ $cantidadVentasHoy > 0 ? 'stat-success' : 'stat-neutral' }}">
        <div class="stat-label">Ventas de hoy</div>
        <div class="stat-value">{{ $cantidadVentasHoy }}</div>
        @if($totalHoy > 0)
            <div style="font-size:0.82rem; margin-top:5px; opacity:0.8;">
                $ {{ number_format($totalHoy, 0, ',', '.') }}
            </div>
        @else
            <div style="font-size:0.82rem; margin-top:5px; opacity:0.7;">Sin ventas aún</div>
        @endif
    </div>

    @if(auth()->user()->esAdmin())
        <div class="stat-card stat-neutral">
            <div class="stat-label">Total esta semana</div>
            <div class="stat-value" style="font-size:1.2rem;">
                $ {{ number_format($totalSemana, 0, ',', '.') }}
            </div>
        </div>
    @endif

    <div class="stat-card {{ $insumosBajoStock > 0 ? 'stat-danger' : 'stat-success' }}">
        <div class="stat-label">Stock bajo</div>
        <div class="stat-value">{{ $insumosBajoStock }}</div>
        <div style="font-size:0.82rem; margin-top:5px; opacity:0.8;">
            @if($insumosBajoStock > 0)
                <a href="{{ route('insumos.index') }}"
                   style="color:inherit; text-decoration:underline;">Ver insumos →</a>
            @else
                Todo en orden
            @endif
        </div>
    </div>

</div>

{{-- Acceso rápido --}}
<h3 style="margin-bottom:14px; color:#6b705c;">Acceso rápido</h3>

<div class="quick-grid">

    {{-- Nueva venta: siempre primero y destacado --}}
    <a href="{{ route('ventas.create') }}" class="quick-card accent"
       style="border:2px solid #d4a373;">
        <span class="quick-icon">💰</span>
        <p>Nueva venta</p>
    </a>

    <a href="{{ route('ventas.index') }}" class="quick-card">
        <span class="quick-icon">🧾</span>
        <p>Historial ventas</p>
    </a>

    <a href="{{ route('insumos.index') }}" class="quick-card">
        <span class="quick-icon">🧂</span>
        <p>Ver inventario</p>
    </a>

    <a href="{{ route('conteos.create') }}" class="quick-card">
        <span class="quick-icon">📋</span>
        <p>Hacer conteo</p>
    </a>

    @if(auth()->user()->esAdmin())
        <a href="{{ route('entradas.create') }}" class="quick-card">
            <span class="quick-icon">📦</span>
            <p>Registrar entrada</p>
        </a>
        <a href="{{ route('productos.index') }}" class="quick-card">
            <span class="quick-icon">🍬</span>
            <p>Productos</p>
        </a>
        <a href="{{ route('recetas.index') }}" class="quick-card">
            <span class="quick-icon">📝</span>
            <p>Recetas</p>
        </a>
        <a href="{{ route('gastos.index') }}" class="quick-card">
            <span class="quick-icon">📊</span>
            <p>Finanzas</p>
        </a>
        <a href="{{ route('insumos.estadisticas') }}" class="quick-card">
            <span class="quick-icon">📈</span>
            <p>Estadísticas</p>
        </a>
        <a href="{{ route('usuarios.index') }}" class="quick-card">
            <span class="quick-icon">👥</span>
            <p>Usuarios</p>
        </a>
    @endif

</div>

@endsection
