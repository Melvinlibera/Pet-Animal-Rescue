<?php
/**
 * PÁGINA DE ESPECIALIDADES - LISTADO COMPLETO Y MEJORADO
 * 
 * Funcionalidad:
 * - Mostrar todas las especialidades médicas disponibles
 * - Información detallada de cada especialidad
 * - Acceso rápido a agendamiento
 * - Información de veterinarios disponibles por especialidad
 */

session_start();
require_once("../config/db.php");

try {
    // Obtener todas las especialidades con información de veterinarios
    $stmt = $pdo->query("
        SELECT e.id, e.nombre, e.descripcion, COUNT(v.id) as veterinarios_disponibles
        FROM especialidades e
        LEFT JOIN veterinarios v ON e.id = v.id_especialidad
        GROUP BY e.id
        ORDER BY e.nombre ASC
    ");
    $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $especialidades = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestras Especialidades - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<header id="header">
    <img src="../assets/img/logo.png" alt="PET HOSPITAL AND RESCUE" class="logo">
    <nav class="nav">
        <a href="../index.php">Inicio</a>
        <a href="index.php">Especialidades</a>
        <?php if(isset($_SESSION['usuario'])): ?>
            <?php if($_SESSION['rol'] == 'admin'): ?>
                <a href="../admin/dashboard.php">Panel Admin</a>
            <?php elseif($_SESSION['rol'] == 'veterinario'): ?>
                <a href="../veterinario/dashboard.php">Mi Panel</a>
            <?php else: ?>
                <a href="../user/dashboard.php">Mi Panel</a>
            <?php endif; ?>
            <a href="../auth/logout.php" style="background: var(--accent-gold); color: var(--primary-dark);">Salir</a>
        <?php else: ?>
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php" style="background: var(--accent-gold); color: var(--primary-dark);">Registrarse</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero" style="min-height: 400px; padding-top: 150px;">
    <h1>Nuestras <span style="color: var(--accent-gold);">Especialidades</span></h1>
    <h2>Atención especializada para tu mascota</h2>
</section>

<section class="container mt-40">
    <h2 style="text-align: center; color: var(--primary-dark); margin-bottom: 50px;">Servicios Disponibles</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; max-width: var(--max-width); margin: 0 auto;">
        <?php foreach($especialidades as $esp): ?>
            <div style="
                background: white;
                padding: 30px;
                border-radius: var(--radius-md);
                border-left: 5px solid var(--accent-gold);
                box-shadow: var(--shadow-soft);
                transition: var(--transition);
                display: flex;
                flex-direction: column;
            " onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='var(--shadow-strong)';" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-soft)';">
                <h3 style="color: var(--primary-dark); margin-bottom: 12px; font-size: 1.3rem;">
                    <?= htmlspecialchars($esp['nombre']) ?>
                </h3>
                <p style="color: var(--text-muted); margin-bottom: 15px; flex-grow: 1; line-height: 1.6;">
                    <?= htmlspecialchars($esp['descripcion']) ?>
                </p>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                    <i class='bx bxs-user-circle' style="color: var(--accent-gold); font-size: 1.2rem;"></i>
                    <span style="color: var(--accent-gold); font-weight: 600;">
                        <?= $esp['veterinarios_disponibles'] ?> <?= $esp['veterinarios_disponibles'] == 1 ? 'Veterinario' : 'Veterinarios' ?> disponibles
                    </span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="ver.php?id=<?= $esp['id'] ?>" class="btn btn-primary" style="flex: 1; text-align: center;">
                        <i class='bx bx-search-alt'></i> Detalles
                    </a>
                    <?php if(isset($_SESSION['usuario']) && $_SESSION['rol'] == 'user'): ?>
                        <a href="../user/agendar.php?especialidad=<?= $esp['id'] ?>" class="btn" style="flex: 1; text-align: center; background: var(--accent-gold); color: var(--primary-dark);">
                            <i class='bx bx-calendar'></i> Agendar
                        </a>
                    <?php elseif(!isset($_SESSION['usuario'])): ?>
                        <a href="../auth/register.php" class="btn" style="flex: 1; text-align: center; background: var(--accent-gold); color: var(--primary-dark);">
                            <i class='bx bx-calendar'></i> Agendar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if(empty($especialidades)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">No hay especialidades disponibles en este momento.</p>
        </div>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2026 PET HOSPITAL AND RESCUE. Excelencia Médica & Compromiso con el Rescate Animal.</p>
</footer>

</body>
</html>