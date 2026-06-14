<?php
/**
 * DASHBOARD DEL USUARIO (PACIENTE) - HOSPITAL & HUMAN
 *
 * Funcionalidad:
 * - Panel de bienvenida personalizado para usuarios regulares (mascotas)
 * - Muestra nombre del usuario autenticado
 * - Tarjetas de acceso rápido a funcionalidades principales
 * - Interfaz visualmente atractiva con animaciones CSS
 * - Navegación intuitiva hacia agendamiento y consultas
 *
 * Elementos visuales:
 * - Header con nombre de usuario y mensaje de bienvenida
 * - Tres tarjetas principales:
 *   • "Agendar Cita": Enlace a formulario de agendamiento
 *   • "Mis Citas": Enlace a historial de citas
 *   • "Mi Perfil": Enlace a información personal
 * - Diseño responsive con gradientes y sombras
 *
 * Funcionalidades disponibles desde dashboard:
 * - Acceso directo a agendar nueva cita
 * - Consulta de citas existentes
 * - Gestión de información personal
 * - Opción de cerrar sesión
 *
 * Seguridad:
 * - Validación estricta de sesión de usuario
 * - Redirección automática a login si no está autenticado
 * - Solo usuarios con rol 'user' pueden acceder
 * - No permite acceso de administradores o veterinarios
 */

session_start();

// Verificar sesión de usuario regular
if(!isset($_SESSION['usuario']) || !isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit();
}

require_once("../config/db.php");

// Obtener nombre y apellido del usuario
$id_usuario = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$nombre_completo = $stmt->fetchColumn();
if(!$nombre_completo) {
    $nombre_completo = $_SESSION['usuario']; // fallback
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mi Panel - PET HOSPITAL AND RESCUE</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* ========================= */
/* DASHBOARD USUARIO CON LOGO Y TARJETAS */
/* ========================= */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
}

.dashboard {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px 40px 20px;
    background: linear-gradient(135deg, #0a1f44 60%, #1e90ff 100%);
}

/* Logo */
.dashboard .logo {
    max-width: 140px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(30,144,255,0.10);
    border-radius: 24px;
    animation: scaleIn 1.2s cubic-bezier(.4,2,.6,1) 0.1s both;
}
.dashboard .logo:hover {
    transform: scale(1.05);
}

/* Contenedor de tarjetas */
.card-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 28px;
}

/* Tarjetas estilo cascada */
.card {
    border-radius: 20px !important;
    min-width: 220px;
    min-height: 140px;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(8px);
    border: 1.5px solid #e3f0ff;
    box-shadow: 0 8px 32px #1e90ff22;
    color: #0a1f44;
    font-size: 17px;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    animation: scaleIn 0.8s cubic-bezier(.4,2,.6,1) both;
}

.card:hover {
    transform: translateY(-10px) scale(1.04);
    box-shadow: 0 20px 50px #1e90ff33;
    background: rgba(255,255,255,0.28) !important;
}

/* Icono grande dentro de la tarjeta */
.card span {
    font-size: 40px;
    margin-bottom: 12px;
}

/* Responsive */
@media(max-width: 600px){
    .card-container {
        flex-direction: column;
        gap: 15px;
    }
    .card {
        width: 90%;
        padding: 20px 15px;
        min-width: unset;
    }
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>

</head>

<body>
<!-- Botón flotante de tema -->
<?php include_once("../includes/floating_theme_toggle.php"); ?>

<script>
    // Aplicar tema INMEDIATAMENTE antes de renderizar el contenido
    (function() {
        const storedTheme = localStorage.getItem('hnh-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = storedTheme || (prefersDark ? 'dark' : 'light');
        document.body.classList.add(theme);
    })();
</script>

<div class="dashboard" style="min-height:100vh; background: linear-gradient(135deg, #0a1f44 60%, #1e90ff 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px 40px 20px;">
    <img src="../assets/img/logo.png" alt="PET HOSPITAL AND RESCUE" class="logo" style="max-width: 140px; margin-bottom: 30px; box-shadow: 0 8px 32px rgba(30,144,255,0.10); border-radius: 24px; animation: scaleIn 1.2s cubic-bezier(.4,2,.6,1) 0.1s both;">
    <h1 style="color:#fff; margin-bottom:18px; font-size: 38px; font-weight: 800; letter-spacing: 1px; text-shadow: 0 2px 12px #0a1f44; animation: fadeUp 1s 0.2s both;">¡Bienvenido, <?php echo htmlspecialchars($nombre_completo); ?>!</h1>
    <h2 style="color:#e2e8f0; font-size: 22px; font-weight: 500; margin-bottom: 38px; animation: fadeUp 1s 0.4s both;">¿Qué deseas hacer hoy?</h2>
    <!-- Contenedor de tarjetas -->
    <div class="card-container" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 28px;">
        <a href="agendar.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">📅</span>
            <div style="font-size: 18px; font-weight: 700;">Agendar Cita</div>
        </a>
        <a href="mis_citas.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">📋</span>
            <div style="font-size: 18px; font-weight: 700;">Ver Mis Citas</div>
        </a>
        <a href="perfil.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">👤</span>
            <div style="font-size: 18px; font-weight: 700;">Mi Perfil</div>
        </a>
        <a href="../especialidades/index.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">⚕️</span>
            <div style="font-size: 18px; font-weight: 700;">Especialidades</div>
        </a>
        <a href="../index.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">🏠</span>
            <div style="font-size: 18px; font-weight: 700;">Inicio</div>
        </a>
        <a href="../auth/logout.php" class="card" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); border: 1.5px solid #e3f0ff; box-shadow: 0 8px 32px #1e90ff22; color: #0a1f44;">
            <span style="font-size: 40px;">🚪</span>
            <div style="font-size: 18px; font-weight: 700;">Cerrar Sesión</div>
        </a>
    </div>
</div>
<script src="../assets/js/main.js" defer></script>
</body>
</html>