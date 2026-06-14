<?php
session_start();
require_once __DIR__ . '/../config/sesiones.php';
require_once __DIR__ . '/../config/respuestas.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="/citas_medicas/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<header id="header">
    <img src="/citas_medicas/assets/img/logo.png" class="logo" onclick="window.location='/citas_medicas/index.php';" style="cursor:pointer;">
    <nav class="nav">
        <?php if(isset($_SESSION['usuario'])): ?>
            <span>Hola, <?= htmlspecialchars($_SESSION['usuario']) ?></span>
            <?php if($_SESSION['rol'] == 'admin'): ?>
                <a href="/citas_medicas/admin/dashboard.php">Admin</a>
            <?php elseif($_SESSION['rol'] == 'veterinario'): ?>
                <a href="/citas_medicas/veterinario/dashboard.php">Mi Panel</a>
            <?php else: ?>
                <a href="/citas_medicas/user/dashboard.php">Mi Panel</a>
            <?php endif; ?>
            <a href="/citas_medicas/auth/logout.php" style="background: var(--accent-gold); color: var(--primary-dark);">Salir</a>
        <?php else: ?>
            <a href="/citas_medicas/index.php">Inicio</a>
            <a href="/citas_medicas/auth/login.php">Login</a>
            <a href="/citas_medicas/auth/register.php" style="background: var(--accent-gold); color: var(--primary-dark);">Registro</a>
        <?php endif; ?>
    </nav>
</header>
<div style="margin-top: 100px;"></div> <!-- Spacer for fixed header -->
