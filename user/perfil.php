<?php
/**
 * PERFIL DEL USUARIO - HOSPITAL & HUMAN
 * 
 * Funcionalidad:
 * - Visualización y edición del perfil del usuario
 * - Cambio de contraseña
 * - Actualización de información de contacto
 * - Gestión de datos del seguro veterinario
 * 
 * Seguridad:
 * - Validación de sesión y rol
 * - Solo usuarios regulares pueden acceder
 */

session_start();

// Validar que el usuario sea user
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/db.php");

$id_usuario = $_SESSION['id'];
$mensaje = "";
$error = "";

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND rol = 'user'");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: ../auth/login.php");
    exit();
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'actualizar_perfil') {
        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $tipo_seguro = trim($_POST['tipo_seguro'] ?? '');
        $nombre_seguro = trim($_POST['nombre_seguro'] ?? '');

        if (empty($nombre)) {
            $error = "El nombre es requerido";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, telefono = ?, tipo_seguro = ?, nombre_seguro = ? WHERE id = ?");
                $stmt->execute([$nombre, $telefono, $tipo_seguro, $nombre_seguro, $id_usuario]);
                $mensaje = "Perfil actualizado correctamente";
                
                // Actualizar sesión
                $_SESSION['nombre'] = $nombre;
                
                // Recargar datos
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$id_usuario]);
                $usuario = $stmt->fetch();
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
        } elseif (!password_verify($password_actual, $usuario['password'])) {
            $error = "La contraseña actual es incorrecta";
        } elseif ($password_nueva !== $password_confirmar) {
            $error = "Las contraseñas nuevas no coinciden";
        } elseif (strlen($password_nueva) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } else {
            try {
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$password_hash, $id_usuario]);
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

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus {
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
            <h1>👤 Mi Perfil</h1>
            <p>Administra tu información personal y seguridad</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje">✓ <?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" onclick="cambiarTab('info')">Información General</button>
            <button class="tab-btn" onclick="cambiarTab('seguro')">Datos del Seguro</button>
            <button class="tab-btn" onclick="cambiarTab('password')">Cambiar Contraseña</button>
        </div>

        <!-- TAB: Información General -->
        <div id="info" class="tab-content active">
            <div class="info-box">
                <div class="info-item">
                    <div class="info-label">Correo</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['correo']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Rol</div>
                    <div class="info-value">Usuario Regular</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Registro</div>
                    <div class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></div>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="accion" value="actualizar_perfil">
                
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>

        <!-- TAB: Datos del Seguro -->
        <div id="seguro" class="tab-content">
            <form method="POST">
                <input type="hidden" name="accion" value="actualizar_perfil">
                
                <div class="form-group">
                    <label for="tipo_seguro">Tipo de Seguro</label>
                    <select id="tipo_seguro" name="tipo_seguro">
                        <option value="">-- Seleccionar --</option>
                        <option value="privado" <?php echo ($usuario['tipo_seguro'] === 'privado') ? 'selected' : ''; ?>>Privado</option>
                        <option value="eps" <?php echo ($usuario['tipo_seguro'] === 'eps') ? 'selected' : ''; ?>>EPS</option>
                        <option value="otro" <?php echo ($usuario['tipo_seguro'] === 'otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre_seguro">Nombre de la Aseguradora</label>
                    <input type="text" id="nombre_seguro" name="nombre_seguro" value="<?php echo htmlspecialchars($usuario['nombre_seguro'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>

        <!-- TAB: Cambiar Contraseña -->
        <div id="password" class="tab-content">
            <form method="POST">
                <input type="hidden" name="accion" value="cambiar_password">
                
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual</label>
                    <input type="password" id="password_actual" name="password_actual" required>
                </div>

                <div class="form-group">
                    <label for="password_nueva">Contraseña Nueva</label>
                    <input type="password" id="password_nueva" name="password_nueva" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmar">Confirmar Contraseña Nueva</label>
                    <input type="password" id="password_confirmar" name="password_confirmar" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-secondary">Cambiar Contraseña</button>
                </div>
            </form>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        function cambiarTab(tabName) {
            // Ocultar todos los tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Desactivar todos los botones
            const botones = document.querySelectorAll('.tab-btn');
            botones.forEach(btn => btn.classList.remove('active'));

            // Mostrar el tab seleccionado
            document.getElementById(tabName).classList.add('active');

            // Activar el botón correspondiente
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
