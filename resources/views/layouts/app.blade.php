<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistema de Inventario y Ventas' }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d4a373">
    <link rel="apple-touch-icon" href="{{ asset('img/logoDulceG.jpeg') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DulceG">
    <style>
        :root {
            --color-1: #ccd5ae;
            --color-2: #e9edc9;
            --color-3: #fefae0;
            --color-4: #faedcd;
            --color-5: #d4a373;
            --text-dark: #4b4b4b;
            --danger: #b23a48;
            --success: #588157;
            --shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            --radius: 14px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--color-3);
            color: var(--text-dark);
        }

        /* ── NAVBAR ─────────────────────────────────── */
        .navbar {
            background-color: var(--color-5);
            padding: 14px 20px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand {
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            letter-spacing: 0.5px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-logo {
            height: 38px;
            width: auto;
            border-radius: 8px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            background-color: rgba(255, 255, 255, 0.12);
            padding: 9px 14px;
            border-radius: 10px;
            transition: 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .role-tag {
            background-color: rgba(255,255,255,0.22);
            color: white;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        /* Botón hamburguesa — oculto en escritorio */
        .nav-toggle {
            display: none;
            background: rgba(255,255,255,0.18);
            border: none;
            color: white;
            font-size: 1.35rem;
            padding: 7px 13px;
            border-radius: 9px;
            cursor: pointer;
            line-height: 1;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .nav-toggle:hover { background: rgba(255,255,255,0.3); }

        /* ── CONTENIDO ──────────────────────────────── */
        .container {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 20px 40px;
        }

        .page-card {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
        }

        h1, h2, h3 {
            margin-top: 0;
            color: #6b705c;
        }

        h1 { font-size: clamp(1.3rem, 4vw, 1.8rem); }
        h2 { font-size: clamp(1.1rem, 3vw, 1.4rem); }

        .top-actions {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ── BOTONES ────────────────────────────────── */
        .btn,
        button[type="submit"],
        input[type="submit"] {
            background-color: var(--color-5);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s ease;
            display: inline-block;
            min-height: 44px;
            line-height: 1.2;
        }

        .btn:hover,
        button[type="submit"]:hover,
        input[type="submit"]:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary { background-color: #a5a58d; }
        .btn-danger    { background-color: var(--danger); }
        .btn-success   { background-color: var(--success); }

        .inline-form { display: inline; }

        /* ── TABLAS ─────────────────────────────────── */
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            min-width: 480px;
        }

        th {
            background-color: var(--color-1);
            color: #3d405b;
            text-align: left;
            padding: 13px 14px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        td {
            padding: 13px 14px;
            border-top: 1px solid #eee;
            vertical-align: middle;
            font-size: 0.92rem;
        }

        tr:nth-child(even) { background-color: #fcfcf7; }

        /* ── FORMULARIOS ────────────────────────────── */
        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: 0.93rem;
        }

        input[type="text"],
        input[type="number"],
        select,
        input[type="date"],
        textarea {
            width: 100%;
            max-width: 420px;
            padding: 11px 12px;
            border: 1px solid #d6d6d6;
            border-radius: 10px;
            background-color: #fffdf8;
            font-size: 0.95rem;
            font-family: inherit;
            min-height: 44px;
        }

        textarea { resize: vertical; min-height: 80px; max-width: 420px; }

        .grid-two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .small-input  { max-width: 140px; }

        /* ── BADGES ─────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success { background-color: #ddeed8; color: var(--success); }
        .badge-danger  { background-color: #f8d7da; color: var(--danger); }
        .badge-neutral { background-color: #ece7d5; color: #7a6c5d; }

        /* ── NOTIFICACIONES ─────────────────────────── */
        .notification-wrapper {
            position: fixed;
            top: 80px;
            right: 16px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: calc(100vw - 32px);
        }

        .notification {
            min-width: 300px;
            max-width: 420px;
            padding: 13px 16px;
            border-radius: 12px;
            color: white;
            box-shadow: var(--shadow);
            animation: fadeIn 0.35s ease;
            word-wrap: break-word;
        }

        .notification.success { background-color: var(--success); }
        .notification.error   { background-color: var(--danger); }

        .notification ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .muted { color: #7a7a7a; font-size: 0.93rem; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── STAT CARDS ─────────────────────────────── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }
        .stat-card { border-radius: 12px; padding: 18px 20px; }
        .stat-card .stat-label { font-size: .86rem; font-weight: 600; margin-bottom: 5px; }
        .stat-card .stat-value { font-size: 1.4rem; font-weight: 700; line-height: 1.2; }
        .stat-success { background: #ddeed8; }
        .stat-success .stat-label { color: #588157; }
        .stat-success .stat-value { color: #3a6642; }
        .stat-danger { background: #f8d7da; }
        .stat-danger .stat-label { color: #b23a48; }
        .stat-danger .stat-value { color: #9b2335; }
        .stat-neutral { background: var(--color-4); }
        .stat-neutral .stat-label { color: #7a6c5d; }
        .stat-neutral .stat-value { color: #4b4b4b; }

        /* ── FILTER BAR ─────────────────────────────── */
        .filter-bar {
            background: var(--color-2);
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 22px;
        }
        .filter-bar .form-group { margin-bottom: 0; }

        .filter-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            align-items: flex-end;
        }
        .filter-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            align-items: flex-end;
        }

        /* ── SECTION HEADER ─────────────────────────── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }
        .section-header h2 { margin: 0; }

        /* ── INFO BANNER ────────────────────────────── */
        .info-banner {
            background: #fefae0;
            border: 1px solid #ece7d5;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 18px;
            color: #7a6c5d;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .info-banner a { color: var(--color-5); }

        /* ── DASHBOARD QUICK GRID ───────────────────── */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
            gap: 16px;
        }
        .quick-card {
            display: block;
            text-decoration: none;
            background: var(--color-2);
            border-radius: var(--radius);
            padding: 24px 16px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: var(--shadow);
        }
        .quick-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .quick-card .quick-icon { font-size: 2.2rem; display: block; }
        .quick-card p { margin: 10px 0 0; font-weight: 700; color: #6b705c; font-size: 0.9rem; }
        .quick-card.accent { background: var(--color-4); }

        /* ── RESPONSIVE ─────────────────────────────── */
        @media (max-width: 768px) {

            /* --- Navbar --- */
            .nav-toggle { display: block; }

            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--color-5);
                padding: 14px 20px 20px;
                gap: 8px;
                box-shadow: 0 10px 24px rgba(0,0,0,0.18);
                z-index: 999;
            }

            .nav-links.open { display: flex; }

            .nav-links a {
                text-align: center;
                padding: 12px 16px;
                font-size: 0.95rem;
            }

            .role-tag {
                text-align: center;
                width: 100%;
                padding: 8px 16px;
            }

            .nav-links .inline-form { width: 100%; }
            .nav-links .inline-form button { width: 100%; text-align: center; }

            /* --- Layout --- */
            .container { padding: 0 12px 30px; margin: 16px auto; }
            .page-card { padding: 18px 16px; }

            /* --- Grids --- */
            .grid-two        { grid-template-columns: 1fr; }
            .stat-cards      { grid-template-columns: 1fr 1fr; }
            .filter-grid-3   { grid-template-columns: 1fr; }
            .filter-grid-2   { grid-template-columns: 1fr; }
            .quick-grid      { grid-template-columns: 1fr 1fr; gap: 12px; }

            /* --- Inputs full width --- */
            input[type="text"],
            input[type="number"],
            input[type="date"],
            textarea,
            select { max-width: 100%; }

            /* --- Notificaciones --- */
            .notification {
                min-width: unset;
                width: 100%;
            }

            /* --- Top actions wrap nicely --- */
            .top-actions { gap: 8px; }
            .top-actions .btn,
            .top-actions button { font-size: 0.85rem; padding: 10px 13px; }
        }

        @media (max-width: 480px) {
            .stat-cards  { grid-template-columns: 1fr; }
            .quick-grid  { grid-template-columns: 1fr 1fr; }

            h1 { font-size: 1.25rem; }
            h2 { font-size: 1.05rem; }
        }

        /* Ocultar columnas opcionales en móvil (usado en conteos) */
        @media (max-width: 600px) {
            .col-hide-mobile { display: none !important; }
        }
    </style>
    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">

            <a href="{{ route('dashboard') }}" class="brand">
                @if(file_exists(public_path('img/logo.png')))
                    <img src="{{ asset('img/logoDulceG.jpeg') }}" alt="Logo Dulce Guerrero'z" class="brand-logo">
                @elseif(file_exists(public_path('img/logo.jpg')))
                    <img src="{{ asset('img/logoDulceG.jpeg') }}" alt="Logo Dulce Guerrero'z" class="brand-logo">
                @elseif(file_exists(public_path('img/logo.jpeg')))
                    <img src="{{ asset('img/logoDulceG.jpeg') }}" alt="Logo Dulce Guerrero'z" class="brand-logo">
                @elseif(file_exists(public_path('img/logoDulceG.jpeg')))
                    <img src="{{ asset('img/logoDulceG.jpeg') }}" alt="Logo Dulce Guerrero'z" class="brand-logo">
                @else
                    🍬
                @endif
                Dulce Guerrero'z
            </a>

            {{-- Botón hamburguesa (solo móvil) --}}
            <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">☰</button>

            <div class="nav-links" id="navLinks">

                @auth
                    @if(auth()->user()->esAdmin())
                        {{-- ADMINISTRADOR --}}
                        <a href="{{ route('productos.index') }}"
                           class="{{ request()->routeIs('productos.*') ? 'active' : '' }}">
                            🍬 Productos
                        </a>
                        <a href="{{ route('recetas.index') }}"
                           class="{{ request()->routeIs('recetas.*') ? 'active' : '' }}">
                            📋 Recetas
                        </a>
                        <a href="{{ route('insumos.index') }}"
                           class="{{ request()->routeIs('insumos.*') || request()->routeIs('conteos.*') ? 'active' : '' }}">
                            🧂 Insumos
                        </a>
                        <a href="{{ route('entradas.index') }}"
                           class="{{ request()->routeIs('entradas.*') ? 'active' : '' }}">
                            📦 Entradas
                        </a>
                        <a href="{{ route('ventas.index') }}"
                           class="{{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                            💰 Ventas
                        </a>
                        <a href="{{ route('gastos.index') }}"
                           class="{{ request()->routeIs('gastos.*') || request()->routeIs('ingresos.*') ? 'active' : '' }}">
                            📊 Finanzas
                        </a>
                        <a href="{{ route('usuarios.index') }}"
                           class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            👥 Usuarios
                        </a>
                    @else
                        {{-- EMPLEADO --}}
                        <a href="{{ route('ventas.create') }}"
                           class="{{ request()->routeIs('ventas.create') ? 'active' : '' }}">
                            💰 Nueva venta
                        </a>
                        <a href="{{ route('ventas.index') }}"
                           class="{{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                            🧾 Ventas
                        </a>
                        <a href="{{ route('insumos.index') }}"
                           class="{{ request()->routeIs('insumos.*') ? 'active' : '' }}">
                            🧂 Inventario
                        </a>
                        <a href="{{ route('conteos.index') }}"
                           class="{{ request()->routeIs('conteos.*') ? 'active' : '' }}">
                            📋 Conteos
                        </a>
                    @endif

                    <span class="role-tag">
                        {{ auth()->user()->esAdmin() ? '👑 Admin' : '👤 Empleado' }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline-form">
                        @csrf
                        <button type="submit" class="btn btn-danger">Salir</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Notificaciones --}}
    <div class="notification-wrapper">
        @if(session('success'))
            <div class="notification success auto-hide">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="notification error auto-hide">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="notification error auto-hide">
                <strong>Revisa estos campos:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="container">
        <div class="page-card">
            @yield('content')
        </div>
    </main>

    <script>
        // Auto-ocultar notificaciones
        setTimeout(() => {
            document.querySelectorAll('.auto-hide').forEach(el => {
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(() => el.remove(), 500);
            });
        }, 3500);

        // Hamburger menu
        const navToggle = document.getElementById('navToggle');
        const navLinks  = document.getElementById('navLinks');

        if (navToggle && navLinks) {
            navToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const open = navLinks.classList.toggle('open');
                navToggle.textContent = open ? '✕' : '☰';
                navToggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
            });

            // Cerrar al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!navLinks.contains(e.target) && !navToggle.contains(e.target)) {
                    navLinks.classList.remove('open');
                    navToggle.textContent = '☰';
                    navToggle.setAttribute('aria-label', 'Abrir menú');
                }
            });

            // Cerrar al seleccionar un link
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('open');
                    navToggle.textContent = '☰';
                });
            });
        }
    </script>

    {{-- ── INACTIVIDAD ──────────────────────────────────────────────────── --}}
    @auth
    {{-- Modal de advertencia --}}
    <div id="modal-inactividad"
         style="display:none; position:fixed; inset:0; z-index:9999;
                background:rgba(0,0,0,0.55); align-items:center; justify-content:center;">
        <div style="background:white; border-radius:18px; padding:32px 28px;
                    max-width:380px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3);
                    text-align:center; animation:fadeIn 0.3s ease;">
            <div style="font-size:2.8rem; margin-bottom:10px;">⏰</div>
            <h3 style="color:#6b705c; margin:0 0 8px; font-size:1.15rem;">Sesión a punto de cerrar</h3>
            <p style="color:#7a7a7a; font-size:0.93rem; margin-bottom:6px; line-height:1.5;">
                No hemos detectado actividad.<br>
                Tu sesión se cerrará en:
            </p>
            <div id="countdown-display"
                 style="font-size:2rem; font-weight:800; color:#b23a48;
                        margin:10px 0 22px; letter-spacing:2px;">5:00</div>
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <button onclick="extenderSesion()"
                        style="background:#588157; color:white; border:none; border-radius:10px;
                               padding:12px 22px; font-size:0.95rem; font-weight:600; cursor:pointer;
                               min-height:44px; transition:opacity 0.2s;">
                    ✔ Continuar sesión
                </button>
                <button onclick="cerrarSesionAhora()"
                        style="background:#b23a48; color:white; border:none; border-radius:10px;
                               padding:12px 22px; font-size:0.95rem; font-weight:600; cursor:pointer;
                               min-height:44px; transition:opacity 0.2s;">
                    Cerrar sesión
                </button>
            </div>
        </div>
    </div>

    {{-- Formulario oculto para logout por inactividad --}}
    <form id="form-logout-inactividad" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
        <input type="hidden" name="inactividad" value="1">
    </form>

    <script>
    (function () {
        const INACTIVO_MS  = 25 * 60 * 1000; // 25 min sin actividad → mostrar modal
        const AVISO_SEG    = 5 * 60;          // 5 min de cuenta regresiva en modal

        const modal   = document.getElementById('modal-inactividad');
        const display = document.getElementById('countdown-display');
        let timerInac = null;
        let timerCuenta = null;
        let segundos  = AVISO_SEG;

        function fmt(s) {
            const m = Math.floor(s / 60);
            const ss = String(s % 60).padStart(2, '0');
            return `${m}:${ss}`;
        }

        function mostrarModal() {
            segundos = AVISO_SEG;
            display.textContent = fmt(segundos);
            modal.style.display = 'flex';

            timerCuenta = setInterval(function () {
                segundos--;
                display.textContent = fmt(segundos);
                if (segundos <= 0) {
                    clearInterval(timerCuenta);
                    cerrarPorInactividad();
                }
            }, 1000);
        }

        function resetTimer() {
            if (modal.style.display === 'flex') return; // modal visible: no resetear
            clearTimeout(timerInac);
            timerInac = setTimeout(mostrarModal, INACTIVO_MS);
        }

        window.extenderSesion = function () {
            clearInterval(timerCuenta);
            modal.style.display = 'none';
            fetch('{{ route("ping.session") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(function () {});
            resetTimer();
        };

        window.cerrarSesionAhora = function () {
            cerrarPorInactividad();
        };

        function cerrarPorInactividad() {
            clearInterval(timerCuenta);
            modal.style.display = 'none';
            document.getElementById('form-logout-inactividad').submit();
        }

        ['mousemove', 'mousedown', 'keypress', 'touchstart', 'scroll', 'click'].forEach(function (ev) {
            document.addEventListener(ev, resetTimer, { passive: true });
        });

        resetTimer();
    })();
    </script>
    @endauth

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('scripts')
</body>
</html>
