<?php
/**
 * CONFIGURACIÓN CENTRALIZADA DE SESIONES
 * PET HOSPITAL AND RESCUE - Sistema de Rescate y Hospital
 * 
 * Este archivo estandariza el manejo de sesiones en toda la aplicación.
 * Incluye validaciones comunes y helpers para trabajar con sesiones.
 * 
 * Variables de sesión estándar:
 * - $_SESSION['usuario']   : Email del usuario
 * - $_SESSION['id']        : ID del usuario (único)
 * - $_SESSION['nombre']    : Nombre completo
 * - $_SESSION['rol']       : 'admin', 'veterinario', 'user'
 * - $_SESSION['cedula']    : Cédula del usuario
 * - $_SESSION['telefono']  : Teléfono
 * - $_SESSION['seguro']    : Información de seguro (si aplica)
 */

if (!session_id()) {
    session_start();
}

/**
 * VALIDACIONES DE SESIÓN COMUNES
 */

// ========================================================
// FUNCIÓN: verificar sesión de usuario regular
// ========================================================
function verificarSesionUsuario() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['rol'] !== 'user') {
        header("Location: /citas_medicas/auth/login.php");
        exit();
        return false;
    }
    return true;
}

// ========================================================
// FUNCIÓN: verificar sesión de veterinario
// ========================================================
function verificarSesionVeterinario() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['rol'] !== 'veterinario') {
        header("Location: /citas_medicas/auth/login.php");
        exit();
        return false;
    }
    return true;
}

// ========================================================
// FUNCIÓN: verificar sesión de administrador
// ========================================================
function verificarSesionAdmin() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
        header("Location: /citas_medicas/auth/login.php");
        exit();
        return false;
    }
    return true;
}

// ========================================================
// FUNCIÓN: obtener ID del usuario actual
// ========================================================
function obtenerIdUsuario() {
    return $_SESSION['id'] ?? null;
}

// ========================================================
// FUNCIÓN: obtener rol del usuario actual
// ========================================================
function obtenerRolUsuario() {
    return $_SESSION['rol'] ?? null;
}

// ========================================================
// FUNCIÓN: obtener nombre del usuario actual
// ========================================================
function obtenerNombreUsuario() {
    return $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? null;
}

// ========================================================
// FUNCIÓN: verificar si el usuario está autenticado
// ========================================================
function estáAutenticado() {
    return isset($_SESSION['usuario']) && isset($_SESSION['id']) && isset($_SESSION['rol']);
}

// ========================================================
// FUNCIÓN: verificar si el usuario es admin
// ========================================================
function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

// ========================================================
// FUNCIÓN: verificar si el usuario es veterinario
// ========================================================
function esVeterinario() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'veterinario';
}

// ========================================================
// FUNCIÓN: verificar si el usuario es usuario regular
// ========================================================
function esUsuarioRegular() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'user';
}

// ========================================================
// FUNCIÓN: cerrar sesión
// ========================================================
function cerrarSesion() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header("Location: /citas_medicas/index.php");
    exit();
}

// ========================================================
// FUNCIÓN: limpiar sesión con cuidado
// ========================================================
function limpiarSesion() {
    $_SESSION = array();
}

// ========================================================
// FUNCIÓN: validar CSRF token (si se implementa)
// ========================================================
function validarToken($token) {
    if (!isset($_SESSION['token']) || $_SESSION['token'] !== $token) {
        throw new Exception('Token CSRF inválido');
    }
}

// ========================================================
// FUNCIÓN: validar CSRF token a partir de request POST
// ========================================================
function validarTokenPost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!$token) {
        throw new Exception('CSRF token faltante');
    }

    validarToken($token);
}

// ========================================================
// FUNCIÓN: generar token CSRF
// ========================================================
function generarToken() {
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

// ========================================================
// FUNCIÓN: obtener token CSRF para hedge
// ========================================================
function obtenerTokenCSRF() {
    return generarToken();
}

// ========================================================
// FUNCIÓN: responder JSON con autenticación requerida
// ========================================================
if (!function_exists('responderAutenticacionRequerida')) {
    function responderAutenticacionRequerida() {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Autenticación requerida'
        ]);
        exit();
    }
}

// ========================================================
// FUNCIÓN: responder JSON con autorización denegada
// ========================================================
if (!function_exists('responderNoAutorizado')) {
    function responderNoAutorizado() {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado'
        ]);
        exit();
    }
}

// ========================================================
// DEPURACIÓN (Descomentar solo en desarrollo)
// ========================================================
if (false) {
    // $_SESSION debug
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
}
?>
