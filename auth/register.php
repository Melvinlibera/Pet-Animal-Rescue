<?php
session_start();
include("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = 'user'; 

    if(empty($nombre) || empty($correo) || empty($password)){
        $error = "Completa los campos obligatorios";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            if($stmt->fetch()){
                $error = "El correo ya está registrado";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, correo, password, rol) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $apellido, $correo, $hash, $rol]);
                $success = "¡Registro exitoso! Ya puedes iniciar sesión.";
            }
        } catch(PDOException $e) {
            $error = "Error al registrar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .register-box {
            background: white;
            padding: 40px;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-strong);
            border-top: 5px solid var(--accent-gold);
        }
        .register-box h2 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 10px;
            font-weight: 800;
        }
        .btn-register {
            width: 100%;
            padding: 15px;
            background: var(--primary-dark);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-register:hover {
            background: var(--accent-gold);
            color: var(--primary-dark);
        }
        .error-msg { background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: var(--radius-sm); margin-bottom: 20px; text-align: center; }
        .success-msg { background: #dcfce7; color: #15803d; padding: 10px; border-radius: var(--radius-sm); margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="register-box">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="../assets/img/logo.png" style="height: 60px;">
    </div>
    <h2>Crea tu cuenta</h2>
    <p style="text-align: center; color: var(--text-muted); margin-bottom: 30px;">Únete a la familia PET HOSPITAL AND RESCUE</p>

    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success-msg"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" required placeholder="Nombre">
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" required placeholder="Apellido">
            </div>
        </div>
        <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" required placeholder="ejemplo@correo.com">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="Crea una contraseña">
        </div>
        <button type="submit" class="btn-register">REGISTRARSE</button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="login.php" style="color: var(--primary-dark); font-weight: 600; text-decoration: none;">¿Ya tienes cuenta? Inicia sesión</a>
        <br><br>
        <a href="../index.php" style="color: var(--text-muted); font-size: 0.8rem; text-decoration: none;">← Volver al inicio</a>
    </div>
</div>

</body>
</html>
