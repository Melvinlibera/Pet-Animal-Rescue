<?php
/**
 * PÁGINA DE AGENDAMIENTO DE CITA
 * 
 * Funcionalidad:
 * - Formulario para agendar citas médicas
 * - Selección de especialidad, veterinario, fecha y hora
 * - Validación de disponibilidad
 * - Confirmación de agendamiento
 * - Integración con base de datos
 * 
 * Seguridad:
 * - Verificación de sesión
 * - Validación de entrada
 * - Prepared statements
 * - Prevención de duplicados
 */

session_start();
require_once("../config/db.php");

// Verifica login
if(!isset($_SESSION['usuario'])){
    header("Location: ../auth/login.php");
    exit();
}

$success = "";
$error = "";
$veterinario_preseleccionado = isset($_GET['veterinario']) ? intval($_GET['veterinario']) : null;
$especialidad_preseleccionada = isset($_GET['especialidad']) ? intval($_GET['especialidad']) : null;

// Guardar cita si se envía el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $fecha = trim($_POST['fecha'] ?? '');
    $id_especialidad = intval($_POST['especialidad'] ?? 0);
    $id_veterinario = intval($_POST['veterinario'] ?? 0);
    $hora = trim($_POST['hora'] ?? '');

    // Validaciones
    if(!$fecha || !$id_especialidad || !$id_veterinario || !$hora){
        $error = "Todos los campos son obligatorios";
    } elseif(strtotime($fecha) < strtotime(date('Y-m-d'))){
        $error = "No se puede agendar una fecha pasada";
    } else {
        // Revisar si ya hay cita del mismo veterinario en la misma fecha y hora
        $stmt = $pdo->prepare("SELECT id FROM citas WHERE id_veterinario=? AND fecha=? AND hora=? AND estado != 'cancelada'");
        $stmt->execute([$id_veterinario, $fecha, $hora]);
        if($stmt->fetch()){
            $error = "Ese horario ya está ocupado. Por favor, selecciona otro.";
        } else {
            // Insertar cita
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO citas (id_usuario, id_especialidad, id_veterinario, fecha, hora, estado, fecha_creacion)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $_SESSION['id'],
                    $id_especialidad,
                    $id_veterinario,
                    $fecha,
                    $hora,
                    'pendiente'
                ]);
                $success = "✓ Cita agendada correctamente. Te contactaremos pronto para confirmar.";
            } catch(PDOException $e) {
                $error = "Error al agendar la cita. Por favor, intenta nuevamente.";
            }
        }
    }
}

