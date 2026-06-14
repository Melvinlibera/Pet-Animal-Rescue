<?php
/**
 * VALIDACIONES DE SEGUROS Y PRECIOS - HOSPITAL & HUMAN
 * 
 * Funcionalidad:
 * - Funciones para validar seguros veterinarios
 * - Cálculo de precios con descuentos por seguro
 * - Validación de tipos de seguro válidos
 * - Funciones auxiliares para precios
 * 
 * Seguros soportados (ARS de República Dominicana):
 * - SENASA
 * - INSS
 * - AMISALUD
 * - MAPFRE
 * - SEGUROS MONTERREY
 * - PRIVADO (sin seguro)
 */

// =========================
// LISTA DE SEGUROS VÁLIDOS
// =========================
$SEGUROS_VALIDOS = [
    'SENASA',
    'INSS',
    'AMISALUD',
    'MAPFRE',
    'SEGUROS MONTERREY',
    'PRIVADO'
];

// =========================
// DESCUENTOS POR SEGURO
// =========================
$DESCUENTOS_SEGURO = [
    'SENASA' => 0.75,           // 75% de descuento
    'INSS' => 0.75,             // 75% de descuento
    'AMISALUD' => 0.70,         // 70% de descuento
    'MAPFRE' => 0.65,           // 65% de descuento
    'SEGUROS MONTERREY' => 0.60, // 60% de descuento
    'PRIVADO' => 0              // Sin descuento
];

/**
 * Validar si un seguro es válido
 * 
 * @param string $seguro - Nombre del seguro
 * @return bool - True si es válido
 */
function esSeguroValido($seguro) {
    global $SEGUROS_VALIDOS;
    return in_array(strtoupper($seguro), $SEGUROS_VALIDOS);
}

/**
 * Obtener descuento por seguro
 * 
 * @param string $seguro - Nombre del seguro
 * @return float - Porcentaje de descuento (0-1)
 */
function obtenerDescuentoSeguro($seguro) {
    global $DESCUENTOS_SEGURO;
    $seguro = strtoupper($seguro);
    return isset($DESCUENTOS_SEGURO[$seguro]) ? $DESCUENTOS_SEGURO[$seguro] : 0;
}

/**
 * Calcular precio final con descuento por seguro
 * 
 * @param float $precio_base - Precio sin descuento
 * @param string $seguro - Tipo de seguro
 * @return array - Array con precios desglosados
 */
function calcularPrecioFinal($precio_base, $seguro) {
    $descuento_porcentaje = obtenerDescuentoSeguro($seguro);
    $descuento_monto = round($precio_base * $descuento_porcentaje, 2);
    $precio_final = round($precio_base - $descuento_monto, 2);
    
    return [
        'precio_base' => $precio_base,
        'descuento_porcentaje' => $descuento_porcentaje * 100,
        'descuento_monto' => $descuento_monto,
        'precio_final' => $precio_final,
        'seguro' => strtoupper($seguro)
    ];
}

/**
 * Formatear precio en moneda RD$
 * 
 * @param float $precio - Precio a formatear
 * @return string - Precio formateado
 */
function formatearPrecio($precio) {
    return 'RD$ ' . number_format($precio, 2, ',', '.');
}

/**
 * Validar teléfono dominicano
 * 
 * @param string $telefono - Número de teléfono
 * @return bool - True si es válido
 */
function esTelefonoValido($telefono) {
    // Formato: +1-809-XXX-XXXX o 809-XXX-XXXX o 8091234567
    $telefono = preg_replace('/[^0-9]/', '', $telefono);
    
    // Debe tener 10 dígitos (sin el +1) o 12 dígitos (con +1)
    if (strlen($telefono) == 10 && substr($telefono, 0, 3) == '809') {
        return true;
    }
    if (strlen($telefono) == 12 && substr($telefono, 0, 2) == '1' && substr($telefono, 2, 3) == '809') {
        return true;
    }
    
    return false;
}

