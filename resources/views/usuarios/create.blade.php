@extends('layouts.app')

@section('content')
    <div class="top-actions">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
    <h1>Nuevo usuario</h1>

    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf

        <div class="grid-two">
            <div class="form-group">
                <label>Nombre completo</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: María López">
            </div>
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="text" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
            </div>
        </div>

        <div class="form-group">
            <label>Rol</label>
            <select name="role" style="max-width:220px;">
                <option value="empleado"       {{ old('role', 'empleado') === 'empleado'       ? 'selected' : '' }}>👤 Empleado</option>
                <option value="administrador"  {{ old('role') === 'administrador'              ? 'selected' : '' }}>👑 Administrador</option>
            </select>
        </div>

        <div class="grid-two">
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres">
            </div>
            <div class="form-group">
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirmation" placeholder="Repite la contraseña">
            </div>
        </div>

        <div class="top-actions" style="margin-top:10px;">
            <button type="submit" class="btn btn-success">Crear usuario</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
@endsection
