<?php
/**
 * MIS CITAS - PANEL DEL USUARIO
 *
 * Funcionalidad:
 * - Muestra todas las citas médicas del usuario autenticado
 * - Lista completa con detalles de cada cita
 * - Información organizada: especialidad, veterinario, fecha, hora, estado
 * - Opción de cancelar citas cuando están en estado 'pendiente'
 * - Vista ordenada por fecha (más recientes primero)
 *
 * Información mostrada por cita:
 * - Especialidad médica consultada
 * - Nombre del veterinario asignado
 * - Fecha de la cita (formato legible)
 * - Hora de la cita
 * - Estado actual: pendiente, confirmada, cancelada
 * - Botón de acción (cancelar si aplica)
 *
 * Estados de cita y acciones permitidas:
 * - pendiente: Puede ser cancelada por el usuario
 * - confirmada: No puede ser modificada por el usuario
 * - cancelada: Mostrada como histórica
 *
 * Funcionalidades disponibles:
 * - Visualización de historial completo de citas
 * - Cancelación de citas pendientes
 * - Navegación de vuelta al dashboard
 * - Interfaz responsive y fácil de usar
 *
 * Consultas realizadas:
 * - JOIN con especialidades para nombre legible
 * - JOIN con veterinarios para nombre del veterinario
 * - Filtro por id_usuario de la sesión
 * - Ordenamiento por fecha y hora descendente
 *
 * Seguridad:
 * - Validación de sesión por ID de usuario
 * - Prepared statements para prevenir SQL injection
 * - Solo el propietario puede ver sus citas
 * - Control de acceso a operaciones de modificación
 */

session_start();
include("../config/db.php");

// Verificar que el usuario esté logueado
if(!isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit;
}

// Obtener todas las citas del usuario actual
// JOIN con tablas relacionadas para mostrar nombres legibles
$stmt = $pdo->prepare("
    SELECT citas.id, citas.fecha, citas.hora, citas.estado,
           especialidades.nombre AS especialidad, veterinarios.nombre AS veterinario
    FROM citas
    JOIN veterinarios ON citas.id_veterinario = veterinarios.id
    JOIN especialidades ON veterinarios.id_especialidad = especialidades.id
    WHERE citas.id_usuario = ?
    ORDER BY citas.fecha DESC, citas.hora DESC
");
$stmt->execute([$_SESSION['id']]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mis Citas - PET HOSPITAL AND RESCUE</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f0f4f8;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #0a1f44;
    margin-bottom: 20px;
}

.table-container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

table th {
    background-color: #0a1f44;
    color: white;
    border-radius: 8px;
}

.status {
    padding: 6px 12px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    text-align: center;
    display: inline-block;
    font-size: 15px;
}

.status.confirmada { background-color: green; }
.status.pendiente { background-color: orange; }
.status.cancelada { background-color: red; }

@media(max-width: 600px){
    table, thead, tbody, th, td, tr { display: block; }
    th { text-align: right; }
    td { text-align: right; padding-left: 50%; position: relative; }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        width: 45%;
        font-weight: bold;
        text-align: left;
    }
}
</style>
</head>
<body style="background: linear-gradient(135deg, #0a1f44 60%, #1e90ff 100%); min-height: 100vh; margin: 0;">
<script>
    // Aplicar tema INMEDIATAMENTE antes de renderizar el contenido
    (function() {
        const storedTheme = localStorage.getItem('hnh-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = storedTheme || (prefersDark ? 'dark' : 'light');
        document.body.classList.add(theme);
    })();
</script>

<?php include('../includes/floating_theme_toggle.php'); ?>

<div style="margin-bottom: 20px; margin-top: 30px; text-align: center;">
    <a href="/citas_medicas/user/dashboard.php" class="btn-back" style="display:inline-block;padding:12px 22px;background:#1e90ff;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;box-shadow:0 2px 12px #1e90ff33;transition:background 0.2s;">&larr; Volver</a>
</div>

<h2 style="text-align:center;color:#fff;margin-bottom:30px;font-size:32px;font-weight:800;letter-spacing:1px;text-shadow:0 2px 12px #0a1f44;">Mis Citas</h2>

<div class="table-container" style="max-width: 950px; margin: auto; background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); padding: 38px 18px; border-radius: 22px; box-shadow: 0 8px 32px #1e90ff22;">
    <?php if(count($citas) > 0): ?>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background: linear-gradient(90deg, #0a1f44 60%, #1e90ff 100%); color: #fff;">
                <th style="padding:14px 8px;font-size:16px;border-radius:8px 8px 0 0;">Especialidad</th>
                <th style="padding:14px 8px;font-size:16px;">Veterinario</th>
                <th style="padding:14px 8px;font-size:16px;">Fecha</th>
                <th style="padding:14px 8px;font-size:16px;">Hora</th>
                <th style="padding:14px 8px;font-size:16px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($citas as $c): ?>
            <tr style="background:rgba(255,255,255,0.85);border-radius:8px;box-shadow:0 2px 8px #1e90ff11;">
                <td data-label="Especialidad" style="padding:12px 8px;font-size:15px;color:#0a1f44;font-weight:600;"> <?php echo htmlspecialchars($c['especialidad']); ?> </td>
                <td data-label="Veterinario" style="padding:12px 8px;font-size:15px;color:#1e90ff;font-weight:600;"> <?php echo htmlspecialchars($c['veterinario']); ?> </td>
                <td data-label="Fecha" style="padding:12px 8px;font-size:15px;"> <?php echo $c['fecha']; ?> </td>
                <td data-label="Hora" style="padding:12px 8px;font-size:15px;"> <?php echo date('h:i A', strtotime($c['hora'])); ?> </td>
                <td data-label="Estado" style="padding:12px 8px;">
                    <?php
                        $estado = strtolower($c['estado']);
                        $badgeColor = $estado === 'confirmada' ? '#28a745' : ($estado === 'pendiente' ? '#ffc107' : '#dc3545');
                        $textColor = $estado === 'pendiente' ? '#222' : '#fff';
                    ?>
                    <span style="padding:7px 18px;border-radius:16px;font-weight:700;font-size:15px;background:<?php echo $badgeColor; ?>;color:<?php echo $textColor; ?>;box-shadow:0 2px 8px #1e90ff22;">
                        <?php echo ucfirst($c['estado']); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align:center;color:#fff;font-size:18px;">No tienes citas agendadas.</p>
    <?php endif; ?>
</div>
<script src="../assets/js/main.js" defer></script>
</body>
</html>