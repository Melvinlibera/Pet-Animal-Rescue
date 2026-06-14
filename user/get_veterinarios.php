<?php
/**
 * OBTENER DOCTORES POR ESPECIALIDAD - AJAX
 *
 * Funcionalidad:
 * - Endpoint AJAX para carga dinámica de veterinarios
 * - Utilizado por formularios de agendamiento de citas
 * - Recibe ID de especialidad y retorna lista de veterinarios disponibles
 * - Respuesta en formato JSON para consumo por JavaScript
 *
 * Uso típico:
 * - Formulario de agendamiento selecciona especialidad
 * - JavaScript envía petición AJAX con id_especialidad
 * - Este endpoint retorna veterinarios de esa especialidad
 * - Frontend actualiza select de veterinarios dinámicamente
 *
 * Parámetros de entrada (JSON):
 * {
 *   "id_especialidad": 5  // ID numérico de la especialidad
 * }
 *
 * Respuesta JSON:
 * [
 *   {"id": 1, "nombre": "Dr. Juan Pérez"},
 *   {"id": 2, "nombre": "Dra. María García"},
 *   ...
 * ]
 *
 * Validaciones:
 * - Recibe datos como JSON desde php://input
 * - Valor por defecto 0 si no se envía id_especialidad
 * - Prepared statement para prevenir SQL injection
 *
 * Seguridad:
 * - No requiere autenticación (información pública)
 * - Prepared statements para consultas seguras
 * - Solo retorna id y nombre (no información sensible)
 * - Header Content-Type: application/json establecido
 *
 * Notas técnicas:
 * - Utiliza PDO::FETCH_ASSOC por defecto
 * - Retorna array vacío si no hay veterinarios para la especialidad
 * - Compatible con axios.post() desde frontend
 */

include("../config/db.php"); // Conexión PDO

// Leer datos JSON enviados por la petición AJAX
$data = json_decode(file_get_contents("php://input"), true);
$esp_id = $data['id_especialidad'] ?? 0; // Valor por defecto si no se envía

// Consultar veterinarios de la especialidad especificada
// Solo campos necesarios: id y nombre para el select
$stmt = $pdo->prepare("SELECT id, nombre FROM veterinarioes WHERE id_especialidad = ?");
$stmt->execute([$esp_id]);
$veterinarios = $stmt->fetchAll();

// Retornar respuesta JSON
header('Content-Type: application/json');
echo json_encode($veterinarios);
?>