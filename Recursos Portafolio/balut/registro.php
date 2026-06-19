<?php
/**
 * BALUT DECO - Registro de nuevos usuarios
 * Crea cuenta nueva y guarda en la base de datos
 */

session_start();
require_once 'db.php';

$error = '';
$success = '';

// Procesar formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];
    
    if (!empty($nombre) && !empty($email) && !empty($contrasena)) {

        // Verificar si el email ya existe
        $query_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $query_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Este email ya está registrado";
        } else {
            // Hashear contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $query = "INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $contrasena_hash);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registro exitoso. Ya puedes iniciar sesión";
            } else {
                $error = "Error al crear la cuenta.";
            }
            
            mysqli_stmt_close($stmt);
        }
        
        mysqli_stmt_close($stmt_check);

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
    <title>Registro - Balut Deco</title>

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

    <h2>Crear cuenta nueva</h2>
    
    <?php if (!empty($error)): ?>
        <p class="login-error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="login-success"><?php echo htmlspecialchars($success); ?></p>
        <p class="login-enlace">
            <a href="login.php">Ir a iniciar sesión</a>
        </p>
    <?php endif; ?>
    
    <form action="registro.php" method="POST" class="login-form">

        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required minlength="6">

        <button type="submit" class="btn-primario">Registrarse</button>
    </form>

    <p class="login-enlace">
        ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
    </p>

    <p class="login-enlace">
        <a href="index.php">← Volver al inicio</a>
    </p>

    <hr>

    <div class="login-social">
        <p>O regístrate con:</p>
        <button type="button" disabled class="social-btn google">🔵 Google</button>
        <button type="button" disabled class="social-btn facebook">📘 Facebook</button>
        <button type="button" disabled class="social-btn apple">🍎 Apple</button>
        <p class="nota">(Registro social no implementado - solo diseño)</p>
    </div>

</div>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
