<?php
/**
 * BALUT DECO - Gestión de direcciones de entrega
 * Permite agregar, listar y eliminar direcciones guardadas
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

// Eliminar dirección
if (isset($_GET['eliminar'])) {
    $direccion_id = intval($_GET['eliminar']);

    $query_delete = "DELETE FROM direcciones WHERE id = ? AND usuario_id = ?";
    $stmt_delete = mysqli_prepare($conn, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $direccion_id, $usuario_id);

    if (mysqli_stmt_execute($stmt_delete)) {
        $mensaje = "Dirección eliminada correctamente";
    } else {
        $error = "Error al eliminar la dirección";
    }

    mysqli_stmt_close($stmt_delete);
}

// Agregar nueva dirección
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['destinatario'])) {
    $destinatario = trim($_POST['destinatario']);
    $calle = trim($_POST['calle']);
    $ciudad = trim($_POST['ciudad']);
    $codigo = trim($_POST['codigo']);
    $telefono = trim($_POST['telefono']);

    if (!empty($destinatario) && !empty($calle) && !empty($ciudad) && !empty($codigo) && !empty($telefono)) {
        $query_insert = "INSERT INTO direcciones (usuario_id, destinatario, calle, ciudad, codigo_postal, telefono) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "isssss", $usuario_id, $destinatario, $calle, $ciudad, $codigo, $telefono);

        if (mysqli_stmt_execute($stmt_insert)) {
            $mensaje = "Dirección agregada correctamente";
        } else {
            $error = "Error al agregar la dirección";
        }

        mysqli_stmt_close($stmt_insert);
    } else {
        $error = "Por favor completa todos los campos";
    }
}

// Obtener direcciones guardadas
$query = "SELECT id, destinatario, calle, ciudad, codigo_postal, telefono FROM direcciones WHERE usuario_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$direcciones = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Direcciones - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <h1><a href="index.php" style="color:white;text-decoration:none;">Balut Deco</a></h1>
</header>

<section style="max-width:700px;">
    <h2>Mis direcciones</h2>

    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Lista de direcciones -->
    <div class="contenedor-direcciones">
        <h3>Direcciones guardadas (<?php echo count($direcciones); ?>)</h3>

        <?php if (count($direcciones) > 0): ?>
            <?php foreach ($direcciones as $direccion): ?>
                <div class="direccion">
                    <a href="direcciones.php?eliminar=<?php echo $direccion['id']; ?>"
                       class="btn-eliminar"
                       onclick="return confirm('¿Eliminar esta dirección?')">
                        ✕ Eliminar
                    </a>

                    <div><strong>Destinatario:</strong> <?php echo htmlspecialchars($direccion['destinatario']); ?></div>
                    <div><strong>Calle / Colonia:</strong> <?php echo htmlspecialchars($direccion['calle']); ?></div>
                    <div><strong>Ciudad / Estado:</strong> <?php echo htmlspecialchars($direccion['ciudad']); ?></div>
                    <div><strong>Código Postal:</strong> <?php echo htmlspecialchars($direccion['codigo_postal']); ?></div>
                    <div><strong>Teléfono:</strong> <?php echo htmlspecialchars($direccion['telefono']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes direcciones guardadas.</p>
        <?php endif; ?>
    </div>

    <!-- Formulario añadir dirección -->
    <div class="añadir-direccion">
        <h3>Añadir nueva dirección</h3>
        <form action="direcciones.php" method="POST" class="direcciones-form">

            <label for="destinatario">Nombre del destinatario:</label>
            <input type="text" id="destinatario" name="destinatario" placeholder="Ej: Julieta García" required>

            <label for="calle">Calle, número, colonia:</label>
            <input type="text" id="calle" name="calle" placeholder="Av. México 123, Col. Centro" required>

            <label for="ciudad">Ciudad y estado:</label>
            <input type="text" id="ciudad" name="ciudad" placeholder="Puerto Vallarta, Jalisco" required>

            <label for="codigo">Código Postal:</label>
            <input type="text" id="codigo" name="codigo" placeholder="12345" required maxlength="10">

            <label for="telefono">Teléfono de contacto:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="3221234567" required maxlength="20">

            <button type="submit" class="btn-primario">Añadir dirección</button>
        </form>
    </div>

    <a href="perfil.php" class="btn-secundario">← Volver al perfil</a>

</section>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
