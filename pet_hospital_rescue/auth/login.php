<?php
session_start();
include("../config/db.php");

$error = "";
$correo_guardado = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $correo_guardado = htmlspecialchars($correo);

    if(empty($correo) || empty($password)){
        $error = "Por favor, completa todos los campos";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $user = $stmt->fetch();

            if($user && password_verify($password, $user['password'])){
                $_SESSION['usuario'] = $user['correo'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['id'] = $user['id'];

                if($user['rol'] == 'admin'){
                    header("Location: ../admin/dashboard.php");
                } elseif($user['rol'] == 'veterinario'){
                    header("Location: ../veterinario/dashboard.php");
                } else {
                    header("Location: ../user/dashboard.php");
                }
                exit();
            } else {
                $error = "Correo o contraseña incorrectos";
            }
        } catch(PDOException $e) {
            $error = "Error en el sistema.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PET HOSPITAL AND RESCUE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-strong);
            border-top: 5px solid var(--accent-gold);
        }
        .login-box h2 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 10px;
            font-weight: 800;
        }
        .login-box p {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 30px;
        }
        .btn-login {
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
        .btn-login:hover {
            background: var(--accent-gold);
            color: var(--primary-dark);
        }
        .error-msg {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="../assets/img/logo.png" style="height: 80px;">
    </div>
    <h2>Bienvenido</h2>
    <p>PET HOSPITAL AND RESCUE</p>

    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" value="<?= $correo_guardado ?>" required placeholder="ejemplo@correo.com">
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn-login">INICIAR SESIÓN</button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="register.php" style="color: var(--primary-dark); font-weight: 600; text-decoration: none;">¿No tienes cuenta? Regístrate</a>
        <br><br>
        <a href="../index.php" style="color: var(--text-muted); font-size: 0.8rem; text-decoration: none;">← Volver al inicio</a>
    </div>
</div>

</body>
</html>
