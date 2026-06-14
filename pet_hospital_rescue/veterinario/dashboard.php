<?php
/**
 * DASHBOARD DEL DOCTOR - HOSPITAL & HUMAN
 * 
 * Funcionalidad:
 * - Panel principal del veterinario
 * - Resumen de citas del día
 * - Estadísticas generales
 * - Acceso rápido a funciones principales
 * 
 * Seguridad:
 * - Validación de sesión y rol
 * - Solo veterinarios pueden acceder
 */

session_start();

// Validar que el usuario sea veterinario
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'veterinario') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

// Obtener información del veterinario con su especialidad
$id_usuario = $_SESSION['id'];
$stmt = $pdo->prepare("
    SELECT u.*, d.id as veterinario_id, d.id_especialidad, e.nombre as especialidad_nombre, e.descripcion as especialidad_descripcion
    FROM usuarios u
    LEFT JOIN veterinarioes d ON d.id_usuario = u.id
    LEFT JOIN especialidades e ON e.id = d.id_especialidad
    WHERE u.id = ? AND u.rol = 'veterinario'
");
$stmt->execute([$id_usuario]);
$veterinario = $stmt->fetch();

if (!$veterinario) {
    header("Location: ../auth/login.php");
    exit();
}

$id_veterinario = $veterinario['veterinario_id']; // ID correcto de la tabla veterinarios

// Obtener citas de hoy
$hoy = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT c.*, u.nombre as mascota_nombre, e.nombre as especialidad_nombre
    FROM citas c
    JOIN usuarios u ON c.id_usuario = u.id
    JOIN especialidades e ON c.id_especialidad = e.id
    WHERE c.id_veterinario = ? AND DATE(c.fecha) = ?
    ORDER BY c.fecha ASC, c.hora ASC
");
$stmt->execute([$id_veterinario, $hoy]);
$citas_hoy = $stmt->fetchAll();

// Obtener estadísticas
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM citas WHERE id_veterinario = ?");
$stmt->execute([$id_veterinario]);
$total_citas = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as completadas FROM citas WHERE id_veterinario = ? AND estado = 'completada'");
$stmt->execute([$id_veterinario]);
$citas_completadas = $stmt->fetch()['completadas'];

$stmt = $pdo->prepare("SELECT COUNT(*) as pendientes FROM citas WHERE id_veterinario = ? AND estado = 'pendiente'");
$stmt->execute([$id_veterinario]);
$citas_pendientes = $stmt->fetch()['pendientes'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Veterinario - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: var(--text-light);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-card h3 {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .citas-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .citas-section h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 0.5rem;
        }

        .cita-item {
            background: var(--background);
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            border-left: 4px solid var(--secondary);
            transition: var(--transition);
        }

        .cita-item:hover {
            background: #f0f5ff;
        }

        .cita-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .cita-mascota {
            font-weight: 600;
            color: var(--primary);
        }

        .cita-hora {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .cita-especialidad {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .cita-estado {
            margin-top: 0.5rem;
        }

        .estado-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .estado-confirmada {
            background: #d1ecf1;
            color: #0c5460;
        }

        .estado-completada {
            background: #d4edda;
            color: #155724;
        }

        .estado-cancelada {
            background: #f8d7da;
            color: #721c24;
        }

        .cita-acciones {
            margin-top: 0.75rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn-accion {
            padding: 0.375rem 0.75rem;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-confirmar {
            background: #28a745;
            color: white;
        }

        .btn-confirmar:hover {
            background: #218838;
        }

        .btn-cancelar {
            background: #dc3545;
            color: white;
        }

        .btn-cancelar:hover {
            background: #c82333;
        }

        .btn-completar {
            background: #007bff;
            color: white;
        }

        .btn-completar:hover {
            background: #0056b3;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: #000a2e;
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: #0d7acc;
        }

        /* ========================= 
           MODO OSCURO - DASHBOARD
        ========================= */
        
        body.dark .stat-card {
            background: var(--card-dark);
            color: var(--text);
        }

        body.dark .stat-card h3 {
            color: var(--text-light);
        }

        body.dark .stat-card .number {
            color: var(--secondary);
        }

        body.dark .citas-section {
            background: var(--card-dark);
            color: var(--text);
        }

        body.dark .citas-section h2 {
            color: var(--text);
            border-bottom-color: var(--secondary);
        }

        body.dark .cita-item {
            background: #1e293b;
            color: var(--text);
            border-left-color: var(--secondary);
        }

        body.dark .cita-item:hover {
            background: #334155;
        }

        body.dark .cita-Header {
            color: var(--text);
        }

        body.dark .cita-mascota {
            color: var(--secondary);
        }

        body.dark .cita-hora,
        body.dark .cita-especialidad {
            color: var(--text-light);
        }

        body.dark .no-citas {
            color: var(--text);
        }

        body.dark .no-citas p {
            color: var(--text-light);
        }

        body.dark .dashboard-header p:first-of-type {
            color: var(--secondary);
        }

        body.dark .dashboard-header p:last-of-type {
            color: var(--text-light);
        }

        /* Estilos párrafo con estilo inline */
        body.dark p {
            color: var(--text);
        }

        body.dark p[style*="color: var(--secondary)"] {
            color: var(--secondary) !important;
        }
    </style>
</head>
<body>
    <!-- Botón flotante de tema -->
    <?php include_once('../includes/floating_theme_toggle.php'); ?>
    
    <script>
        // Aplicar tema INMEDIATAMENTE antes de renderizar el contenido
        (function() {
            const storedTheme = localStorage.getItem('hnh-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme || (prefersDark ? 'dark' : 'light');
            document.body.classList.add(theme);
        })();
    </script>
    <?php include("../includes/header_dynamic.php"); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>👨‍⚕️ Dr. <?php echo htmlspecialchars($veterinario['nombre']); ?></h1>
            <?php if ($veterinario['especialidad_nombre']): ?>
                <p style="color: var(--secondary); font-weight: 600; font-size: 1.1rem;">Especialidad: <?php echo htmlspecialchars($veterinario['especialidad_nombre']); ?></p>
            <?php else: ?>
                <p style="color: #dc3545; font-style: italic;">⚠️ Sin especialidad asignada</p>
            <?php endif; ?>
            <p style="margin-top: 0.5rem;">Panel de control del veterinario - PET HOSPITAL AND RESCUE</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Citas</h3>
                <div class="number"><?php echo $total_citas; ?></div>
            </div>
            <div class="stat-card">
                <h3>Citas Completadas</h3>
                <div class="number"><?php echo $citas_completadas; ?></div>
            </div>
            <div class="stat-card">
                <h3>Citas Pendientes</h3>
                <div class="number"><?php echo $citas_pendientes; ?></div>
            </div>
        </div>

        <!-- Citas de Hoy -->
        <div class="citas-section">
            <h2>Citas de Hoy (<?php echo date('d/m/Y'); ?>)</h2>
            
            <?php if (empty($citas_hoy)): ?>
                <div class="no-citas">
                    <p>No tienes citas programadas para hoy.</p>
                </div>
            <?php else: ?>
                <?php foreach ($citas_hoy as $cita): ?>
                    <div class="cita-item">
                        <div class="cita-header">
                            <span class="cita-mascota"><?php echo htmlspecialchars($cita['mascota_nombre']); ?></span>
                            <span class="cita-hora"><?php echo date('h:i A', strtotime($cita['hora'])); ?></span>
                        </div>
                        <div class="cita-especialidad">
                            <?php echo htmlspecialchars($cita['especialidad_nombre']); ?>
                        </div>
                        <div class="cita-estado">
                            <span class="estado-badge estado-<?php echo $cita['estado']; ?>">
                                <?php echo ucfirst($cita['estado']); ?>
                            </span>
                        </div>
                        <div class="cita-acciones">
                            <?php if ($cita['estado'] === 'pendiente'): ?>
                                <button onclick="cambiarEstadoCita(<?php echo $cita['id']; ?>, 'confirmada')" class="btn-accion btn-confirmar">Confirmar</button>
                                <button onclick="cambiarEstadoCita(<?php echo $cita['id']; ?>, 'cancelada')" class="btn-accion btn-cancelar">Cancelar</button>
                            <?php elseif ($cita['estado'] === 'confirmada'): ?>
                                <button onclick="cambiarEstadoCita(<?php echo $cita['id']; ?>, 'completada')" class="btn-accion btn-completar">Completar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="action-buttons">
                <a href="mis_citas.php" class="btn btn-primary">Ver Todas mis Citas</a>
                <a href="perfil.php" class="btn btn-secondary">Editar Perfil</a>
            </div>
        </div>
    </div>

    <script>
        function cambiarEstadoCita(idCita, nuevoEstado) {
            if (!confirm(`¿Estás seguro de que quieres ${nuevoEstado === 'confirmada' ? 'confirmar' : nuevoEstado === 'cancelada' ? 'cancelar' : 'completar'} esta cita?`)) {
                return;
            }

            fetch('../admin/ajax/cambiar_estado_cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: idCita,
                    estado: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Estado de la cita actualizado correctamente', 'success');
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarNotificacion('Error al actualizar el estado de la cita', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
        }

        function mostrarNotificacion(mensaje, tipo = 'info', duracion = 3000) {
            const notificacion = document.createElement('div');
            notificacion.className = `notificacion notificacion-${tipo}`;
            notificacion.textContent = mensaje;
            notificacion.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                z-index: 9999;
                animation: slideInRight 0.3s ease-out;
            `;

            const estilos = {
                success: { background: '#d4edda', color: '#155724', border: '1px solid #c3e6cb' },
                error: { background: '#f8d7da', color: '#721c24', border: '1px solid #f5c6cb' },
                warning: { background: '#fff3cd', color: '#856404', border: '1px solid #ffeaa7' },
                info: { background: '#d1ecf1', color: '#0c5460', border: '1px solid #bee5eb' }
            };

            // Ajustar estilos según el tema
            if (document.body.classList.contains('dark')) {
                estilos.success = { background: 'rgba(34, 197, 94, 0.1)', color: '#86efac', border: '1px solid #22c55e' };
                estilos.error = { background: 'rgba(239, 68, 68, 0.1)', color: '#fca5a5', border: '1px solid #ef4444' };
                estilos.warning = { background: 'rgba(234, 179, 8, 0.1)', color: '#fef08a', border: '1px solid #eab308' };
                estilos.info = { background: 'rgba(59, 130, 246, 0.1)', color: '#93c5fd', border: '1px solid #3b82f6' };
            }

            const estilo = estilos[tipo] || estilos.info;
            Object.assign(notificacion.style, estilo);

            document.body.appendChild(notificacion);

            setTimeout(() => {
                notificacion.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notificacion.remove(), 300);
            }, duracion);
        }
    </script>

    <style>
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }
    </style>
    <script src="../assets/js/main.js" defer></script>
</body>
</html>
