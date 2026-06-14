<?php
session_start();
require_once("../config/db.php");
require_once("../config/respuestas.php");
require_once("../config/sesiones.php");
header('Content-Type: application/json');

try {
    validarTokenPost();
} catch (Exception $e) {
    responderNoAutorizado($e->getMessage());
}

// Validar sesión
if(!isset($_SESSION['id'])){
    responderNoAutenticado("Debes iniciar sesión");
}

// Validar campos requeridos
if (!validarCamposRequeridos(['especialidad', 'veterinario', 'fecha', 'hora'], $_POST)) {
    responderValidacion("Todos los campos son obligatorios");
}

$especialidad = sanitizarNumero($_POST['especialidad']);
$veterinario = sanitizarNumero($_POST['veterinario']);
$fecha = sanitizarTexto($_POST['fecha']);
$hora = sanitizarTexto($_POST['hora'] ?? '08:00');

// Validar formato de fecha y hora
if (!validarFecha($fecha)) {
    responderValidacion("Formato de fecha inválido (debe ser YYYY-MM-DD)");
}

if (!validarHora($hora)) {
    responderValidacion("Formato de hora inválido (debe ser HH:MM)");
}

try {
    // Verificar que veterinario existe
    $stmt = $pdo->prepare("SELECT id FROM veterinarioes WHERE id = ?");
    $stmt->execute([$veterinario]);
    if (!$stmt->fetch()) {
        responderError("Veterinario no encontrado", [], 404);
    }
    
    // Verificar que especialidad existe
    $stmt = $pdo->prepare("SELECT id FROM especialidades WHERE id = ?");
    $stmt->execute([$especialidad]);
    if (!$stmt->fetch()) {
        responderError("Especialidad no encontrada", [], 404);
    }
    
    // Verificar que fecha sea en el futuro
    $fecha_actual = date('Y-m-d');
    if ($fecha < $fecha_actual) {
        responderError("La fecha no puede ser en el pasado", [], 400);
    }
    
    // Insertar cita
    $stmt = $pdo->prepare("
        INSERT INTO citas (id_usuario, id_veterinario, id_especialidad, fecha, hora, estado, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())
    ");
    
    $stmt->execute([
        $_SESSION['id'],
        $veterinario,
        $especialidad,
        $fecha,
        $hora
    ]);
    
    $id_cita = $pdo->lastInsertId();
    registrarLog('agendar_cita', [
        'id_cita' => $id_cita,
        'id_usuario' => $_SESSION['id'],
        'id_veterinario' => $veterinario,
        'id_especialidad' => $especialidad,
        'fecha' => $fecha,
        'hora' => $hora
    ]);
    responderExito("Cita agendada correctamente", ['id_cita' => $id_cita]);
    
} catch(PDOException $e) {
    responderError("Error al agendar cita: " . $e->getMessage(), [], 500);
}
?>