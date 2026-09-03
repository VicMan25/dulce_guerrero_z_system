@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>

    <h1>🧹 Turno de aseo de baños</h1>

    <div class="info-banner">
        Los domingos, si <strong>todos llegan a tiempo</strong>, el aseo de los baños lo hace la
        persona a quien le toca según el orden de la lista. Cuando la lista termina, vuelve a empezar.
        Si <strong>alguien llega tarde</strong>, esa persona hace el aseo (queda en el historial, pero
        <em>no</em> consume el turno de la rotación).
        <br>Los <strong>administradores</strong> no entran en la rotación por defecto; si un administrador
        también trabaja, se agrega con el botón <strong>«Incluir»</strong> de la lista.
        @if(auth()->user()->esAdmin())
            <br>Solo el administrador puede marcar quién lo hizo y en qué fecha.
        @endif
    </div>

    {{-- ── A QUIÉN LE TOCA ─────────────────────────────── --}}
    <div class="stat-cards" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
        <div class="stat-card {{ $turnoActual ? 'stat-success' : 'stat-danger' }}">
            <div class="stat-label">Le toca a</div>
            <div class="stat-value" style="font-size:1.25rem;">
                @if($turnoActual)
                    {{ $turnoActual->user->name }}
                @else
                    Sin personas en rotación
                @endif
            </div>
            @if($turnoActual)
                <div style="font-size:0.82rem; margin-top:5px; opacity:0.8;">
                    Posición {{ $roster->where('activo', true)->values()->search(fn($t) => $t->id === $turnoActual->id) + 1 }}
                    de {{ $totalActivos }}
                </div>
            @endif
        </div>

        <div class="stat-card stat-neutral">
            <div class="stat-label">Ciclo actual</div>
            <div class="stat-value">#{{ $cicloActual }}</div>
            <div style="font-size:0.82rem; margin-top:5px; opacity:0.8;">
                {{ count($hechosEnCiclo) }} de {{ $totalActivos }} completados
            </div>
        </div>
    </div>

    @if($cicloCompletado && $totalActivos > 0)
        <div class="info-banner" style="background:#ddeed8; border-color:#c3ddba; color:#3a6642;">
            🎉 Todos completaron el ciclo anterior. Comienza el ciclo <strong>#{{ $cicloActual }}</strong>
            desde el inicio de la lista.
        </div>
    @endif

    {{-- ── LISTA / ROTACIÓN ────────────────────────────── --}}
    <div class="section-header" style="margin-top:24px;">
        <h2>Lista de rotación</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width:44px;">#</th>
                    <th>Persona</th>
                    <th style="text-align:center;">Estado en el ciclo #{{ $cicloActual }}</th>
                    @if(auth()->user()->esAdmin())
                        <th style="text-align:center;">Orden</th>
                        <th style="text-align:center;">Rotación</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php $posActiva = 0; @endphp
                @forelse($roster as $turno)
                    @php
                        $esActivo = $turno->activo;
                        if ($esActivo) $posActiva++;
                        $hizo = in_array($turno->user_id, $hechosEnCiclo, true);
                        $esTurno = $turnoActual && $turno->id === $turnoActual->id;
                        $fecha = $fechasCiclo[$turno->user_id] ?? null;
                    @endphp
                    <tr @if($esTurno) style="background-color:#eef6e9;" @elseif(!$esActivo) style="opacity:0.55;" @endif>
                        <td style="font-weight:700; color:#6b705c;">
                            {{ $esActivo ? $posActiva : '—' }}
                        </td>
                        <td>
                            {{ $turno->user->name }}
                            @if($turno->user_id === auth()->id())
                                <span class="badge badge-neutral" style="margin-left:6px;">Tú</span>
                            @endif
                            @if($esTurno)
                                <span class="badge badge-success" style="margin-left:6px;">← Le toca</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if(!$esActivo)
                                <span class="badge badge-neutral">🚫 Fuera de rotación</span>
                            @elseif($hizo)
                                <span class="badge badge-success">
                                    ✅ Hecho{{ $fecha ? ' · ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y') : '' }}
                                </span>
                            @else
                                <span class="badge badge-neutral">⏳ Pendiente</span>
                            @endif
                        </td>
                        @if(auth()->user()->esAdmin())
                            <td style="text-align:center; white-space:nowrap;">
                                <form method="POST" action="{{ route('aseo.subir', $turno->id) }}" class="inline-form">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary"
                                            style="padding:5px 10px; font-size:0.8rem; min-height:auto;"
                                            @disabled($loop->first)>▲</button>
                                </form>
                                <form method="POST" action="{{ route('aseo.bajar', $turno->id) }}" class="inline-form">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary"
                                            style="padding:5px 10px; font-size:0.8rem; min-height:auto;"
                                            @disabled($loop->last)>▼</button>
                                </form>
                            </td>
                            <td style="text-align:center;">
                                <form method="POST" action="{{ route('aseo.toggle', $turno->id) }}" class="inline-form">
                                    @csrf
                                    <button type="submit"
                                            class="btn {{ $esActivo ? 'btn-danger' : 'btn-success' }}"
                                            style="padding:6px 12px; font-size:0.8rem; min-height:auto;">
                                        {{ $esActivo ? 'Retirar' : 'Incluir' }}
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->esAdmin() ? 5 : 3 }}" class="muted">
                            No hay personas en la lista. Registra usuarios en el sistema y aparecerán aquí.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── REGISTRAR ASEO (solo admin) ─────────────────── --}}
    @if(auth()->user()->esAdmin() && $roster->where('activo', true)->isNotEmpty())
        <div class="filter-bar" style="margin-top:26px;">
            <h2 style="margin-top:0;">✔ Registrar aseo realizado</h2>
            <form method="POST" action="{{ route('aseo.store') }}">
                @csrf
                <div class="filter-grid-3">
                    <div class="form-group">
                        <label for="user_id">Persona que hizo el aseo</label>
                        <select name="user_id" id="user_id" required>
                            @foreach($roster->where('activo', true) as $turno)
                                <option value="{{ $turno->user_id }}"
                                    {{ (int) old('user_id', $turnoActual?->user_id) === $turno->user_id ? 'selected' : '' }}>
                                    {{ $turno->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha"
                               value="{{ old('fecha', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                               max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Motivo</label>
                        <div style="display:flex; flex-direction:column; gap:6px; padding-top:4px;">
                            <label style="font-weight:500; display:flex; align-items:center; gap:8px; margin:0;">
                                <input type="radio" name="motivo" value="turno" style="width:auto; min-height:auto;"
                                       {{ old('motivo', 'turno') === 'turno' ? 'checked' : '' }}>
                                Todos llegaron a tiempo (turno)
                            </label>
                            <label style="font-weight:500; display:flex; align-items:center; gap:8px; margin:0;">
                                <input type="radio" name="motivo" value="llegada_tarde" style="width:auto; min-height:auto;"
                                       {{ old('motivo') === 'llegada_tarde' ? 'checked' : '' }}>
                                Llegó tarde
                            </label>
                        </div>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <button type="submit" class="btn btn-success">Guardar registro</button>
                </div>
            </form>
        </div>
    @endif

    {{-- ── HISTORIAL ───────────────────────────────────── --}}
    <div class="section-header" style="margin-top:30px;">
        <h2>Historial de aseos</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Persona</th>
                    <th style="text-align:center;">Motivo</th>
                    <th style="text-align:center;" class="col-hide-mobile">Ciclo</th>
                    <th class="col-hide-mobile">Registrado por</th>
                    @if(auth()->user()->esAdmin())
                        <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td style="font-weight:600;">{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $registro->nombre_usuario }}</td>
                        <td style="text-align:center;">
                            @if($registro->motivo === 'turno')
                                <span class="badge badge-success">✅ A tiempo</span>
                            @else
                                <span class="badge badge-danger">⏰ Llegó tarde</span>
                            @endif
                        </td>
                        <td style="text-align:center;" class="col-hide-mobile">#{{ $registro->ciclo }}</td>
                        <td class="muted col-hide-mobile">{{ $registro->registrador->name ?? '—' }}</td>
                        @if(auth()->user()->esAdmin())
                            <td>
                                <form method="POST" action="{{ route('aseo.destroy', $registro->id) }}"
                                      class="inline-form"
                                      onsubmit="return confirm('¿Eliminar este registro de aseo de {{ addslashes($registro->nombre_usuario) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            style="padding:6px 12px; font-size:0.8rem; min-height:auto;">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->esAdmin() ? 6 : 5 }}" class="muted">
                            Aún no hay aseos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
