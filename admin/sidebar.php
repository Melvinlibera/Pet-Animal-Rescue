<?php
/**
 * BARRA LATERAL (SIDEBAR) - PANEL ADMINISTRATIVO
 *
 * Funcionalidad:
 * - Componente de navegación lateral para panel de administración
 * - Menú fijo con acceso rápido a todas las funciones administrativas
 * - Diseño compacto y profesional con iconos descriptivos
 * - Navegación intuitiva hacia módulos de gestión
 *
 * Secciones del menú:
 * - 🏠 Dashboard: Panel principal con estadísticas
 * - 👨‍⚕️ Veterinarioes: Gestión de veterinarios y especialidades
 * - 💉 Especialidades: Administración de servicios veterinarios
 * - 📋 Citas: Control de citas médicas agendadas
 * - 👤 Usuarios: Gestión de usuarios del sistema
 * - 🚪 Cerrar sesión: Terminación de sesión administrativa
 *
 * Características técnicas:
 * - Posicionamiento fijo (fixed) en lado izquierdo
 * - Ancho fijo de 220px para consistencia
 * - Altura completa de la ventana (100%)
 * - Scroll interno si contenido excede altura
 *
 * Estilos CSS incluidos:
 * - Colores corporativos (#0a1f44 azul oscuro)
 * - Efectos hover en enlaces
 * - Tipografía clara y legible
 * - Diseño responsive (se oculta en móviles si es necesario)
 *
 * Inclusión en páginas admin:
 * - <?php include('sidebar.php'); ?>
 * - Debe incluirse dentro del <body> antes del contenido principal
 * - Requiere que las páginas estén en el directorio admin/
 *
 * Archivos relacionados:
 * - Todas las páginas del directorio admin/
 * - assets/css/style.css (estilos base)
 * - auth/logout.php (cierre de sesión)
 *
 * Seguridad:
 * - Solo accesible desde páginas con validación de admin
 * - No incluye validación propia (delega a páginas padre)
 */
?>

<!-- admin/sidebar.php -->
<div class="sidebar">
    <h2>Panel Admin</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="veterinarios.php">👨‍⚕️ Veterinarioes</a></li>
        <li><a href="especialidades.php">💉 Especialidades</a></li>
        <li><a href="citas.php">📋 Citas</a></li>
        <li><a href="usuarios.php">👤 Usuarios</a></li>
        <li><a href="../auth/logout.php">🚪 Cerrar sesión</a></li>
    </ul>
</div>

<style>
.sidebar {
    width: 220px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background: #0a1f44;
    padding: 20px;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    border-radius: 0 16px 16px 0;
}
.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}
.sidebar ul {
    list-style: none;
    padding: 0;
}
.sidebar ul li {
    margin: 15px 0;
}
.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    border-radius: 8px;
    transition: 0.3s;
}
.sidebar ul li a:hover {
    background: #1e90ff;
}
</style>

<?php
// Para mantener el sidebar limpio, el toggle de tema debería agregarse
// desde la página principal (dashboard, especialidades, etc.) y no aquí.
// include_once('../includes/floating_theme_toggle.php');
?>