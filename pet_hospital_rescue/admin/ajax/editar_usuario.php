<?php
/**
 * EDITAR USUARIO - AJAX
 * Actualiza información de usuario existente
 * Parámetros: id, nombre, apellido, genero, seguro, cedula, telefono, correo, rol
 * Opcionales: password, confirm_password (para cambiar contraseña)
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
    empty($_POST['id']) ||
    empty($_POST['nombre']) ||
    empty($_POST['apellido']) ||
    empty($_POST['genero']) ||
    empty($_POST['seguro']) ||
    empty($_POST['cedula']) ||
    empty($_POST['telefono']) ||
    empty($_POST['correo']) ||
    empty($_POST['rol'])
) {
    responderValidacion("Todos los campos son obligatorios", []);
}

$id = (int)$_POST['id'];
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$genero = $_POST['genero'];
$seguro = trim($_POST['seguro']);
$correo = $_POST['correo'];
$rol = $_POST['rol'];
$password = $_POST['password'] ?? null;
$confirm_password = $_POST['confirm_password'] ?? null;

// ============================
// VALIDACIÓN: Contraseñas coinciden (si se proporcionan)
// ============================
if($password && $password !== $confirm_password) {
    responderValidacion("Las contraseñas no coinciden", []);
}

// ============================
// VALIDACIÓN: Formato correo
// ============================
if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responderValidacion("Formato de correo inválido", []);
}

// ============================
// VALIDACIÓN: Formato cédula (11 dígitos)
// ============================"
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
// VALIDACIÓN: Género válido
// ============================
if(!in_array($genero, ['masculino', 'femenino'])) {
    responderValidacion("Género inválido", []);
}

// ============================
// VALIDACIÓN: Rol válido
// ============================
if(!in_array($rol, ['admin', 'veterinario', 'user'])) {
    responderValidacion("Rol inválido", []);
}

// ============================
// VALIDACIÓN: Longitud contraseña (si se proporciona)
// ============================
if($password && strlen($password) < 6) {
    responderValidacion("La contraseña debe tener mínimo 6 caracteres", []);
}

try {
    // ============================
    // VALIDACIÓN: Usuario existe
    // ============================
    if(!db_fetch("SELECT id FROM usuarios WHERE id = ?", [$id])) {
        responderError("El usuario no existe", [], 404);
    }

    // ============================
    // VALIDACIÓN: Unicidad correo y cédula (excepto el usuario actual)
    // ============================
    if(db_fetch("SELECT id FROM usuarios WHERE (correo = ? OR cedula = ?) AND id != ?", [$correo, $cedula_limpia, $id])) {
        responderError("El correo o cédula ya están en uso por otro usuario", [], 409);
    }

    // ============================
    // FORMATEO AUTOMÁTICO
    // ============================
    $cedula_formateada = substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1);
    $telefono_formateado = substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4);

    // ============================
    // ACTUALIZAR: Usuario con o sin password
    // ============================
    if(!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $updated = db_update('usuarios', [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'genero' => $genero,
            'seguro' => $seguro,
            'cedula' => $cedula_formateada,
            'telefono' => $telefono_formateado,
            'correo' => $correo,
            'password' => $passwordHash,
            'rol' => $rol,
            'fecha_actualizado' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);

        if($updated === 0) {
            responderError("No se pudo actualizar el usuario", [], 500);
        }

        registrarLog('editar_usuario', ['id_usuario' => $id, 'id_admin' => $_SESSION['id'] ?? null]);
        responderExito("Usuario actualizado correctamente (con cambio de contraseña)", ['id' => $id]);

    } else {
        // Sin actualizar password
        $updated = db_update('usuarios', [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'genero' => $genero,
            'seguro' => $seguro,
            'cedula' => $cedula_formateada,
            'telefono' => $telefono_formateado,
            'correo' => $correo,
            'rol' => $rol,
            'fecha_actualizado' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);

        if($updated === 0) {
            responderError("No se pudo actualizar el usuario", [], 500);
        }

        registrarLog('editar_usuario', ['id_usuario' => $id, 'id_admin' => $_SESSION['id'] ?? null]);
        responderExito("Usuario actualizado correctamente", ['id' => $id]);
    }

} catch(PDOException $e) {
    responderError("Error al actualizar el usuario: " . $e->getMessage(), [], 500);
}
?>