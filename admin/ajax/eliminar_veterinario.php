<?php
/**
 * ELIMINAR DOCTOR - AJAX
 * Elimina un veterinario y su usuario asociado del sistema
 * Parámetros POST: id (id del veterinario)
 * Retorna: JSON con éxito o error
 * Nota: Usa transacción para eliminar veterinario y su usuario de forma atómica
 * Seguridad: Solo admin puede eliminar veterinarios
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
    responderValidacion("ID del veterinario es requerido", []);
}

$id_veterinario = (int)$_POST['id'];

try {
    // ============================
    // VALIDACIÓN: Veterinario existe
    // ============================
    $veterinario = db_fetch("SELECT id_usuario FROM veterinarioes WHERE id = ?", [$id_veterinario]);

    if(!$veterinario) {
        responderError("El veterinario no existe", [], 404);
    }

    $id_usuario = $veterinario['id_usuario'];

    // ============================
    // INICIAR TRANSACCIÓN
    // ============================
    db()->beginTransaction();

    // ============================
    // PASO 1: Eliminar o marcar citas como canceladas
    // ============================
    // Opción: Marcar como canceladas en lugar de eliminar (conserva historial)
    db_update('citas', ['estado' => 'cancelada', 'fecha_actualizado' => date('Y-m-d H:i:s')], 'id_veterinario = ?', [$id_veterinario]);

    // ============================
    // PASO 2: Eliminar registro de veterinario
    // ============================
    db_delete('veterinarios', 'id = ?', [$id_veterinario]);

    // ============================
    // PASO 3: Eliminar usuario
    // ============================
    db_delete('usuarios', 'id = ?', [$id_usuario]);

    // ============================
    // CONFIRMAR TRANSACCIÓN
    // ============================
    db()->commit();

    registrarLog('eliminar_veterinario', ['id_veterinario' => $id_veterinario, 'id_usuario' => $id_usuario, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito(
        "Veterinario eliminado correctamente (sus citas fueron marcadas como canceladas)",
        ['id' => $id_veterinario, 'id_usuario' => $id_usuario]
    );

} catch(PDOException $e) {
    if(db()->inTransaction()) {
        db()->rollBack();
    }
    responderError("Error al eliminar el veterinario: " . $e->getMessage(), [], 500);
}
?>