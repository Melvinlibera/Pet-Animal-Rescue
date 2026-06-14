<?php
/* =========================
   PANEL DE USUARIO (PACIENTE)
   - Ver citas
   - Acceso protegido
========================= */

session_start();

/* =========================
   VALIDAR SESIÓN
========================= */
if (!isset($_SESSION['usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

/* =========================
   CONEXIÓN BD (PDO)
========================= */
require_once("../config/db.php");

/* =========================
   OBTENER CITAS DEL USUARIO
========================= */
$stmt = $pdo->prepare("
    SELECT c.*, d.nombre AS veterinario, e.nombre AS especialidad
    FROM citas c
    JOIN veterinarios d ON c.id_veterinario = d.id
    JOIN especialidades e ON c.id_especialidad = e.id
    WHERE c.id_usuario = ?
    ORDER BY c.fecha DESC
");

$stmt->execute([$_SESSION['id']]);
$citas = $stmt->fetchAll();
?>

<?php include("../includes/header.php"); ?>

<div class="section">

    <h2>Mis Citas</h2>

    <?php if (count($citas) > 0): ?>

        <div class="cards">

            <?php foreach ($citas as $cita): ?>

                <div class="container">

                    <h3><?php echo $cita['especialidad']; ?></h3>

                    <p><strong>Veterinario:</strong> <?php echo $cita['veterinario']; ?></p>
                    <p><strong>Fecha:</strong> <?php echo $cita['fecha']; ?></p>
                    <p><strong>Hora:</strong> <?php echo date('h:i A', strtotime($cita['hora'])); ?></p>

                    <!-- BOTÓN ELIMINAR -->
                    <a href="../ajax/eliminar_cita.php?id=<?php echo $cita['id']; ?>" 
                       onclick="return confirm('¿Cancelar cita?')">
                       Cancelar
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <p>No tienes citas registradas.</p>

    <?php endif; ?>

</div>

<script src="../assets/js/main.js" defer></script>
<?php include("../includes/footer.php"); ?>