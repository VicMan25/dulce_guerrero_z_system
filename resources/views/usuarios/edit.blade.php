@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Editar usuario</h1>

    @if($usuario->id === auth()->id())
        <div class="info-banner">
            ⚠️ Estás editando tu propia cuenta. No puedes cambiar tu rol de administrador.
        </div>
    @endif

    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
        @csrf
        @method('PUT')

        <div class="grid-two">
            <div class="form-group">
                <label>Nombre completo</label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}" placeholder="Nombre">
            </div>
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="text" name="email" value="{{ old('email', $usuario->email) }}" placeholder="correo@ejemplo.com">
            </div>
        </div>

        <div class="form-group">
            <label>Rol</label>
            <select name="role" style="max-width:220px;" {{ $usuario->id === auth()->id() ? 'disabled' : '' }}>
                <option value="empleado"      {{ old('role', $usuario->role) === 'empleado'      ? 'selected' : '' }}>👤 Empleado</option>
                <option value="administrador" {{ old('role', $usuario->role) === 'administrador' ? 'selected' : '' }}>👑 Administrador</option>
            </select>
            {{-- Si está deshabilitado, enviamos el valor igualmente --}}
            @if($usuario->id === auth()->id())
                <input type="hidden" name="role" value="administrador">
            @endif
        </div>

        <div style="background:var(--color-2); border-radius:12px; padding:16px 18px; margin-bottom:18px;">
            <p style="margin:0 0 14px; font-weight:600; color:#6b705c;">
                Cambiar contraseña
                <span class="muted" style="font-weight:400; font-size:0.88rem;">(deja en blanco para mantener la actual)</span>
            </p>
            <div class="grid-two">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Nueva contraseña</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" placeholder="Repite la contraseña">
                </div>
            </div>
        </div>

        <div class="top-actions">
            <button type="submit" class="btn btn-success">Guardar cambios</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
