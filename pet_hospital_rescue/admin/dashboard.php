<?php
/**
 * DASHBOARD DEL ADMINISTRADOR - HOSPITAL & HUMAN
 *
 * Funcionalidad:
 * - Panel principal del administrador del sistema
 * - Muestra estadísticas generales: usuarios, veterinarios, citas, especialidades
 * - Panel de control de personalizacion de colores
 * - Tarjetas informativas con métricas clave del sistema
 * - Acceso rápido a las funciones de gestión administrativa
 *
 * Características:
 * - Personalización de colores en tiempo real
 * - Guardado de preferencias en localStorage
 * - Diseño glassmorphism moderno
 * - Estadísticas visuales mejoradas
 * - Responsive y accesible
 */

session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
require_once '../config/db.php';

// Obtener estadísticas
$veterinarios = $pdo->query("SELECT COUNT(*) FROM veterinarios")->fetchColumn();
$especialidades = $pdo->query("SELECT COUNT(*) FROM especialidades")->fetchColumn();
$usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$citas = $pdo->query("SELECT COUNT(*) FROM citas")->fetchColumn();
$citas_hoy = $pdo->query("SELECT COUNT(*) FROM citas WHERE DATE(fecha) = CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Admin - PET HOSPITAL AND RESCUE</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
/* Variables de color personalizables */
:root {
    --primary: #0a1f44;
    --secondary: #1e90ff;
    --accent: #00d4ff;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --bg-light: #f8fafc;
    --text-dark: #0a1f44;
    --text-light: #555;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--bg-light) 0%, #e3f0ff 100%);
    color: var(--text-dark);
    transition: background-color 0.3s ease;
    min-height: 100vh;
}

/* ===== SIDEBAR MODERNO ===== */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, var(--primary) 0%, #0f172a 100%);
    color: #fff;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
    box-shadow: 0 8px 32px rgba(10, 31, 68, 0.15);
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 0 20px;
}

.sidebar-header h2 {
    font-size: 24px;
    color: var(--accent);
    font-weight: 800;
    letter-spacing: 1px;
    text-shadow: 0 2px 8px rgba(0, 212, 255, 0.2);
}

.sidebar-header p {
    font-size: 12px;
    color: #b0c4de;
    margin-top: 4px;
}

.sidebar a {
    padding: 14px 20px;
    text-decoration: none;
    color: #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: 0.25s ease;
    margin: 4px 8px;
    border-radius: 10px;
    font-weight: 500;
}

.sidebar a:hover {
    background: rgba(30, 144, 255, 0.15);
    color: var(--accent);
    transform: translateX(4px);
    box-shadow: inset 0 0 12px rgba(30, 144, 255, 0.1);
}

.sidebar a.active {
    background: linear-gradient(90deg, var(--secondary) 0%, var(--accent) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30, 144, 255, 0.25);
}

.sidebar i {
    font-size: 20px;
}

/* ===== MAIN CONTENT ===== */
.main {
    margin-left: 260px;
    padding: 30px;
    min-height: 100vh;
}

.header-admin {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    padding: 20px 28px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(30, 144, 255, 0.08);
    border: 1px solid rgba(30, 144, 255, 0.1);
}

.header-admin h1 {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-dark);
}

.header-admin .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--secondary);
    color: #fff;
    padding: 10px 16px;
    border-radius: 12px;
    font-weight: 600;
}

/* ===== TARJETAS ESTADÍSTICAS ===== */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1.5px solid rgba(30, 144, 255, 0.1);
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(30, 144, 255, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(30, 144, 255, 0.15);
    border-color: rgba(30, 144, 255, 0.3);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(30, 144, 255, 0.2);
}

.stat-info h3 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-dark);
    text-shadow: 0 2px 8px rgba(30, 144, 255, 0.1);
}

/* ===== PANEL DE PERSONALIZACION ===== */
.customization-panel {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1.5px solid rgba(30, 144, 255, 0.1);
}

.customization-panel h2 {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 20px;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.customization-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}

.color-option {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.color-option label {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-dark);
}

.color-input {
    width: 100%;
    height: 45px;
    border: 2px solid rgba(30, 144, 255, 0.2);
    border-radius: 10px;
    cursor: pointer;
    transition: 0.2s;
}

.color-input:hover {
    border-color: var(--secondary);
    box-shadow: 0 0 12px rgba(30, 144, 255, 0.2);
}

.preset-colors {
    display: flex;
    gap: 10px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.preset-btn {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    border: 2px solid #ddd;
    cursor: pointer;
    transition: 0.2s;
    font-size: 12px;
    font-weight: 600;
}

.preset-btn:hover {
    transform: scale(1.1);
    border-color: var(--secondary);
}

.reset-btn {
    background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
    color: #fff;
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(30, 144, 255, 0.2);
}

.reset-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 144, 255, 0.3);
}

