<?php
/**
 * AGREGAR USUARIO - AJAX
 * Validaciones de servidor para crear nuevo usuario
 * Requiere: nombre, apellido, cedula, telefono, correo, password, rol, genero, seguro
 * Formatea automáticamente cédula (XXX-XXXXXXX-X) y teléfono (XXX-XXX-XXXX)
 * Valida contraseña mínimo 6 caracteres
 * Valida unicidad de correo y cédula
 * Retorna: JSON con éxito o error
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
// VALIDACIÓN: Campos obligatorios
// ============================
if(
    empty($_POST['nombre']) ||
    empty($_POST['apellido']) ||
    empty($_POST['genero']) ||
    empty($_POST['seguro']) ||
    empty($_POST['cedula']) ||
    empty($_POST['telefono']) ||
    empty($_POST['correo']) ||
    empty($_POST['password']) ||
    empty($_POST['confirm_password']) ||
    empty($_POST['rol'])
) {
    responderValidacion("Todos los campos son obligatorios", []);
}

// ============================
// VALIDACIÓN: Contraseñas coinciden
// ============================
if($_POST['password'] !== $_POST['confirm_password']) {
    responderValidacion("Las contraseñas no coinciden", []);
}

// ============================
// VALIDACIÓN: Formato correo
// ============================
if(!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
    responderValidacion("Formato de correo inválido", []);
}

// ============================
// VALIDACIÓN: Formato cédula (10 dígitos)
// ============================
$cedula_limpia = preg_replace('/[.\-\s()]/i', '', $_POST['cedula']);
if(!preg_match('/^\d{11}$/', $cedula_limpia)) {
    responderValidacion("Cédula inválida (debe tener exactamente 11 dígitos)", []);
}

// ============================
// VALIDACIÓN: Formato teléfono (10 dígitos)
// ============================
$telefono_limpio = preg_replace('/[.\-\s()]/i', '', $_POST['telefono']);
if(!preg_match('/^\d{10}$/', $telefono_limpio)) {
    responderValidacion("Teléfono inválido (debe tener 10 dígitos)", []);
}

// ============================
// VALIDACIÓN: Longitud contraseña
// ============================
if(strlen($_POST['password']) < 6) {
    responderValidacion("La contraseña debe tener mínimo 6 caracteres", []);
}

// ============================
// VALIDACIÓN: Rol válido
// ============================
$roles_validos = ['admin', 'veterinario', 'user'];
if(!in_array($_POST['rol'], $roles_validos)) {
    responderValidacion("Rol inválido", []);
}

try {
    // ============================
    // VALIDACIÓN: Unicidad correo y cédula
    // ============================
    if(db_fetch("SELECT id FROM usuarios WHERE correo = ? OR cedula = ?", [$_POST['correo'], $cedula_limpia])) {
        responderError("El correo o cédula ya están registrados en el sistema", [], 409);
    }

    // ============================
    // FORMATEO AUTOMÁTICO
    // ============================
    $cedula_formateada = substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1);
    $telefono_formateado = substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $genero = $_POST['genero'];
    $seguro = trim($_POST['seguro']);

    // ============================
    // INSERTAR: Nuevo usuario
    // ============================
    $id_usuario = db_insert('usuarios', [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'cedula' => $cedula_formateada,
        'telefono' => $telefono_formateado,
        'correo' => $_POST['correo'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'seguro' => $seguro,
        'genero' => $genero,
        'rol' => $_POST['rol'],
        'fecha_registro' => date('Y-m-d H:i:s')
    ]);

    registrarLog('agregar_usuario', ['id_usuario' => $id_usuario, 'id_admin' => $_SESSION['id'] ?? null]);
    registrarLog('agregar_usuario', ['id_usuario' => $id_usuario, 'id_admin' => $_SESSION['id'] ?? null]);
    responderExito("Usuario agregado correctamente", ['id' => $id_usuario]);

} catch(PDOException $e) {
    responderError("Error al guardar el usuario: " . $e->getMessage(), [], 500);
}
?>