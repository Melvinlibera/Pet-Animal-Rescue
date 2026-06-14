<?php
/**
 * EDITAR ESPECIALIDAD - AJAX
 * Actualiza información de especialidad existente
 * Parámetros POST: id, nombre, descripcion, precio
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede editar especialidades
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

// Obtener datos del formulario
$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$precio = $_POST['precio'] ?? null;

// Validar campos
if (!validarCamposRequeridos(['id', 'nombre', 'descripcion', 'precio'], $_POST)) {
    responderValidacion("Todos los campos son requeridos");
}

$id = sanitizarNumero($id);
if (!$id) {
    responderError("ID inválido", [], 400);
}

try {
    $updated = db_update('especialidades', [
        'nombre' => sanitizarTexto($nombre),
        'descripcion' => sanitizarTexto($descripcion),
        'precio' => floatval($precio)
    ], 'id = ?', [$id]);
    
    if ($updated > 0) {
        registrarLog('editar_especialidad', ['id' => $id, 'id_admin' => $_SESSION['id'] ?? null]);
        responderExito("Especialidad actualizada correctamente");
    } else {
        responderError("No se encontró la especialidad", [], 404);
    }
} catch(PDOException $e) {
    responderError("Error al actualizar: " . $e->getMessage(), [], 500);
}
?>