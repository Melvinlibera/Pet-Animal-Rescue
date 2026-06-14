<?php
/**
 * PÁGINA DE DETALLES DE ESPECIALIDAD
 * 
 * Funcionalidad:
 * - Muestra información detallada de una especialidad
 * - Lista veterinarios disponibles con botón de agendamiento
 * - Información de precios
 * - Sistema de agendamiento rápido
 */

session_start();
require_once("../config/db.php");

// Validar ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener especialidad
try {
    $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = ?");
    $stmt->execute([$id]);
    $especialidad = $stmt->fetch();
    
    if(!$especialidad){
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    header("Location: index.php");
    exit();
}

// Obtener veterinarios de la especialidad
try {
    $stmt = $pdo->prepare("
        SELECT v.*, u.nombre as nombre_veterinario, u.correo, u.telefono
        FROM veterinarioes v
        JOIN usuarios u ON v.id_usuario = u.id
        WHERE v.id_especialidad = ?
        ORDER BY u.nombre ASC
    ");
    $stmt->execute([$id]);
    $veterinarios = $stmt->fetchAll();
} catch(PDOException $e) {
    $veterinarios = [];
}

// Mapeo de iconos
$iconos = [
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
    global $iconos;
    foreach ($iconos as $key => $icon) {
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
    <title><?= htmlspecialchars($especialidad['nombre']) ?> - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .detalle-hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 60px;
        }
        
        .detalle-hero h1 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 10px;
            margin-top: 0;
        }
        
        .detalle-icon-grande {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .detalle-content {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }
        
        .info-seccion {
            background: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(15, 58, 92, 0.1);
        }
        
        .info-seccion h2 {
            color: var(--primary-dark);
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 3px solid var(--accent-gold);
            padding-bottom: 15px;
        }
        
        .descripcion {
            color: var(--text-muted);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        .precios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .precio-card {
            background: linear-gradient(135deg, #f0f4f8 0%, #e3f0ff 100%);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--accent-gold);
        }
        
        .precio-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .precio-monto {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--primary-dark);
        }
        
        .precio-moneda {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        .descuento-badge {
            background: var(--accent-gold);
            color: var(--primary-dark);
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 10px;
            display: inline-block;
        }
        
        .veterinarios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .veterinario-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(15, 58, 92, 0.1);
            border-top: 5px solid var(--primary-dark);
            transition: var(--transition);
            text-align: center;
        }
        
        .veterinario-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(15, 58, 92, 0.2);
        }
        
        .veterinario-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 15px;
        }
        
        .veterinario-nombre {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 10px;
        }
        
        .veterinario-especialidad {
            background: var(--accent-gold);
            color: var(--primary-dark);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .veterinario-contacto {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .veterinario-contacto a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
        }
        
        .veterinario-contacto a:hover {
            color: var(--accent-gold);
        }
        
        .btn-agendar-vet {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        
        .btn-agendar-vet:hover {
            background: linear-gradient(135deg, var(--accent-gold) 0%, #e5c05b 100%);
            color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .no-veterinarios {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        
        .btn-volver {
            display: inline-block;
            margin-bottom: 40px;
            padding: 10px 20px;
            background: var(--primary-dark);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .btn-volver:hover {
            background: var(--accent-gold);
            color: var(--primary-dark);
        }
    </style>
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

<section class="detalle-hero">
    <div class="detalle-icon-grande"><?= obtener_icono($especialidad['nombre']) ?></div>
    <h1><?= htmlspecialchars($especialidad['nombre']) ?></h1>
    <p style="font-size: 1.1rem; margin: 0; opacity: 0.9;">Especialidad Médica de Excelencia</p>
</section>

<section class="detalle-content">
    <a href="index.php" class="btn-volver">← Volver a Especialidades</a>
    
    <!-- Información General -->
    <div class="info-seccion">
        <h2>📋 Información General</h2>
        <p class="descripcion"><?= htmlspecialchars($especialidad['descripcion']) ?></p>
    </div>
    
    <!-- Información de Precios -->
    <div class="info-seccion">
        <h2>💰 Información de Precios</h2>
        <div class="precios-grid">
            <div class="precio-card">
                <div class="precio-label">Sin Seguro Veterinario</div>
                <div class="precio-monto"><?= number_format($especialidad['precio'], 2) ?></div>
                <div class="precio-moneda">RD$ (Pesos Dominicanos)</div>
                <div class="descuento-badge">Precio Regular</div>
            </div>
            <div class="precio-card">
                <div class="precio-label">Con Seguro Veterinario</div>
                <div class="precio-monto"><?= number_format($especialidad['precio'] * 0.25, 2) ?></div>
                <div class="precio-moneda">RD$ (Pesos Dominicanos)</div>
                <div class="descuento-badge">Ahorra RD$<?= number_format($especialidad['precio'] * 0.75, 2) ?> (75%)</div>
            </div>
        </div>
    </div>
    
    <!-- Veterinarios Disponibles -->
    <div class="info-seccion">
        <h2>👨‍⚕️ Veterinarios Especialistas</h2>
        
        <?php if(!empty($veterinarios)): ?>
            <div class="veterinarios-grid">
                <?php foreach($veterinarios as $vet): ?>
                    <div class="veterinario-card">
                        <div class="veterinario-avatar">👨‍⚕️</div>
                        <div class="veterinario-nombre"><?= htmlspecialchars($vet['nombre_veterinario']) ?></div>
                        <div class="veterinario-especialidad"><?= htmlspecialchars($especialidad['nombre']) ?></div>
                        
                        <div class="veterinario-contacto">
                            <div>📧 <a href="mailto:<?= htmlspecialchars($vet['correo']) ?>"><?= htmlspecialchars($vet['correo']) ?></a></div>
                            <div>📞 <?= htmlspecialchars($vet['telefono']) ?></div>
                        </div>
                        
                        <?php if(isset($_SESSION['usuario']) && $_SESSION['rol'] == 'user'): ?>
                            <a href="../user/agendar.php?veterinario=<?= $vet['id'] ?>&especialidad=<?= $id ?>" class="btn-agendar-vet">
                                <i class='bx bx-calendar'></i> Agendar Cita
                            </a>
                        <?php elseif(!isset($_SESSION['usuario'])): ?>
                            <a href="../auth/register.php" class="btn-agendar-vet">
                                <i class='bx bx-calendar'></i> Registrarse para Agendar
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-veterinarios">
                <p>No hay veterinarios disponibles para esta especialidad en este momento.</p>
                <p>Por favor, intenta más tarde o contáctanos directamente.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Información Adicional -->
    <div class="info-seccion">
        <h2>ℹ️ Información Importante</h2>
        <ul style="color: var(--text-muted); line-height: 1.8; margin-left: 20px;">
            <li>Disponemos de equipamiento médico de última tecnología</li>
            <li>Nuestros especialistas cuentan con amplia experiencia</li>
            <li>Atención disponible 24/7 para emergencias</li>
            <li>Horarios de consulta flexibles adaptados a tu necesidad</li>
            <li>Seguimiento completo del tratamiento de tu mascota</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="index.php" class="btn-volver">← Volver a Especialidades</a>
    </div>
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
