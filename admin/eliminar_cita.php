<?php
session_start();
include("../config/db.php");

// Verificar admin
if($_SESSION['user']['rol'] != 'admin'){
    exit("Acceso denegado");
}

// Obtener ID de cita
$id = $_GET['id'];

// Eliminar cita
$stmt = $pdo->prepare("DELETE FROM citas WHERE id=?");
$stmt->execute([$id]);

// Redirigir
header("Location: citas.php");