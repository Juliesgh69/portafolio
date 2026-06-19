<?php
/**
 * BALUT DECO - Perfil de usuario
 * Muestra información del usuario autenticado
 */

session_start();
require_once 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos actualizados del usuario desde la BD
$query = "SELECT nombre, email, foto_perfil FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$usuario) {
    // Si no se encuentra el usuario, cerrar sesión
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <h1><a href="index.php" style="color:white;text-decoration:none;">Balut Deco</a></h1>
</header>

<div class="login-container" style="max-width:600px;">

    <h2>Mi Perfil</h2>

    <!-- Foto de perfil -->
    <div class="perfil-foto">
        <div class="foto-container">
            <img 
                src="<?php echo !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : 'uploads/placeholder.jpg'; ?>" 
                alt="Foto de perfil"
            >
            <div class="overlay">Cambiar foto</div>
        </div>
    </div>

    <!-- Información del perfil -->
    <div class="perfil-info">
        <p class="perfil-nombre">
            <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
        </p>
        <p class="perfil-email">
            <?php echo htmlspecialchars($usuario['email']); ?>
        </p>
    </div>

    <!-- Apartados -->
    <div class="perfil-opciones">
        <a class="perfil-btn" href="informacion.php">Información del usuario</a>
        <a class="perfil-btn" href="tarjetas.php">Tarjetas</a>
        <a class="perfil-btn" href="direcciones.php">Direcciones</a>
    </div>

    <hr>

    <p class="login-enlace">
        <a href="index.php">← Volver al inicio</a> • 
        <a href="logout.php">Cerrar sesión</a>
    </p>

</div>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
