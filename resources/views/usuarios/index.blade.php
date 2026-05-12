@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
    </div>
    <h1>Gestión de usuarios</h1>

    <div class="top-actions">
        <a href="{{ route('usuarios.create') }}" class="btn btn-success">+ Nuevo usuario</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th style="text-align:center;">Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            {{ $usuario->name }}
                            @if($usuario->id === auth()->id())
                                <span class="badge badge-neutral" style="margin-left:6px;">Tú</span>
                            @endif
                        </td>
                        <td class="muted">{{ $usuario->email }}</td>
                        <td style="text-align:center;">
                            @if($usuario->esAdmin())
                                <span class="badge badge-success">👑 Administrador</span>
                            @else
                                <span class="badge badge-neutral">👤 Empleado</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                   class="btn btn-secondary" style="padding:6px 12px; font-size:0.85rem;">
                                    Editar
                                </a>
                                @if($usuario->id !== auth()->id())
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario->id) }}"
                                          class="inline-form"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar a {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                                style="padding:6px 12px; font-size:0.85rem;">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
