<?php
/**
 * HEADER DINÁMICO - HOSPITAL & HUMAN
 * 
 * Funcionalidad:
 * - Encabezado dinámico que se adapta según el rol del usuario
 * - Navegación responsiva
 * - Logo que se desplaza al hacer scroll
 * 
 * Roles soportados:
 * - admin: Acceso a panel de administración
 * - veterinario: Acceso a panel del veterinario
 * - user: Acceso a panel del usuario
 * - guest: Sin autenticación
 */

// Determinar el rol del usuario
$rol = $_SESSION['rol'] ?? 'guest';
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
$es_autenticado = isset($_SESSION['id']);
?>
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<header class="header-dynamic" id="headerDynamic">
    <div class="header-container">
        <!-- Logo -->
        <div class="logo-container">
            <a href="/citas_medicas/index.php" class="logo">
                <img src="/citas_medicas/assets/img/logo.png" alt="PET HOSPITAL AND RESCUE" class="logo-img">
            </a>
        </div>

        <!-- Controles de navegación rápida -->
        <div class="header-controls">
            <button type="button" class="btn-nav" onclick="history.back();" title="Volver">
                <i class="bx bx-arrow-back"></i>
            </button>
            <a href="/citas_medicas/index.php" class="btn-nav" title="Inicio">
                <i class="bx bx-home"></i>
            </a>
        </div>

        <!-- Navegación -->
        <nav class="nav-menu" id="navMenu">
            <?php if (!$es_autenticado): ?>
                <!-- Menú para usuarios no autenticados -->
                <a href="/citas_medicas/index.php#especialidades" class="nav-link">Especialidades</a>
                <a href="/citas_medicas/index.php#sucursales" class="nav-link">Sucursales</a>
                <a href="/citas_medicas/auth/login.php" class="nav-link nav-login">Iniciar Sesión</a>
                <a href="/citas_medicas/auth/register.php" class="nav-link nav-register">Registrarse</a>
            <?php elseif ($rol === 'admin'): ?>
                <!-- Menú para administradores -->
                <a href="/citas_medicas/admin/dashboard.php" class="nav-link">Dashboard</a>
                <a href="/citas_medicas/admin/usuarios.php" class="nav-link">Usuarios</a>
                <a href="/citas_medicas/admin/veterinarios.php" class="nav-link">Veterinarioes</a>
                <a href="/citas_medicas/admin/especialidades.php" class="nav-link">Especialidades</a>
                <a href="/citas_medicas/admin/citas.php" class="nav-link">Citas</a>
                <div class="user-menu">
                    <span class="user-name"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    <a href="/citas_medicas/auth/logout.php" class="nav-link nav-logout">Cerrar Sesión</a>
                </div>
            <?php elseif ($rol === 'veterinario'): ?>
                <!-- Menú para veterinarios -->
                <a href="/citas_medicas/veterinario/dashboard.php" class="nav-link">Mi Panel</a>
                <a href="/citas_medicas/veterinario/mis_citas.php" class="nav-link">Mis Citas</a>
                <a href="/citas_medicas/veterinario/perfil.php" class="nav-link">Mi Perfil</a>
                <div class="user-menu">
                    <span class="user-name"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    <a href="/citas_medicas/auth/logout.php" class="nav-link nav-logout">Cerrar Sesión</a>
                </div>
            <?php elseif ($rol === 'user'): ?>
                <!-- Menú para usuarios regulares -->
                <a href="/citas_medicas/index.php#especialidades" class="nav-link">Especialidades</a>
                <a href="/citas_medicas/user/mis_citas.php" class="nav-link">Mis Citas</a>
                <a href="/citas_medicas/user/perfil.php" class="nav-link">Mi Perfil</a>
                <div class="user-menu">
                    <span class="user-name"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    <a href="/citas_medicas/auth/logout.php" class="nav-link nav-logout">Cerrar Sesión</a>
                </div>
            <?php endif; ?>
        </nav>

        <!-- Botón hamburguesa para móvil -->
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<style>
    .header-dynamic {
        position: sticky;
        top: 0;
        background: var(--white);
        box-shadow: var(--shadow-sm);
        z-index: 1000;
        transition: var(--transition);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo-container {
        flex-shrink: 0;
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-right: 1rem;
    }

    .btn-nav {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        background: var(--white);
        color: var(--primary);
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .btn-nav:hover {
        background: var(--secondary);
        color: var(--white);
    }

    .btn-nav i {
        font-size: 16px;
        line-height: 1;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: var(--primary);
        font-weight: 700;
        font-size: 1.25rem;
        transition: var(--transition);
    }

    .logo:hover {
        opacity: 0.8;
    }

    .logo-img {
        height: 50px;
        width: auto;
        max-width: 200px;
        object-fit: contain;
    }

    .logo-icon {
        font-size: 1.5rem;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        gap: 2rem;
        flex: 1;
        justify-content: center;
    }

    .nav-link {
        color: var(--text);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
    }

    .nav-link:hover {
        color: var(--secondary);
        background: rgba(30, 144, 255, 0.1);
    }

    .nav-login {
        color: var(--secondary);
        border: 2px solid var(--secondary);
    }

    .nav-register {
        background: var(--secondary);
        color: var(--white);
        border: 2px solid var(--secondary);
    }

    .nav-logout {
        background: var(--error);
        color: var(--white);
        border: 2px solid var(--error);
    }

    .user-menu {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-name {
        font-weight: 600;
        color: var(--primary);
    }

    .hamburger {
        display: none;
        flex-direction: column;
        background: none;
        border: none;
        cursor: pointer;
        gap: 0.5rem;
    }

    .hamburger span {
        width: 25px;
        height: 3px;
        background: var(--primary);
        border-radius: 2px;
        transition: var(--transition);
    }

    @media (max-width: 768px) {
        .hamburger {
            display: flex;
        }

        .nav-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--white);
            flex-direction: column;
            gap: 0;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
        }

        .nav-menu.active {
            max-height: 500px;
            box-shadow: var(--shadow-md);
        }

        .nav-link {
            width: 100%;
            padding: 1rem;
            border-radius: 0;
            border-bottom: 1px solid #eee;
        }
    }
</style>

<script>
    // Lógica del menú hamburguesa
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Lógica del scroll del header
    let lastScrollTop = 0;
    const headerDynamic = document.getElementById('headerDynamic');

    window.addEventListener('scroll', () => {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > 100) {
            headerDynamic.style.boxShadow = 'var(--shadow-md)';
        } else {
            headerDynamic.style.boxShadow = 'var(--shadow-sm)';
        }

        lastScrollTop = scrollTop;
    });
</script>