// Obtener especialidades
try {
    $stmt = $pdo->query("SELECT id, nombre FROM especialidades ORDER BY nombre ASC");
    $especialidades = $stmt->fetchAll();
} catch(PDOException $e) {
    $especialidades = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* =========================
           CONTENEDOR DE AGENDAMIENTO
        ========================= */
        .agendar-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0a1f44 60%, #1e90ff 100%);
            padding: 20px;
            margin-top: 0;
        }

        /* =========================
           CAJA DE AGENDAMIENTO
        ========================= */
        .agendar-box {
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px #1e90ff22;
            border-radius: 28px;
            max-width: 480px;
            width: 100%;
            padding: 44px 28px;
        }

        .agendar-box h2 {
            margin-bottom: 10px;
            color: #0a1f44;
            font-size: 32px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 1px;
            text-shadow: 0 2px 12px #0a1f44;
        }

        .agendar-box .subtitle {
            text-align: center;
            color: #1e90ff;
            font-size: 16px;
            margin-bottom: 28px;
            font-weight: 500;
        }

        /* =========================
           CAMPOS DEL FORMULARIO
        ========================= */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: var(--radius-sm);
            border: 1px solid #ddd;
            font-size: 14px;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        /* =========================
           MENSAJES
        ========================= */
        .error {
            color: #dc3545;
            background: #fff0f3;
            border: 1.5px solid #dc3545;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 600;
            animation: slideDown 0.3s ease-out;
            text-align:center;
        }

        .success {
            color: #28a745;
            background: #e6f9ed;
            border: 1.5px solid #28a745;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 600;
            animation: slideDown 0.3s ease-out;
            text-align:center;
        }

        /* =========================
           BOTÓN DE ENVÍO
        ========================= */
        .agendar-box button {
            width: 100%;
            padding: 14px;
            background: #1e90ff;
            color: #fff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 2px 12px #1e90ff33;
        }

        .agendar-box button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .agendar-box button:active {
            transform: translateY(0);
        }

        /* =========================
           ENLACE DE VOLVER
        ========================= */
        .volver-link {
            margin-top: 28px;
            text-align: center;
            font-size: 15px;
        }

        .volver-link a {
            color: #1e90ff;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .volver-link a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* =========================
           ANIMACIONES
        ========================= */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 480px) {
            .agendar-box {
                padding: 30px 20px;
            }

            .agendar-box h2 {
                font-size: 24px;
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

<div class="agendar-container" style="min-height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(135deg, #0a1f44 60%, #1e90ff 100%); padding: 20px; margin-top: 0;">
    <div class="agendar-box" style="background: rgba(255,255,255,0.18); backdrop-filter: blur(10px); box-shadow: 0 8px 32px #1e90ff22; border-radius: 28px; max-width: 480px; width: 100%; padding: 44px 28px;">
        <h2 style="margin-bottom: 10px; color: #0a1f44; font-size: 32px; font-weight: 800; text-align: center; letter-spacing: 1px; text-shadow: 0 2px 12px #0a1f44;">📅 Agendar Cita</h2>
        <p class="subtitle" style="text-align: center; color: #1e90ff; font-size: 16px; margin-bottom: 28px; font-weight: 500;">Selecciona los detalles de tu cita médica</p>
        <?php if($error): ?>
            <div class="error" style="color: #dc3545; background: #fff0f3; border: 1.5px solid #dc3545; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 15px; font-weight: 600; animation: slideDown 0.3s ease-out; text-align:center;">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success" style="color: #28a745; background: #e6f9ed; border: 1.5px solid #28a745; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 15px; font-weight: 600; animation: slideDown 0.3s ease-out; text-align:center;">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" onsubmit="return validarAgendamiento()">
            <!-- Especialidad -->
            <div class="form-group">
                <label for="especialidad">Especialidad *</label>
                <select name="especialidad" id="especialidad" required style="background:rgba(255,255,255,0.7);border:1.5px solid #1e90ff;">
                    <option value="">Selecciona una especialidad</option>
                    <?php foreach($especialidades as $esp): ?>
                        <option value="<?php echo $esp['id']; ?>" <?php echo ($especialidad_preseleccionada == $esp['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($esp['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Veterinario -->
            <div class="form-group">
                <label for="veterinario">Veterinario Especialista *</label>
                <select name="veterinario" id="veterinario" required style="background:rgba(255,255,255,0.7);border:1.5px solid #1e90ff;">
                    <option value="">Selecciona un veterinario</option>
                </select>
            </div>
            <!-- Fecha -->
            <div class="form-group">
                <label for="fecha">Fecha de la Cita *</label>
                <input type="date" name="fecha" id="fecha" required style="background:rgba(255,255,255,0.7);border:1.5px solid #1e90ff;">
            </div>
            <!-- Hora -->
            <div class="form-group">
                <label for="hora">Hora de la Cita *</label>
                <input type="time" name="hora" id="hora" required style="background:rgba(255,255,255,0.7);border:1.5px solid #1e90ff;">
            </div>
            <!-- Botón de envío -->
            <button type="submit" style="width:100%;padding:14px;background:#1e90ff;color:#fff;border:none;border-radius:12px;cursor:pointer;margin-top:10px;transition:background 0.2s;font-size:16px;font-weight:700;text-transform:uppercase;box-shadow:0 2px 12px #1e90ff33;">Agendar Cita</button>
        </form>
        <!-- Volver -->
        <div class="volver-link" style="margin-top: 28px; text-align: center; font-size: 15px;">
            <a href="/citas_medicas/user/dashboard.php" style="color: #1e90ff; text-decoration: none; font-weight: 700; transition: color 0.2s;">← Volver al inicio</a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="../assets/js/validaciones.js" defer></script>

<script>
/**
 * Cargar veterinarios según la especialidad seleccionada
 */
document.getElementById('especialidad').addEventListener('change', function(){
    var espId = this.value;
    var veterinarioSelect = document.getElementById('veterinario');
    
    if(!espId) {
        veterinarioSelect.innerHTML = "<option value=''>Selecciona un veterinario</option>";
        return;
    }

    veterinarioSelect.innerHTML = "<option value=''>Cargando veterinarios...</option>";

    axios.post('../user/get_veterinarios.php', { id_especialidad: espId })
    .then(function(res){
        var docs = res.data;
        veterinarioSelect.innerHTML = "<option value=''>Selecciona un veterinario</option>";
        
        if(docs.length === 0) {
            veterinarioSelect.innerHTML = "<option value=''>No hay veterinarios disponibles</option>";
            return;
        }

        docs.forEach(d => {
            var opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.nombre;
            <?php if($veterinario_preseleccionado): ?>
                if(d.id == <?php echo $veterinario_preseleccionado; ?>) {
                    opt.selected = true;
                }
            <?php endif; ?>
            veterinarioSelect.appendChild(opt);
        });
    })
    .catch(err=>{
        console.error(err);
        veterinarioSelect.innerHTML = "<option value=''>Error al cargar veterinarios</option>";
    });
});

// Cargar veterinarios si hay especialidad preseleccionada
<?php if($especialidad_preseleccionada): ?>
document.getElementById('especialidad').value = <?php echo $especialidad_preseleccionada; ?>;
document.getElementById('especialidad').dispatchEvent(new Event('change'));
<?php endif; ?>

// Establecer fecha mínima a hoy
document.getElementById('fecha').min = new Date().toISOString().split('T')[0];
</script>
<script src="../assets/js/main.js" defer></script>

</body>
</html>
