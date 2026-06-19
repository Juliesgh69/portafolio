<?php
/**
 * BALUT DECO - Login de usuarios
 * Autentica usuarios contra la base de datos
 */

session_start();
require_once 'db.php';

$error = '';
$mensaje = '';

// Si ya hay sesión iniciada, no tiene sentido mostrar login
if (isset($_SESSION['usuario_id'])) {
    header("Location: perfil.php");
    exit();
}

// Mensaje si viene de logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $mensaje = "Sesión cerrada correctamente.";
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];
    
    if (!empty($email) && !empty($contrasena)) {

        $query = "SELECT id, nombre, email, contrasena, foto_perfil FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($usuario = mysqli_fetch_assoc($resultado)) {

            if (password_verify($contrasena, $usuario['contrasena'])) {

                // Regenerar ID de sesión para mayor seguridad
                session_regenerate_id(true);

                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_foto']   = $usuario['foto_perfil'];

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                header("Location: perfil.php");
                exit();

            } else {
                $error = "Contraseña incorrecta";
            }

        } else {
            $error = "Usuario no encontrado";
        }

        mysqli_stmt_close($stmt);

    } else {
        $error = "Por favor completa todos los campos";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">

</head>
<body>

<header class="header">
    <h1><a href="index.php" style="color:white;text-decoration:none;">Balut Deco</a></h1>
</header>

<div class="login-container">

    <h2>Iniciar sesión</h2>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="login-error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST" class="login-form">

        <label for="usuario">Email:</label>
        <input type="email" id="usuario" name="usuario" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit" class="btn-primario">Ingresar</button>
    </form>

    <p class="login-enlace">
        ¿No tienes cuenta? <a href="registro.php">Crear cuenta nueva</a>
    </p>

    <p class="login-enlace">
        <a href="index.php">← Volver al inicio</a>
    </p>

    <hr>

    <div class="login-social">
        <p>O inicia sesión con:</p>
        <button disabled class="social-btn google">🔵 Google</button>
        <button disabled class="social-btn facebook">📘 Facebook</button>
        <button disabled class="social-btn apple">🍎 Apple</button>
        <p class="nota">(Botones solo decorativos)</p>
    </div>

</div>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
