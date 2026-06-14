<?php
/**
 * MANEJADOR DE RESPUESTAS JSON CENTRALIZADO
 * PET HOSPITAL AND RESCUE - Sistema de Rescate y Hospital
 * 
 * Este archivo proporciona funciones comunes para responder en formato JSON
 * desde todas las solicitudes AJAX del sistema.
 * 
 * Uso en archivos AJAX:
 * include("../../config/respuestas.php");
 * responderExito("Acción completada");
 * responderError("Algo salió mal");
 */

// Nota: NO se fija encabezado global aquí, para evitar alteración de páginas HTML
// Cada función de respuesta establece su propia cabecera JSON.

// ========================================================
// FUNCIÓN: Responder con éxito
// ========================================================
function responderExito($mensaje, $datos = [], $codigo = 200) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($codigo);
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'data' => $datos
    ]);
    exit();
}

// ========================================================
// FUNCIÓN: Responder con error
// ========================================================
function responderError($mensaje, $datos = [], $codigo = 400) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($codigo);
    echo json_encode([
        'success' => false,
        'message' => $mensaje,
        'data' => $datos
    ]);
    exit();
}

// ========================================================
// FUNCIÓN: Responder error de validación
// ========================================================
function responderValidacion($mensaje, $campos = []) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => $mensaje,
        'errors' => $campos
    ]);
    exit();
}

// ========================================================
// FUNCIÓN: Responder no autorizado
// ========================================================
if (!function_exists('responderNoAutorizado')) {
    function responderNoAutorizado($mensaje = 'Acceso denegado') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => $mensaje
        ]);
        exit();
    }
}

// ========================================================
// FUNCIÓN: Responder no autenticado
// ========================================================
if (!function_exists('responderNoAutenticado')) {
    function responderNoAutenticado($mensaje = 'Debe iniciar sesión') {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $mensaje
        ]);
        exit();
    }
}

// ========================================================
// FUNCIÓN: Responder no encontrado
// ========================================================
function responderNoEncontrado($mensaje = 'Recurso no encontrado') {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => $mensaje
    ]);
    exit();
}

// ========================================================
// FUNCIÓN: Responder error del servidor
// ========================================================
function responderError500($mensaje = 'Error del servidor') {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $mensaje
    ]);
    exit();
}

// ========================================================
// FUNCIONES DE VALIDACIÓN COMUNES
// ========================================================

/**
 * Escapar texto para salida HTML
 *
 * @param string $texto
 * @return string
 */
function esc($texto) {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Registrar acción en log
 *
 * @param string $accion
 * @param array $datos
 * @return void
 */
function registrarLog($accion, $datos = []) {
    $timestamp = date('Y-m-d H:i:s');
    $usuario_id = $_SESSION['id'] ?? 'N/A';
    $rol = $_SESSION['rol'] ?? 'N/A';
    $detalle = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $entry = "[{$timestamp}] usuario_id={$usuario_id} rol={$rol} accion={$accion} datos={$detalle}\n";

    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    file_put_contents("{$logDir}/acciones.log", $entry, FILE_APPEND | LOCK_EX);
}

/**
 * Validar que todos los campos requeridos existan
 * 
 * @param array $requeridos Lista de nombres de campos
 * @param array $datos Data a validar (por defecto $_POST)
 * @return bool
 */
function validarCamposRequeridos($requeridos, $datos = null) {
    if ($datos === null) {
        $datos = $_POST;
    }
    
    foreach ($requeridos as $campo) {
        if (empty($datos[$campo])) {
            return false;
        }
    }
    return true;
}

/**
 * Validar formato de email
 * 
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar formato de fecha (YYYY-MM-DD)
 * 
 * @param string $fecha
 * @return bool
 */
function validarFecha($fecha) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha);
}

/**
 * Validar formato de hora (HH:MM o HH:MM:SS)
 * 
 * @param string $hora
 * @return bool
 */
function validarHora($hora) {
    return preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora);
}

/**
 * Sanitizar entrada de texto
 * 
 * @param string $texto
 * @return string
 */
function sanitizarTexto($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitizar entrada de email
 * 
 * @param string $email
 * @return string
 */
function sanitizarEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitizar entrada de número
 * 
 * @param mixed $numero
 * @return int|float|null
 */
function sanitizarNumero($numero) {
    if (is_numeric($numero)) {
        return (int)$numero;
    }
    return null;
}

// ========================================================
// FUNCIONES DE LOGGING (Opcional)
// ========================================================

// El logging ya está implementado en registrarLog() sobre las funciones comunes.

?>
