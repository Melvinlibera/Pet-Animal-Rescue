<?php
/**
 * PÁGINA DE ESPECIALIDADES - LISTADO COMPLETO
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
        SELECT e.*, COUNT(v.id) as veterinarios_disponibles
        FROM especialidades e
        LEFT JOIN veterinarioes v ON e.id = v.id_especialidad
        GROUP BY e.id
        ORDER BY e.nombre ASC
    ");
    $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $especialidades = [];
}

// Mapeo de iconos por especialidad
$iconos_especialidad = [
    'Cirugía' => '🔪',
    'Medicina' => '💊',
    'Vacunación' => '💉',
    'Dental' => '🦷',
    'Oftalmología' => '👁️',
    'Cardiología' => '❤️',
    'Dermatología' => '🐾',
    'Traumatología' => '🦴',
    'Oncología' => '⚕️',
    'Nutrición' => '🥗',
];

function obtener_icono($nombre) {
    global $iconos_especialidad;
    foreach ($iconos_especialidad as $key => $icon) {
        if (stripos($nombre, $key) !== false) {
            return $icon;
        }
    }
    return '🏥';
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
    <style>
        .especialidades-hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            padding: 150px 20px 100px;
            text-align: center;
            margin-top: 60px;
        }
        
        .especialidades-hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        
        .especialidades-hero p {
            font-size: 1.2rem;
            color: var(--accent-gold);
            margin: 0;
        }
        
        .especialidades-content {
            max-width: 1400px;
            margin: 60px auto;
            padding: 0 20px;
        }
        
        .especialidades-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .especialidad-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 58, 92, 0.1);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            border: 2px solid #f0f0f0;
        }
        
        .especialidad-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 15px 45px rgba(15, 58, 92, 0.2);
            border-color: var(--accent-gold);
        }
        
        .especialidad-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .especialidad-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .especialidad-header h3 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 700;
        }
        
        .especialidad-body {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .especialidad-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        
        .especialidad-stats {
            display: flex;
            justify-content: space-around;
            padding: 15px 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        
        .especialidad-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-accion {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .btn-detalles {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-detalles:hover {
            background: var(--accent-gold);
            color: var(--primary-dark);
        }
        
        .btn-agendar {
            background: var(--accent-gold);
            color: var(--primary-dark);
        }
        
        .btn-agendar:hover {
            background: #e5c05b;
            transform: translateY(-2px);
        }
        
        .no-especialidades {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<header id="header">
    <img src="../assets/img/logo.png" alt="PET HOSPITAL AND RESCUE" class="logo">
    <nav class="nav">
        <a href="../index.php">Inicio</a>
        <a href="index.php" style="color: var(--accent-gold);">Especialidades</a>
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

<section class="especialidades-hero">
    <h1>Nuestras <span style="color: var(--accent-gold);">Especialidades</span></h1>
    <p>Encuentra el servicio especializado que tu mascota necesita</p>
</section>

<section class="especialidades-content">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="color: var(--primary-dark); margin-bottom: 10px;">Servicios Veterinarios Disponibles</h2>
        <p style="color: var(--text-muted); margin: 0;">Contamos con especialistas en todas las áreas de medicina veterinaria</p>
    </div>
    
    <?php if(!empty($especialidades)): ?>
        <div class="especialidades-grid">
            <?php foreach($especialidades as $esp): ?>
                <div class="especialidad-card">
                    <div class="especialidad-header">
                        <div class="especialidad-icon"><?= obtener_icono($esp['nombre']) ?></div>
                        <h3><?= htmlspecialchars($esp['nombre']) ?></h3>
                    </div>
                    
                    <div class="especialidad-body">
                        <p class="especialidad-desc"><?= htmlspecialchars($esp['descripcion']) ?></p>
                        
                        <div class="especialidad-stats">
                            <div class="stat">
                                <div class="stat-number"><?= $esp['veterinarios_disponibles'] ?></div>
                                <div class="stat-label">Especialistas</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Disponible</div>
                            </div>
                        </div>
                        
                        <div class="especialidad-actions">
                            <a href="ver.php?id=<?= $esp['id'] ?>" class="btn-accion btn-detalles">
                                <i class='bx bx-search'></i> Ver Detalles
                            </a>
                            <?php if(isset($_SESSION['usuario']) && $_SESSION['rol'] == 'user'): ?>
                                <a href="../user/agendar.php?especialidad=<?= $esp['id'] ?>" class="btn-accion btn-agendar">
                                    <i class='bx bx-calendar'></i> Agendar
                                </a>
                            <?php elseif(!isset($_SESSION['usuario'])): ?>
                                <a href="../auth/register.php" class="btn-accion btn-agendar">
                                    <i class='bx bx-calendar'></i> Agendar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-especialidades">
            <h3>No hay especialidades disponibles</h3>
            <p>Por favor, intenta más tarde o contáctanos directamente.</p>
        </div>
    <?php endif; ?>
</section>

<footer>
    <div style="max-width: var(--max-width); margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; padding: 0 20px;">
            <div style="text-align: center;">
                <img src="../assets/img/logo.png" alt="Logo" style="height: 70px; filter: brightness(0) invert(1); margin-bottom: 20px;">
                <p style="margin: 0; opacity: 0.9;">Excelencia Médica & Compromiso con el Rescate Animal</p>
            </div>
            <div style="text-align: center;">
                <h4 style="margin-bottom: 15px;">Navegación Rápida</h4>
                <p style="margin: 8px 0;"><a href="../index.php" style="color: var(--accent-gold); text-decoration: none;">Inicio</a></p>
                <p style="margin: 8px 0;"><a href="index.php" style="color: var(--accent-gold); text-decoration: none;">Especialidades</a></p>
                <p style="margin: 8px 0;"><a href="../auth/login.php" style="color: var(--accent-gold); text-decoration: none;">Iniciar Sesión</a></p>
            </div>
            <div style="text-align: center;">
                <h4 style="margin-bottom: 15px;">Contacto</h4>
                <p style="margin: 8px 0;">📞 +1 (809) 123-4567</p>
                <p style="margin: 8px 0;">📧 info@pethospital.com</p>
                <p style="margin: 8px 0;">📍 Avenida Independencia #123</p>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; text-align: center;">
            <p style="margin: 0; opacity: 0.8;">&copy; 2026 PET HOSPITAL AND RESCUE. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

</body>
</html>
