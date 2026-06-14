<?php
// =========================
//       CONEXIÓN PDO
// =========================

// CONFIGURACIÓN
$host = "localhost";
$db   = "citas_medicas";
$user = "root";
$pass = "";

// MODO DEBUG (cambiar a false en producción)
$debug = true;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        $options
    );

} catch (PDOException $e) {

    if($debug){
        die("Error de conexión: " . $e->getMessage());
    } else {
        die("Error de conexión con la base de datos.");
    }

}

// =========================
//   FUNCIONES AUXILIARES
// =========================

function db() {
    global $pdo;
    return $pdo;
}

function db_query(string $sql, array $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch(string $sql, array $params = []) {
    return db_query($sql, $params)->fetch();
}

function db_fetch_all(string $sql, array $params = []) {
    return db_query($sql, $params)->fetchAll();
}

function db_execute(string $sql, array $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt->rowCount();
}

function db_insert(string $table, array $data) {
    if(empty($data)) {
        throw new InvalidArgumentException('db_insert requiere datos no vacíos.');
    }

    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    db_query($sql, array_values($data));
    return db()->lastInsertId();
}

function db_update(string $table, array $data, string $where, array $whereParams = []) {
    if(empty($data)) {
        throw new InvalidArgumentException('db_update requiere un array de datos no vacío.');
    }

    $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
    $params = array_merge(array_values($data), $whereParams);

    $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, $set, $where);
    return db_execute($sql, $params);
}

function db_delete(string $table, string $where, array $whereParams = []) {
    $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
    return db_execute($sql, $whereParams);
}

?>