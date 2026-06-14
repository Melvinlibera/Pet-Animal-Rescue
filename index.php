<?php 
session_start(); 
$nombre_completo = null;
if (isset($_SESSION['id'])) {
    require_once("config/db.php");
    $id_usuario = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $nombre_completo = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PET HOSPITAL AND RESCUE - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<header id="header">
    <img src="assets/img/logo.png" alt="PET HOSPITAL AND RESCUE" class="logo">
    <nav class="nav">
        <a href="index.php">Inicio</a>
        <a href="especialidades/index.php">Especialidades</a>
        <?php if(isset($_SESSION['usuario'])): ?>
            <?php if($_SESSION['rol'] == 'admin'): ?>
                <a href="admin/dashboard.php">Panel Admin</a>
            <?php elseif($_SESSION['rol'] == 'veterinario'): ?>
                <a href="veterinario/dashboard.php">Mi Panel</a>
            <?php else: ?>
                <a href="user/dashboard.php">Mi Panel</a>
            <?php endif; ?>
            <a href="auth/logout.php" style="background: var(--accent-gold); color: var(--primary-dark);">Salir</a>
        <?php else: ?>
            <a href="auth/login.php">Login</a>
            <a href="auth/register.php" style="background: var(--accent-gold); color: var(--primary-dark);">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <h1>PET HOSPITAL <span style="color: var(--accent-gold);">&</span> RESCUE</h1>
    <h2>Excelencia Médica & Compromiso con el Rescate Animal</h2>
    <div style="margin-top: 40px; position: relative; z-index: 10;">
        <?php if(isset($_SESSION['usuario'])): ?>
            <a href="user/agendar.php" class="btn-hero">📅 Agendar Cita</a>
            <a href="user/mis_citas.php" class="btn-hero btn-outline">📋 Mis Citas</a>
        <?php else: ?>
            <a href="auth/register.php" class="btn-hero">🐾 Unirse Ahora</a>
            <a href="auth/login.php" class="btn-hero btn-outline">🔐 Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</section>

<section class="info">
    <div class="info-container">
        <div class="info-card">
            <h3>Nuestra Misión</h3>
            <p>Brindar una segunda oportunidad a los animales en situación de riesgo, combinando el rescate activo con servicios veterinarios de la más alta calidad tecnológica y humana.</p>
        </div>
        <div class="info-card">
            <h3>Hospital 24/7</h3>
            <p>Contamos con especialistas en cirugía, medicina interna y emergencias listos para atender a tu mascota en cualquier momento, con el respaldo de nuestro centro de rescate.</p>
        </div>
        <div class="info-card">
            <h3>Rescate Animal</h3>
            <p>Cada servicio que adquieres apoya directamente nuestro fondo de rescate, permitiéndonos salvar, rehabilitar y encontrar hogares para cientos de animales cada año.</p>
        </div>
    </div>
</section>

<section class="especialidades-modernas">
    <h2 style="text-align: center; color: var(--primary-dark); font-size: 2.5rem; margin-bottom: 10px;">Nuestros Servicios</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 50px;">Atención integral para el bienestar de tu mascota</p>
    
    <div class="services-grid" style="max-width: 1200px; margin: 0 auto;">
        <a href="especialidades/index.php" class="service-item" style="text-decoration: none; color: inherit;">
            <i class='bx bx-plus-medical'></i>
            <h4>Cirugía General</h4>
            <p>Quirófanos equipados con tecnología de punta.</p>
        </a>
        <a href="especialidades/index.php" class="service-item" style="text-decoration: none; color: inherit;">
            <i class='bx bxs-dog'></i>
            <h4>Medicina Canina</h4>
            <p>Especialistas en salud y comportamiento canino.</p>
        </a>
        <a href="especialidades/index.php" class="service-item" style="text-decoration: none; color: inherit;">
            <i class='bx bxs-cat'></i>
            <h4>Cuidado Felino</h4>
            <p>Áreas exclusivas y especialistas en gatos.</p>
        </a>
        <a href="especialidades/index.php" class="service-item" style="text-decoration: none; color: inherit;">
            <i class='bx bx-shield-quarter'></i>
            <h4>Vacunación</h4>
            <p>Planes preventivos para una vida larga y sana.</p>
        </a>
    </div>
    
    <div style="text-align: center; margin-top: 50px;">
        <a href="especialidades/index.php" class="btn-hero" style="background: var(--accent-gold); color: var(--primary-dark); display: inline-block;">
            ✨ Ver Todas las Especialidades
        </a>
    </div>
</section>

<footer>
    <div style="max-width: var(--max-width); margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; padding: 0 20px;">
            <div style="text-align: center;">
                <img src="assets/img/logo.png" alt="Logo" style="height: 70px; filter: brightness(0) invert(1); margin-bottom: 20px;">
                <p style="margin: 0; opacity: 0.9;">Excelencia Médica & Compromiso con el Rescate Animal</p>
            </div>
            <div style="text-align: center;">
                <h4 style="margin-bottom: 15px;">Navegación Rápida</h4>
                <p style="margin: 8px 0;"><a href="index.php" style="color: var(--accent-gold); text-decoration: none;">Inicio</a></p>
                <p style="margin: 8px 0;"><a href="especialidades/index.php" style="color: var(--accent-gold); text-decoration: none;">Especialidades</a></p>
                <p style="margin: 8px 0;"><a href="auth/login.php" style="color: var(--accent-gold); text-decoration: none;">Iniciar Sesión</a></p>
            </div>
            <div style="text-align: center;">
                <h4 style="margin-bottom: 15px;">Contacto</h4>
                <p style="margin: 8px 0;">📞 +1 (809) 123-4567</p>
                <p style="margin: 8px 0;">📧 info@pethospital.com</p>
                <p style="margin: 8px 0;">📍 Avenida Independencia #123</p>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; text-align: center;">
            <p style="margin: 0; opacity: 0.8;">&copy; 2026 PET HOSPITAL AND RESCUE. Todos los derechos reservados.</p>
            <p style="margin: 10px 0 0 0; opacity: 0.7; font-size: 0.9rem;">Diseñado con <span style="color: var(--accent-gold);">❤</span> para el cuidado de tu mascota</p>
        </div>
    </div>
</footer>

</body>
</html>
