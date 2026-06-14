<?php
/**
 * PERFIL DEL DOCTOR - HOSPITAL & HUMAN
 * 
 * Funcionalidad:
 * - Visualización y edición del perfil del veterinario
 * - Cambio de contraseña
 * - Actualización de información de contacto
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

$id_veterinario = $_SESSION['id'];
$mensaje = "";
$error = "";

// Obtener información del veterinario con su especialidad
$stmt = $pdo->prepare("
    SELECT u.*, d.id_especialidad, e.nombre as especialidad_nombre
    FROM usuarios u
    LEFT JOIN veterinarioes d ON d.id_usuario = u.id
    LEFT JOIN especialidades e ON e.id = d.id_especialidad
    WHERE u.id = ? AND u.rol = 'veterinario'
");
$stmt->execute([$id_veterinario]);
$veterinario = $stmt->fetch();

if (!$veterinario) {
    header("Location: ../auth/login.php");
    exit();
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'actualizar_perfil') {
        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');

        if (empty($nombre)) {
            $error = "El nombre es requerido";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, telefono = ?, cedula = ? WHERE id = ?");
                $stmt->execute([$nombre, $telefono, $cedula, $id_veterinario]);
                $mensaje = "Perfil actualizado correctamente";
                
                // Actualizar sesión
                $_SESSION['nombre'] = $nombre;
                
                // Recargar datos
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$id_veterinario]);
                $veterinario = $stmt->fetch();
            } catch (Exception $e) {
                $error = "Error al actualizar el perfil";
            }
        }
    } elseif ($accion === 'cambiar_password') {
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';

        if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
            $error = "Todos los campos de contraseña son requeridos";
        } elseif (!password_verify($password_actual, $veterinario['password'])) {
            $error = "La contraseña actual es incorrecta";
        } elseif ($password_nueva !== $password_confirmar) {
            $error = "Las contraseñas nuevas no coinciden";
        } elseif (strlen($password_nueva) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } else {
            try {
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$password_hash, $id_veterinario]);
                $mensaje = "Contraseña cambiada correctamente";
            } catch (Exception $e) {
                $error = "Error al cambiar la contraseña";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header-section {
            margin-bottom: 2rem;
        }

        .header-section h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #eee;
        }

        .tab-btn {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }

        .tab-btn.active {
            color: var(--secondary);
            border-bottom-color: var(--secondary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .form-actions {
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

        .mensaje {
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-box {
            background: var(--background);
            padding: 1.5rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--secondary);
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text);
        }
    </style>
</head>
<body>
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
    <script src="../assets/js/main.js" defer></script>

    <div class="container">
        <div class="header-section">
            <h1>Mi Perfil</h1>
            <p>Gestiona tu información personal y seguridad</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="cambiarTab('informacion')">Información Personal</button>
            <button class="tab-btn" onclick="cambiarTab('seguridad')">Seguridad</button>
        </div>

        <!-- Tab: Información Personal -->
        <div id="informacion" class="tab-content active">
            <div class="info-box">
                <div class="info-item">
                    <div class="info-label">Correo Electrónico:</div>
                    <div class="info-value"><?php echo htmlspecialchars($veterinario['correo']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Rol:</div>
                    <div class="info-value">👨‍⚕️ Veterinario</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Especialidad:</div>
                    <div class="info-value" style="font-weight: 600; color: var(--secondary);">
                        <?php echo $veterinario['especialidad_nombre'] ? htmlspecialchars($veterinario['especialidad_nombre']) : '<span style="color: #dc3545; font-style: italic;">Sin especialidad asignada</span>'; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Registro:</div>
                    <div class="info-value"><?php echo date('d/m/Y', strtotime($veterinario['fecha_registro'])); ?></div>
                </div>
            </div>

            <div class="info-box">
            <div class="info-item"><span class="info-label">Nombre Completo:</span> <span class="info-value"><?php echo htmlspecialchars($veterinario['nombre']); ?></span></div>
            <div class="info-item"><span class="info-label">Cédula:</span> <span class="info-value"><?php echo htmlspecialchars($veterinario['cedula'] ?? 'No disponible'); ?></span></div>
            <div class="info-item"><span class="info-label">Teléfono:</span> <span class="info-value"><?php echo htmlspecialchars($veterinario['telefono'] ?? 'No disponible'); ?></span></div>
            <div class="info-item"><span class="info-label">Correo:</span> <span class="info-value"><?php echo htmlspecialchars($veterinario['correo']); ?></span></div>
            <div class="info-item"><span class="info-label">Especialidad:</span> <span class="info-value"><?php echo $veterinario['especialidad_nombre'] ? htmlspecialchars($veterinario['especialidad_nombre']) : '<span style="color: #dc3545; font-style: italic;">Sin especialidad asignada</span>'; ?></span></div>
        </div>
        </div>

        <!-- Tab: Seguridad -->
        <div id="seguridad" class="tab-content">
            <div class="info-box">
                <p>Su perfil solo puede visualizarse aquí. Para cambiar contraseña o datos, contacte con el administrador.</p>
            </div>
        </div>
    </div>

    <script>
        function cambiarTab(tab) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            // Mostrar el tab seleccionado
            document.getElementById(tab).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
