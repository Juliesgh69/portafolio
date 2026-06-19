<?php
/**
 * BALUT DECO - Gestión de tarjetas
 * Permite agregar, listar y eliminar tarjetas guardadas
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

// Eliminar tarjeta
if (isset($_GET['eliminar'])) {
    $tarjeta_id = intval($_GET['eliminar']);
    
    $query_delete = "DELETE FROM tarjetas WHERE id = ? AND usuario_id = ?";
    $stmt_delete = mysqli_prepare($conn, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "ii", $tarjeta_id, $usuario_id);
    
    if (mysqli_stmt_execute($stmt_delete)) {
        $mensaje = "Tarjeta eliminada correctamente";
    } else {
        $error = "Error al eliminar la tarjeta";
    }
    
    mysqli_stmt_close($stmt_delete);
}

// Agregar nueva tarjeta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['titular'])) {
    $titular = trim($_POST['titular']);
    $numero = trim($_POST['numero']);
    $banco = trim($_POST['banco']);
    $expiracion = trim($_POST['expiracion']);
    $cvv = trim($_POST['cvv']);
    
    if (!empty($titular) && !empty($numero) && !empty($banco) && !empty($expiracion) && !empty($cvv)) {

        $ultimos_digitos = substr($numero, -4);
        $numero_encriptado = base64_encode($numero); // solo demo

        $query_insert = "INSERT INTO tarjetas (usuario_id, titular, numero_encriptado, ultimos_digitos, banco, expiracion) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "isssss", $usuario_id, $titular, $numero_encriptado, $ultimos_digitos, $banco, $expiracion);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            $mensaje = "Tarjeta agregada correctamente";
        } else {
            $error = "Error al agregar la tarjeta";
        }
        
        mysqli_stmt_close($stmt_insert);
    } else {
        $error = "Por favor completa todos los campos";
    }
}

// Obtener tarjetas del usuario
$query = "SELECT id, titular, ultimos_digitos, banco, expiracion FROM tarjetas WHERE usuario_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$tarjetas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tarjetas - Balut Deco</title>

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

    <h2>Mis tarjetas</h2>

    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Lista de tarjetas -->
    <div class="contenedor-tarjetas">
        <h3>Tarjetas guardadas (<?php echo count($tarjetas); ?>)</h3>
        
        <?php if (count($tarjetas) > 0): ?>
            <?php foreach ($tarjetas as $tarjeta): ?>
                <div class="tarjeta">
                    <a href="tarjetas.php?eliminar=<?php echo $tarjeta['id']; ?>" 
                       class="btn-eliminar"
                       onclick="return confirm('¿Estás seguro de eliminar esta tarjeta?')">
                        ✕ Eliminar
                    </a>
                    <div><strong>Titular:</strong> <?php echo htmlspecialchars($tarjeta['titular']); ?></div>
                    <div><strong>Número:</strong> **** **** **** <?php echo htmlspecialchars($tarjeta['ultimos_digitos']); ?></div>
                    <div><strong>Banco:</strong> <?php echo htmlspecialchars($tarjeta['banco']); ?></div>
                    <div><strong>Expiración:</strong> <?php echo htmlspecialchars($tarjeta['expiracion']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes tarjetas guardadas.</p>
        <?php endif; ?>
    </div>

    <!-- Formulario para añadir tarjeta -->
    <div class="añadir-tarjeta">
        <h3>Añadir nueva tarjeta</h3>
        <form action="tarjetas.php" method="POST" class="tarjetas-form">
            <label for="titular">Nombre del titular:</label>
            <input type="text" id="titular" name="titular" placeholder="Ej: Juan Pérez" required>

            <label for="numero">Número de tarjeta:</label>
            <input type="text" id="numero" name="numero" placeholder="1234 5678 9012 3456" required maxlength="19">

            <label for="banco">Banco emisor:</label>
            <input type="text" id="banco" name="banco" placeholder="Ej: BBVA, Santander" required>

            <label for="expiracion">Fecha de expiración (MM/AA):</label>
            <input type="text" id="expiracion" name="expiracion" placeholder="12/25" required maxlength="5">

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" placeholder="123" required maxlength="4">

            <button type="submit" class="btn-primario">Añadir tarjeta</button>
        </form>
        
        <p class="nota-tarjetas"><small>🔒 Tus datos están protegidos y encriptados (demo escolar)</small></p>
    </div>

    <a href="perfil.php" class="btn-secundario">← Volver al perfil</a>

    <hr>

    <div class="tarjetas-metodos">
        <h3>Métodos de pago aceptados</h3>
        <p>💳 Mastercard &nbsp; | &nbsp; 💳 VISA &nbsp; | &nbsp; 💰 PayPal</p>
    </div>

</section>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
