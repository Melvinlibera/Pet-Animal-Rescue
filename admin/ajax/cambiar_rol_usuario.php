<?php
/**
 * CAMBIAR ROL DE USUARIO - AJAX
 * Cambia el rol de un usuario en el sistema
 * Parámetros JSON: {id: 123, rol: "admin"|"veterinario"|"user"}
 * Retorna: JSON con éxito o error
 * Seguridad: Solo admin puede cambiar roles
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
// VALIDACIÓN: Campos requeridos
// ============================
if(empty($_POST['id']) || empty($_POST['rol'])) {
    responderValidacion("ID y rol son requeridos", []);
}

$id_usuario = (int)$_POST['id'];
$rol_nuevo = $_POST['rol'];

// ============================
// VALIDACIÓN: Rol válido
// ============================
$roles_validos = ['admin', 'veterinario', 'user'];
if(!in_array($rol_nuevo, $roles_validos)) {
    responderValidacion("Rol inválido. Válidos: " . implode(", ", $roles_validos), []);
}

try {
    // ============================
    // VALIDACIÓN: Usuario existe
    // ============================
    $usuario = db_fetch("SELECT id, rol FROM usuarios WHERE id = ?", [$id_usuario]);

    if(!$usuario) {
        responderError("El usuario no existe", [], 404);
    }

    $rol_actual = $usuario['rol'];

    // Si el rol es el mismo, no hacer nada
    if($rol_actual === $rol_nuevo) {
        registrarLog('cambiar_rol_usuario', ['id_usuario' => $id_usuario, 'rol' => $rol_nuevo, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito("El usuario ya tiene este rol", ['id' => $id_usuario, 'rol' => $rol_nuevo]);
    }

    // ============================
    // ACTUALIZAR: Rol del usuario
    // ============================
    db_update('usuarios', ['rol' => $rol_nuevo, 'fecha_actualizado' => date('Y-m-d H:i:s')], 'id = ?', [$id_usuario]);

    responderExito(
        "Rol actualizado correctamente de '$rol_actual' a '$rol_nuevo'",
        ['id' => $id_usuario, 'rol_anterior' => $rol_actual, 'rol_nuevo' => $rol_nuevo]
    );

} catch(PDOException $e) {
    responderError("Error al cambiar el rol: " . $e->getMessage(), [], 500);
}
?>