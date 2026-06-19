<?php
/**
 * BALUT DECO - Información del usuario
 * Permite editar el nombre del usuario
 */

session_start();
require_once 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';
$error = '';

// Obtener datos actuales del usuario
$query = "SELECT nombre, email FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Procesar actualización del nombre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nuevoUsuario'])) {
    $nuevo_nombre = trim($_POST['nuevoUsuario']);

    if (!empty($nuevo_nombre)) {

        $query_update = "UPDATE usuarios SET nombre = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "si", $nuevo_nombre, $usuario_id);

        if (mysqli_stmt_execute($stmt_update)) {
            $mensaje = "Nombre actualizado correctamente";
            $usuario['nombre'] = $nuevo_nombre;

            // Actualizar sesión
            $_SESSION['usuario_nombre'] = $nuevo_nombre;
        } else {
            $error = "Error al actualizar el nombre: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_update);
    } else {
        $error = "El nombre no puede estar vacío";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del usuario - Balut Deco</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <h1><a href="index.php" style="color:white;text-decoration:none;">Balut Deco</a></h1>
</header>

<section style="max-width:600px;">

    <h2>Información del usuario</h2>

    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Datos actuales -->
    <div class="info-section">
        <h3>Datos actuales</h3>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <p><small>(El email no se puede modificar)</small></p>
    </div>

    <!-- Formulario -->
    <div class="info-section">
        <h3>Actualizar nombre</h3>
        <form action="informacion.php" method="POST">
            <label for="nuevoUsuario">Nuevo nombre:</label>
            <input 
                type="text"
                id="nuevoUsuario"
                name="nuevoUsuario"
                placeholder="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                required
            >
            <button type="submit" class="btn">Actualizar nombre</button>
        </form>
    </div>

    <br>
    <a href="perfil.php" class="btn-secundario">← Volver al perfil</a>

</section>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