/* ===== ENLACES RÁPIDOS ===== */
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.quick-link {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    border: 1.5px solid rgba(30, 144, 255, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    text-align: center;
}

.quick-link:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(30, 144, 255, 0.15);
    border-color: rgba(30, 144, 255, 0.3);
}

.quick-link i {
    font-size: 32px;
    background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.quick-link span {
    font-weight: 600;
    color: var(--text-dark);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .sidebar {
        width: 70px;
        padding: 10px;
    }

    .sidebar h2,
    .sidebar p {
        display: none;
    }

    .sidebar a {
        justify-content: center;
        width: 50px;
        height: 50px;
    }

    .main {
        margin-left: 70px;
        padding: 15px;
    }

    .header-admin {
        flex-direction: column;
        gap: 12px;
    }

    .stats-container {
        grid-template-columns: 1fr 1fr;
    }

    .customization-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .main {
        margin-left: 0;
    }

    .sidebar {
        width: 100%;
        height: auto;
        flex-direction: row;
        overflow-x: auto;
        padding: 10px;
    }

    .sidebar a {
        min-width: 50px;
    }

    .header-admin {
        padding: 15px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>
<!-- Botón flotante de tema -->
<?php include_once('../includes/floating_theme_toggle.php'); ?>

<!-- SIDEBAR MODERNO -->
<div class="sidebar">
    <div class="sidebar-header">
        <h2>ADMIN</h2>
        <p>Panel de Control</p>
    </div>
    <a href="dashboard.php" class="active"><i class='bx bx-home-alt-2'></i> Inicio</a>
    <a href="veterinarios.php"><i class='bx bx-user-md'></i> Veterinarioes</a>
    <a href="especialidades.php"><i class='bx bx-briefcase-medical'></i> Especialidades</a>
    <a href="citas.php"><i class='bx bx-calendar-check'></i> Citas</a>
    <a href="usuarios.php"><i class='bx bx-group'></i> Usuarios</a>
    <a href="../auth/logout.php"><i class='bx bx-log-out'></i> Cerrar sesión</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <!-- HEADER -->
    <div class="header-admin">
        <h1>Panel de Control</h1>
        <div class="user-info">
            <i class='bx bxs-user-circle'></i>
            <?php echo htmlspecialchars($_SESSION['usuario']); ?>
        </div>
    </div>

    <!-- PANEL DE PERSONALIZACION -->
    <div class="customization-panel">
        <h2><i class='bx bx-palette' style="font-size: 24px;"></i> Personalizar Tema</h2>
        <div class="customization-grid">
            <div class="color-option">
                <label>Color Principal</label>
                <input type="color" id="primaryColor" class="color-input" value="#0a1f44">
                <div class="preset-colors">
                    <button class="preset-btn" style="background: #0a1f44;" onclick="setTheme('#0a1f44', '#1e90ff', '#00d4ff', '#f8fafc')">Azul</button>
                    <button class="preset-btn" style="background: #1a1428;" onclick="setTheme('#1a1428', '#c946ef', '#f47fee', '#f8f6fb')">Púrpura</button>
                    <button class="preset-btn" style="background: #0d3e1f;" onclick="setTheme('#0d3e1f', '#10b981', '#34d399', '#f0fdf4')">Verde</button>
                </div>
            </div>
            <div class="color-option">
                <label>Modo</label>
                <select id="themeModeSelector" class="color-input">
                    <option value="light">Claro</option>
                    <option value="dark">Oscuro</option>
                </select>
            </div>
            <div class="color-option">
                <label>Color Secundario</label>
                <input type="color" id="secondaryColor" class="color-input" value="#1e90ff">
            </div>
            <div class="color-option">
                <label>Color Acento</label>
                <input type="color" id="accentColor" class="color-input" value="#00d4ff">
            </div>
            <div class="color-option">
                <label>Fondo Claro</label>
                <input type="color" id="bgLight" class="color-input" value="#f8fafc">
            </div>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
            <button class="reset-btn" onclick="resetTheme()">↺ Restablecer Tema Predeterminado</button>
            <button class="reset-btn" style="background: #07b; border-color: #0ef;" onclick="applyAllThemeSettings()">✔ Aplicar Cambios</button>
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-container">
        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3>Usuarios</h3>
                    <div class="stat-value"><?php echo $usuarios; ?></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="stat-icon">👨‍⚕️</div>
                <div class="stat-info">
                    <h3>Veterinarioes</h3>
                    <div class="stat-value"><?php echo $veterinarios; ?></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="stat-icon">🏥</div>
                <div class="stat-info">
                    <h3>Especialidades</h3>
                    <div class="stat-value"><?php echo $especialidades; ?></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="stat-icon">📅</div>
                <div class="stat-info">
                    <h3>Citas Totales</h3>
                    <div class="stat-value"><?php echo $citas; ?></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="stat-icon">⏰</div>
                <div class="stat-info">
                    <h3>Citas Hoy</h3>
                    <div class="stat-value"><?php echo $citas_hoy; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ENLACES RÁPIDOS -->
    <h3 style="margin: 30px 0 20px 0; color: var(--text-dark); font-size: 20px; font-weight: 800;">Administración Rápida</h3>
    <div class="quick-links">
        <a href="veterinarios.php" class="quick-link">
            <i class='bx bx-user'></i>
            <span>Gestionar Veterinarioes</span>
        </a>
        <a href="especialidades.php" class="quick-link">
            <i class='bx bx-dock-bottom'></i>
            <span>Gestionar Especialidades</span>
        </a>
        <a href="citas.php" class="quick-link">
            <i class='bx bx-calendar-check'></i>
            <span>Gestionar Citas</span>
        </a>
        <a href="usuarios.php" class="quick-link">
            <i class='bx bx-group'></i>
            <span>Gestionar Usuarios</span>
        </a>
    </div>
</div>

<script>
// Personalización de colores con localStorage
const defaultTheme = {
    primary: '#0a1f44',
    secondary: '#1e90ff',
    accent: '#00d4ff',
    bgLight: '#f8fafc'
};

function applyTheme(primary, secondary, accent, bgLight) {
    document.documentElement.style.setProperty('--primary', primary);
    document.documentElement.style.setProperty('--secondary', secondary);
    document.documentElement.style.setProperty('--accent', accent);
    document.documentElement.style.setProperty('--bg-light', bgLight);
    
    localStorage.setItem('theme', JSON.stringify({
        primary, secondary, accent, bgLight
    }));
}

function setTheme(primary, secondary, accent, bgLight) {
    document.getElementById('primaryColor').value = primary;
    document.getElementById('secondaryColor').value = secondary;
    document.getElementById('accentColor').value = accent;
    document.getElementById('bgLight').value = bgLight;
    applyTheme(primary, secondary, accent, bgLight);
}

function applyMode(mode) {
    document.body.classList.remove('light', 'dark');
    document.body.classList.add(mode);
    document.getElementById('themeModeSelector').value = mode;
    localStorage.setItem('hnh-theme', mode);
}

function setMode(mode) {
    document.getElementById('themeModeSelector').value = mode;
    applyMode(mode);
}

function applyAllThemeSettings() {
    const primary = document.getElementById('primaryColor').value;
    const secondary = document.getElementById('secondaryColor').value;
    const accent = document.getElementById('accentColor').value;
    const bgLight = document.getElementById('bgLight').value;
    const mode = document.getElementById('themeModeSelector').value;

    setTheme(primary, secondary, accent, bgLight);
    setMode(mode);

    if (typeof ThemeManager !== 'undefined') {
        // Sincronizar con el botón flotante global
        ThemeManager.setTheme(mode);
    }

    alert('✓ Los cambios fueron aplicados y se guardaron para todas las páginas');
}

function resetTheme() {
    setTheme(defaultTheme.primary, defaultTheme.secondary, defaultTheme.accent, defaultTheme.bgLight);
    setMode('light');
    alert('✓ Tema restablecido al predeterminado');
}

// Cargar tema guardado
window.addEventListener('load', () => {
    const saved = localStorage.getItem('theme');
    if (saved) {
        const theme = JSON.parse(saved);
        setTheme(theme.primary, theme.secondary, theme.accent, theme.bgLight);
    } else {
        setTheme(defaultTheme.primary, defaultTheme.secondary, defaultTheme.accent, defaultTheme.bgLight);
    }

    const mode = localStorage.getItem('hnh-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    setMode(mode);

    document.getElementById('themeModeSelector').addEventListener('change', (e) => {
        setMode(e.target.value);
        if (typeof ThemeManager !== 'undefined') {
            ThemeManager.setTheme(e.target.value);
        }
    });
});

// Listeners para cambios en tiempo real
document.getElementById('primaryColor').addEventListener('change', (e) => {
    applyTheme(e.target.value, 
               document.getElementById('secondaryColor').value, 
               document.getElementById('accentColor').value, 
               document.getElementById('bgLight').value);
});

document.getElementById('secondaryColor').addEventListener('change', (e) => {
    applyTheme(document.getElementById('primaryColor').value, 
               e.target.value, 
               document.getElementById('accentColor').value, 
               document.getElementById('bgLight').value);
});

document.getElementById('accentColor').addEventListener('change', (e) => {
    applyTheme(document.getElementById('primaryColor').value, 
               document.getElementById('secondaryColor').value, 
               e.target.value, 
               document.getElementById('bgLight').value);
});

document.getElementById('bgLight').addEventListener('change', (e) => {
    applyTheme(document.getElementById('primaryColor').value, 
               document.getElementById('secondaryColor').value, 
               document.getElementById('accentColor').value, 
               e.target.value);
});

// Marcar enlace activo
document.querySelectorAll('.sidebar a').forEach(link => {
    if (link.href === window.location.href) {
        link.classList.add('active');
    }
});
</script>

</body>
</html>