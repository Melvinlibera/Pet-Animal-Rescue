<?php
/**
 * ELIMINAR ESPECIALIDAD - AJAX
 * Elimina una especialidad médica
 * Parámetros JSON: {id: 123}
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede eliminar especialidades
 */
session_start();
include("../../config/db.php");
include("../../config/respuestas.php");
include("../../config/sesiones.php");

// ============================
// VALIDACIÓN: Autorización
// ============================
if(!verificarSesionAdmin()) {
    responderError("No autorizado - Requiere permisos de admin", [], 403);
}

// Obtener datos (POST)
if(empty($_POST['id'])) {
    responderValidacion("ID requerido", []);
}

$id = $_POST['id'];

$id = sanitizarNumero($id);
if (!$id) {
    responderError("ID inválido", [], 400);
}

try {
    // Verificar que exista
    if (!db_fetch("SELECT id FROM especialidades WHERE id=?", [$id])) {
        responderError("Especialidad no encontrada", [], 404);
    }

    // Eliminar
    db_delete('especialidades', 'id = ?', [$id]);
    registrarLog('eliminar_especialidad', ['id' => $id, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito("Especialidad eliminada correctamente");
} catch(PDOException $e) {
    responderError("Error al eliminar: " . $e->getMessage(), [], 500);
}
?>