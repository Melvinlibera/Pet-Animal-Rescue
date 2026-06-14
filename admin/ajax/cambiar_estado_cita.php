<?php
/**
 * CAMBIAR ESTADO DE CITA - AJAX
 * 
 * Funcionalidad:
 * - Actualiza el estado de una cita médica
 * - Valida permisos de admin/veterinario
 * - Cambia entre estados: pendiente → confirmada/cancelada/completada
 * - Veterinarioes solo pueden cambiar sus propias citas
 * 
 * Parámetros JSON:
 * {
 *   "id": 123,
 *   "estado": "confirmada"
 * }
 * 
 * Respuesta JSON:
 * {
 *   "success": true,
 *   "message": "Estado actualizado a: confirmada",
 *   "data": {
 *     "id": 123,
 *     "estado": "confirmada"
 *   }
 * }
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
// VALIDACIÓN: Autenticación
// ============================
if(!estáAutenticado()) {
    responderNoAutenticado("Debe iniciar sesión");
}

// ============================
// VALIDACIÓN: Autorización (admin o veterinario)
// ============================
if(!in_array(obtenerRolUsuario(), ['admin', 'veterinario'])) {
    responderNoAutorizado("Solo administradores y veterinarios pueden cambiar estados de citas");
}

try {
    // ============================
    // LEER DATOS JSON
    // ============================
    // Datos vienen en POST
    
    // ============================
    // VALIDACIÓN: Campos requeridos
    // ============================
    if(empty($_POST['id']) || empty($_POST['estado'])) {
        responderValidacion("ID y estado son requeridos", []);
    }
    
    // ============================
    // SANITIZAR Y VALIDAR ID
    // ============================
    $id_cita = sanitizarNumero($_POST['id']);
    if(!$id_cita) {
        responderError("ID de cita inválido", [], 400);
    }
    
    // ============================
    // VALIDACIÓN: Estado válido
    // ============================
    $estados_validos = ['pendiente', 'confirmada', 'cancelada', 'completada'];
    $estado_nuevo = sanitizarTexto($_POST['estado']);
    
    if(!in_array($estado_nuevo, $estados_validos)) {
        responderError(
            "Estado inválido. Estados válidos: " . implode(", ", $estados_validos),
            [],
            400
        );
    }

    // ============================
    // VALIDACIÓN: Cita existe
    // ============================
    $cita = db_fetch("SELECT id_veterinario FROM citas WHERE id = ?", [$id_cita]);
    if(!$cita) {
        responderNoEncontrado("La cita no existe");
    }

    // ============================
    // VALIDACIÓN: Permiso para cambiar
    // ============================
    // Si es veterinario, verificar que sea su propia cita
    if(obtenerRolUsuario() === 'veterinario') {
        $veterinario = db_fetch("SELECT d.id_usuario FROM veterinarioes d WHERE d.id = ?", [$cita['id_veterinario']]);
        if(!$veterinario || $veterinario['id_usuario'] != obtenerIdUsuario()) {
            responderNoAutorizado("No puedes cambiar el estado de una cita que no es tuya");
        }
    }

    // ============================
    // ACTUALIZAR: Estado de cita
    // ============================
    $updated = db_update('citas', ['estado' => $estado_nuevo, 'fecha_actualizado' => date('Y-m-d H:i:s')], 'id = ?', [$id_cita]);

    if($updated === 0) {
        responderError("No se pudo actualizar el estado", [], 500);
    }

    registrarLog('cambiar_estado_cita', ['id_cita' => $id_cita, 'estado' => $estado_nuevo, 'id_usuario' => $_SESSION['id'] ?? null]);
    responderExito(
        "Estado actualizado correctamente a: $estado_nuevo",
        [
            'id' => $id_cita,
            'estado' => $estado_nuevo
        ]
    );

} catch(PDOException $e) {
    responderError("Error al actualizar el estado: " . $e->getMessage(), [], 500);
}
?>