<?php
/**
 * AGREGAR ESPECIALIDAD - AJAX
 * Crea una nueva especialidad médica
 * Parámetros POST: nombre, descripcion, precio
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede agregar especialidades
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

// Obtener datos del formulario (FormData)
$nombre = $_POST['nombre'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$precio = $_POST['precio'] ?? null;

// Validar campos
if (!validarCamposRequeridos(['nombre', 'descripcion', 'precio'], $_POST)) {
    responderValidacion("Todos los campos son requeridos");
}

try {
    $id = db_insert('especialidades', [
        'nombre' => sanitizarTexto($nombre),
        'descripcion' => sanitizarTexto($descripcion),
        'precio' => floatval($precio)
    ]);
    
    registrarLog('agregar_especialidad', ['id' => $id, 'nombre' => $nombre, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito("Especialidad agregada correctamente", ['id' => $id]);
} catch(PDOException $e) {
    responderError("Error al agregar especialidad: " . $e->getMessage(), [], 500);
}
?>