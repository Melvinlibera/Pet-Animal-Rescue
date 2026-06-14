<?php
/**
 * AGREGAR CITA - AJAX
 * Validaciones de servidor para crear nueva cita
 * Requiere: id_usuario, id_veterinario, id_especialidad, fecha, hora
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede crear citas
 */
session_start();
header('Content-Type: application/json');
include("../../config/db.php");
include("../../config/respuestas.php");
include("../../config/sesiones.php");

try {
    validarTokenPost();
} catch (Exception $e) {
    responderNoAutorizado($e->getMessage());
}

// ============================
// VALIDACIÓN: Autorización
// ============================
if(!verificarSesionAdmin()) {
    responderError("No autorizado - Requiere permisos de admin", [], 403);
}

// ============================
// VALIDACIÓN: Campos obligatorios
// ============================
if(empty($_POST['id_usuario']) || empty($_POST['id_veterinario']) || 
   empty($_POST['id_especialidad']) || empty($_POST['fecha']) || empty($_POST['hora'])) {
    responderValidacion("Todos los campos son obligatorios", []);
}

// ============================
// VALIDACIÓN: Formato fecha y hora
// ============================
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha'])) {
    responderValidacion("Formato de fecha inválido (YYYY-MM-DD)", []);
}

if(!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $_POST['hora'])) {
    responderValidacion("Formato de hora inválido (HH:MM o HH:MM:SS)", []);
}

try {
    // Validar que el usuario existe
    if (!db_fetch("SELECT id FROM usuarios WHERE id = ?", [$_POST['id_usuario']])) {
        responderError("Usuario no encontrado", [], 404);
    }
    
    // Validar que el veterinario existe
    if (!db_fetch("SELECT id FROM veterinarioes WHERE id = ?", [$_POST['id_veterinario']])) {
        responderError("Veterinario no encontrado", [], 404);
    }
    
    // Validar que la especialidad existe
    if (!db_fetch("SELECT id FROM especialidades WHERE id = ?", [$_POST['id_especialidad']])) {
        responderError("Especialidad no encontrada", [], 404);
    }
    
    // Validar que no exista cita en el mismo horario
    if (db_fetch("SELECT id FROM citas WHERE id_veterinario = ? AND fecha = ? AND hora = ? AND estado != 'cancelada'", [$_POST['id_veterinario'], $_POST['fecha'], $_POST['hora']])) {
        responderError("El veterinario ya tiene una cita en esa fecha y hora", [], 409);
    }

    // Insertar cita
    $id_usuario = sanitizarNumero($_POST['id_usuario']);
    $id_veterinario = sanitizarNumero($_POST['id_veterinario']);
    $id_especialidad = sanitizarNumero($_POST['id_especialidad']);
    $fecha = sanitizarTexto($_POST['fecha']);
    $hora = sanitizarTexto($_POST['hora']);

    $id_cita = db_insert('citas', [
        'id_usuario' => $id_usuario,
        'id_veterinario' => $id_veterinario,
        'id_especialidad' => $id_especialidad,
        'fecha' => $fecha,
        'hora' => $hora,
        'estado' => 'pendiente',
        'fecha_creacion' => date('Y-m-d H:i:s')
    ]);


} catch(PDOException $e){
    responderError("Error al guardar la cita: " . $e->getMessage(), [], 500);
}
?>