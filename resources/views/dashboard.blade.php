@extends('layouts.app')

@section('content')

<div style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 4px;">
        {{ auth()->user()->esAdmin() ? '👑 Panel de Administrador' : '👤 Panel de Empleado' }}
    </h2>
    <p class="muted">Bienvenido de nuevo, <strong>{{ auth()->user()->name }}</strong></p>
</div>

<div class="quick-grid">

    @if(auth()->user()->esAdmin())
        <a href="{{ route('productos.index') }}" class="quick-card">
            <span class="quick-icon">🍬</span>
            <p>Productos</p>
        </a>
        <a href="{{ route('recetas.index') }}" class="quick-card">
            <span class="quick-icon">📋</span>
            <p>Recetas</p>
        </a>
        <a href="{{ route('entradas.create') }}" class="quick-card">
            <span class="quick-icon">📦</span>
            <p>Registrar entrada</p>
        </a>
        <a href="{{ route('gastos.index') }}" class="quick-card">
            <span class="quick-icon">📊</span>
            <p>Finanzas</p>
        </a>
        <a href="{{ route('usuarios.index') }}" class="quick-card">
            <span class="quick-icon">👥</span>
            <p>Usuarios</p>
        </a>
    @endif

    {{-- Accesos compartidos admin + empleado --}}
    <a href="{{ route('ventas.create') }}" class="quick-card">
        <span class="quick-icon">💰</span>
        <p>Nueva venta</p>
    </a>

    <a href="{{ route('ventas.index') }}" class="quick-card">
        <span class="quick-icon">🧾</span>
        <p>Historial ventas</p>
    </a>

    <a href="{{ route('conteos.create') }}" class="quick-card">
        <span class="quick-icon">📋</span>
        <p>Hacer conteo</p>
    </a>

    <a href="{{ route('insumos.index') }}" class="quick-card">
        <span class="quick-icon">🧂</span>
        <p>Ver inventario</p>
    </a>

</div>

@endsection
