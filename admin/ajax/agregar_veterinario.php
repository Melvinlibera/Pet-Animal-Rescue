<?php
/**
 * AGREGAR DOCTOR - AJAX
 *
 * Funcionalidad:
 * - Crea un nuevo veterinario en el sistema mediante proceso de dos pasos
 * - Paso 1: Inserta usuario con rol 'veterinario' en tabla usuarios
 * - Paso 2: Inserta registro en tabla veterinarios con enlace a especialidad
 *
 * Proceso detallado:
 * 1. Valida todos los campos obligatorios
 * 2. Valida formato de cédula (11 dígitos) y correo electrónico
 * 3. Verifica que la especialidad seleccionada existe
 * 4. Verifica que no existan duplicados (correo, cédula)
 * 5. Inicia transacción de base de datos
 * 6. Inserta usuario con contraseña hasheada
 * 7. Obtiene ID del usuario creado
 * 8. Inserta registro de veterinario con enlace a especialidad
 * 9. Confirma transacción (COMMIT)
 *
 * Parámetros POST esperados:
 * - nombre, apellido: Nombre y apellido del veterinario
 * - cedula: Cédula sin formato (11 dígitos)
 * - telefono: Teléfono (10 dígitos)
 * - correo: Correo electrónico único
 * - genero: Género del veterinario
 * - password, confirm_password: Contraseña con confirmación
 * - id_especialidad: ID de la especialidad médica
 *
 * Respuesta JSON:
 * - success: true/false
 * - message: Mensaje descriptivo
 * - data: {id_usuario, id_veterinario}
 *
 * Seguridad:
 * - Transacción ACID para integridad
 * - Prepared statements
 * - Validación de relaciones foráneas
 * - Hash de contraseña con PASSWORD_DEFAULT
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
    empty($_POST['cedula']) ||
    empty($_POST['telefono']) ||
    empty($_POST['correo']) ||
    empty($_POST['genero']) ||
    empty($_POST['password']) ||
    empty($_POST['confirm_password']) ||
    empty($_POST['id_especialidad'])
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
if(!in_array($_POST['genero'], ['masculino', 'femenino'])) {
    responderValidacion("Género inválido", []);
}

// ============================
// VALIDACIÓN: Longitud contraseña
// ============================
if(strlen($_POST['password']) < 6) {
    responderValidacion("La contraseña debe tener mínimo 6 caracteres", []);
}

try {
    // ============================
    // VALIDACIÓN: Especialidad existe
    // ============================
    if(!db_fetch("SELECT id FROM especialidades WHERE id = ?", [$_POST['id_especialidad']])) {
        responderError("La especialidad seleccionada no existe", [], 404);
    }

    // ============================
    // VALIDACIÓN: Unicidad correo y cédula
    // ============================
    if(db_fetch("SELECT id FROM usuarios WHERE correo = ? OR cedula = ?", [$_POST['correo'], $cedula_limpia])) {
        responderError("El correo o cédula ya están registrados en el sistema", [], 409);
    }

    // ============================
    // INICIAR TRANSACCIÓN
    // ============================
    db()->beginTransaction();

    $nombreCompleto = trim($_POST['nombre']) . ' ' . trim($_POST['apellido']);
    $cedula_formateada = substr($cedula_limpia, 0, 3) . '-' . substr($cedula_limpia, 3, 7) . '-' . substr($cedula_limpia, 10, 1);
    $telefono_formateado = substr($telefono_limpio, 0, 3) . '-' . substr($telefono_limpio, 3, 3) . '-' . substr($telefono_limpio, 6, 4);

    // ============================
    // PASO 1: Crear usuario con rol veterinario
    // ============================
    $id_usuario = db_insert('usuarios', [
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'cedula' => $cedula_formateada,
        'telefono' => $telefono_formateado,
        'correo' => $_POST['correo'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'genero' => $_POST['genero'],
        'rol' => 'veterinario',
        'fecha_registro' => date('Y-m-d H:i:s')
    ]);

    // ============================
    // PASO 2: Crear registro de veterinario enlazado con especialidad
    // ============================
    $id_veterinario = db_insert('veterinarios', [
        'nombre' => $nombreCompleto,
        'id_especialidad' => $_POST['id_especialidad'],
        'id_usuario' => $id_usuario,
        'fecha_creacion' => date('Y-m-d H:i:s')
    ]);

    // ============================
    // CONFIRMAR TRANSACCIÓN
    // ============================
    db()->commit();

    registrarLog('agregar_veterinario', ['id_usuario' => $id_usuario, 'id_veterinario' => $id_veterinario]);
    responderExito(
        "Veterinario agregado correctamente y enlazado automáticamente con su especialidad",
        [
            'id_usuario' => $id_usuario,
            'id_veterinario' => $id_veterinario
        ]
    );

} catch(PDOException $e) {
    if(db()->inTransaction()) {
        db()->rollBack();
    }
    responderError("Error al guardar el veterinario: " . $e->getMessage(), [], 500);
}
?>