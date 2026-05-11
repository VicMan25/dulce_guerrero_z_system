<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear producto</title>
</head>
<body>
    <h1>Crear producto</h1>

    @if($errors->any())
        <ul style="color:red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}">
        <br><br>

        <label>Precio:</label>
        <input type="text" name="precio" id="precio" value="{{ old('precio') }}">
        <br><br>

        <label>Activo:</label>
        <select name="activo">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>
        <br><br>

        <button type="submit">Guardar</button>
    </form>

    <br>
    <a href="{{ route('productos.index') }}">Volver</a>

    <script>
    document.getElementById('precio').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = new Intl.NumberFormat('es-CO').format(value);
        e.target.value = value;
    });
    </script>
</body>
</html>