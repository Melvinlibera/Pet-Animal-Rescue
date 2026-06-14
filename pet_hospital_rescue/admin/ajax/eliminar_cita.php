<?php
/**
 * ELIMINAR CITA - AJAX
 * Elimina una cita médica del sistema
 * Parámetros JSON: {id: 123}
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede eliminar citas
 */
session_start();
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
// VALIDACIÓN: ID requerido
// ============================
if(empty($_POST['id'])) {
    responderValidacion("ID de la cita es requerido", []);
}

$id_cita = (int)$_POST['id'];

try {
    // ============================
    // VALIDACIÓN: Cita existe
    // ============================
    if(!db_fetch("SELECT id FROM citas WHERE id = ?", [$id_cita])) {
        responderError("La cita no existe", [], 404);
    }

    // ============================
    // ELIMINAR: Cita
    // ============================
    db_delete('citas', 'id = ?', [$id_cita]);

    registrarLog('eliminar_cita', ['id_cita' => $id_cita, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito("Cita eliminada correctamente", ['id' => $id_cita]);

} catch(PDOException $e) {
    responderError("Error al eliminar la cita: " . $e->getMessage(), [], 500);
}
?>