/**
 * Validar cédula dominicana
 * 
 * @param string $cedula - Número de cédula
 * @return bool - True si es válido
 */
function esCedulaValida($cedula) {
    // Formato: XXX-XXXXXXX-X o XXXXXXXXXXX
    $cedula = preg_replace('/[^0-9]/', '', $cedula);
    
    // Debe tener 11 dígitos
    if (strlen($cedula) != 11) {
        return false;
    }
    
    // Validación del dígito verificador
    $digitos = str_split($cedula);
    $multiplicadores = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
    $suma = 0;
    
    for ($i = 0; $i < 10; $i++) {
        $producto = $digitos[$i] * $multiplicadores[$i];
        if ($producto >= 10) {
            $producto = $producto - 9;
        }
        $suma += $producto;
    }
    
    $digito_verificador = (10 - ($suma % 10)) % 10;
    
    return $digito_verificador == $digitos[10];
}

/**
 * Obtener lista de seguros válidos
 * 
 * @return array - Array de seguros
 */
function obtenerSegurosValidos() {
    global $SEGUROS_VALIDOS;
    return $SEGUROS_VALIDOS;
}

/**
 * Obtener información completa de un seguro
 * 
 * @param string $seguro - Nombre del seguro
 * @return array|null - Información del seguro o null
 */
function obtenerInfoSeguro($seguro) {
    if (!esSeguroValido($seguro)) {
        return null;
    }
    
    $seguro = strtoupper($seguro);
    $descuento = obtenerDescuentoSeguro($seguro);
    
    return [
        'nombre' => $seguro,
        'descuento_porcentaje' => $descuento * 100,
        'es_privado' => $seguro === 'PRIVADO'
    ];
}

/**
 * Validar disponibilidad de cita
 * 
 * @param PDO $pdo - Conexión a base de datos
 * @param int $id_veterinario - ID del veterinario
 * @param string $fecha - Fecha de la cita (YYYY-MM-DD)
 * @param string $hora - Hora de la cita (HH:MM)
 * @return bool - True si está disponible
 */
function esCitaDisponible($pdo, $id_veterinario, $fecha, $hora) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total FROM citas 
            WHERE id_veterinario = ? 
            AND DATE(fecha_cita) = ? 
            AND TIME(fecha_cita) = ? 
            AND estado IN ('pendiente', 'completada')
        ");
        $stmt->execute([$id_veterinario, $fecha, $hora]);
        $resultado = $stmt->fetch();
        
        return $resultado['total'] == 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Validar que la fecha sea válida para agendar
 * 
 * @param string $fecha - Fecha a validar (YYYY-MM-DD)
 * @param int $dias_minimos - Días mínimos en el futuro (default: 1)
 * @return bool - True si es válida
 */
function esFechaValida($fecha, $dias_minimos = 1) {
    // Validar formato
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return false;
    }
    
    // Validar que sea una fecha válida
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return false;
    }
    
    // Validar que sea en el futuro
    $fecha_minima = date('Y-m-d', strtotime("+$dias_minimos days"));
    if ($fecha < $fecha_minima) {
        return false;
    }
    
    // Validar que no sea más de 3 meses en el futuro
    $fecha_maxima = date('Y-m-d', strtotime('+3 months'));
    if ($fecha > $fecha_maxima) {
        return false;
    }
    
    return true;
}

/**
 * Validar que la hora sea válida para agendar
 * 
 * @param string $hora - Hora a validar (HH:MM)
 * @return bool - True si es válida
 */
function esHoraValida($hora) {
    // Validar formato
    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        return false;
    }
    
    list($horas, $minutos) = explode(':', $hora);
    
    // Validar rango
    if ($horas < 8 || $horas > 17) {
        return false;
    }
    
    if ($minutos % 30 != 0) {
        return false;
    }
    
    return true;
}

?>
