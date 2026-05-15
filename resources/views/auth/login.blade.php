<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DulceG — Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #d4a373 0%, #e6c09a 50%, #ecd6c0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(248, 100, 150, 0.2);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }

        .login-logo {
            font-size: 2.5rem;
            font-weight: 800;
            color: #d45f31bd;
            letter-spacing: -1px;
        }

        .login-logo span {
            color: #f8b4c8;
        }

        .role-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        /* Roles */
        .role-admin {
            background: #fefae0;
            color: #6b705c;
            border-color: #ccd5ae;
        }

        .role-emp {
            background: #faedcd;
            color: #7a6c5d;
            border-color: #d4a373;
        }

        .role-admin.active {
            background: #6b705c;
            color: white;
        }

        .role-emp.active {
            background: #d4a373;
            color: white;
        }

        /* Botón login */
        .btn-login {
            background: linear-gradient(135deg, #d4a373, #ccd5ae);
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: transform 0.2s, box-shadow 0.2s;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 163, 115, 0.4);
        }

        /* Inputs */
        .form-control:focus {
            border-color: #d4a373;
            box-shadow: 0 0 0 0.2rem rgba(212, 163, 115, 0.25);
        }

        /* Divider */
        .divider {
            color: #a5a58d;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="login-card">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <img src="{{ asset('img/logoDulceG.jpeg') }}" alt="Logo Dulce Guerrero'z"
                 style="height:120px; width:120px; object-fit:cover; border-radius:50%;
                        box-shadow:0 4px 18px rgba(212,163,115,0.35); margin-bottom:14px;">
            <div class="login-logo">Dulce_Guerrero'z</div>
            <p class="text-muted mt-1" style="font-size:0.9rem;">Sistema de gestión</p>
        </div>


        {{-- Alertas --}}
        @if (session('inactividad'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert"
                 style="background:#fff3cd; border-color:#ffc107; color:#856404;">
                <strong>⏰ Sesión cerrada por inactividad.</strong><br>
                <span style="font-size:0.88rem;">Vuelve a iniciar sesión para continuar.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="correo@dulceg.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••"
                        required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">👁</button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                
            </div>

            <button type="submit" class="btn btn-login text-white w-100 mb-3">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center text-muted mt-3" style="font-size:0.78rem;">
            © {{ date('Y') }} DulceG · Todos los derechos reservados
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            p.type = p.type === 'password' ? 'text' : 'password';
        }

        function selectRole(role, el) {
            document.querySelectorAll('.role-badge').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
            // Puedes pre-rellenar el correo de ejemplo si lo deseas:
            const hints = {
                administrador: 'admin@dulceg.com',
                empleado: 'empleado@dulceg.com'
            };
            document.querySelector('[name="email"]').placeholder = hints[role];
        }
    </script>
</body>

</html>
