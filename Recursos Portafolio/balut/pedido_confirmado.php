<?php
/**
 * BALUT DECO - Confirmación de pedido
 * Muestra los detalles del pedido realizado
 */

session_start();
require_once 'db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener ID del pedido
$pedido_id = isset($_GET['pedido_id']) ? intval($_GET['pedido_id']) : 0;

if ($pedido_id <= 0) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener información del pedido
$query_pedido = "SELECT p.*, 
                 d.destinatario, d.calle, d.ciudad, d.codigo_postal, d.telefono,
                 t.banco, t.ultimos_digitos
                 FROM pedidos p
                 INNER JOIN direcciones d ON p.direccion_id = d.id
                 INNER JOIN tarjetas t ON p.tarjeta_id = t.id
                 WHERE p.id = ? AND p.usuario_id = ?";
$stmt_pedido = mysqli_prepare($conn, $query_pedido);
mysqli_stmt_bind_param($stmt_pedido, "ii", $pedido_id, $usuario_id);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);
$pedido = mysqli_fetch_assoc($resultado_pedido);
mysqli_stmt_close($stmt_pedido);

// Si no existe el pedido o no pertenece al usuario, redirigir
if (!$pedido) {
    mysqli_close($conn);
    header("Location: index.php");
    exit();
}

// Obtener detalles del pedido (productos)
$query_detalles = "SELECT * FROM pedido_detalles WHERE pedido_id = ?";
$stmt_detalles = mysqli_prepare($conn, $query_detalles);
mysqli_stmt_bind_param($stmt_detalles, "i", $pedido_id);
mysqli_stmt_execute($stmt_detalles);
$resultado_detalles = mysqli_stmt_get_result($stmt_detalles);
$detalles = mysqli_fetch_all($resultado_detalles, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_detalles);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <h1><a href="index.php" style="color:white;text-decoration:none;">Balut Deco</a></h1>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="catalogo.php">Catálogo</a>
        <a href="carrito.php">🛒 Carrito</a>
        <a href="perfil.php">👤 Mi Perfil</a>
    </nav>
</header>

<section class="confirmacion-container">
    <!-- Box principal de confirmación -->
    <div class="confirmacion-box">
        <div class="icono-exito">✓</div>
        <h2>¡Tu pedido ha sido confirmado!</h2>
        <p>Hemos recibido tu pedido y está siendo procesado.</p>

        <div class="numero-pedido">
            <p>Número de pedido:</p>
            <strong>#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></strong>
        </div>

        <p>Te enviamos un correo con los detalles de tu compra a:</p>
        <p><strong><?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?></strong></p>

        <span class="estado-pedido-badge">
            Estado: <?php echo htmlspecialchars($pedido['estado']); ?>
        </span>
    </div>

    <!-- Estado / timeline -->
    <div class="seccion-pedido">
        <h3>📦 Estado del pedido</h3>
        <p class="pedido-fecha">
            Fecha del pedido: <?php echo date('d/m/Y H:i', strtotime($pedido['created_at'])); ?>
        </p>

        <div class="timeline">
            <h4>Proceso de entrega</h4>

            <div class="timeline-item completado">
                <span class="icono">✓</span>
                <span>Pedido confirmado</span>
            </div>
            <div class="timeline-item pendiente">
                <span class="icono">⏳</span>
                <span>Preparando envío</span>
            </div>
            <div class="timeline-item pendiente">
                <span class="icono">🚚</span>
                <span>En camino</span>
            </div>
            <div class="timeline-item pendiente">
                <span class="icono">📬</span>
                <span>Entregado</span>
            </div>
        </div>

        <div class="info-envio">
            <strong>Tiempo estimado de entrega:</strong> 3-5 días hábiles.
        </div>
    </div>

    <!-- Dirección de envío -->
    <div class="seccion-pedido">
        <h3>📍 Dirección de envío</h3>

        <div class="info-linea">
            <strong>Destinatario:</strong>
            <span><?php echo htmlspecialchars($pedido['destinatario']); ?></span>
        </div>
        <div class="info-linea">
            <strong>Dirección:</strong>
            <span><?php echo htmlspecialchars($pedido['calle']); ?></span>
        </div>
        <div class="info-linea">
            <strong>Ciudad:</strong>
            <span><?php echo htmlspecialchars($pedido['ciudad']); ?></span>
        </div>
        <div class="info-linea">
            <strong>Código Postal:</strong>
            <span><?php echo htmlspecialchars($pedido['codigo_postal']); ?></span>
        </div>
        <div class="info-linea">
            <strong>Teléfono:</strong>
            <span><?php echo htmlspecialchars($pedido['telefono']); ?></span>
        </div>
    </div>

    <!-- Método de pago -->
    <div class="seccion-pedido">
        <h3>💳 Método de pago</h3>

        <div class="info-linea">
            <strong>Tarjeta:</strong>
            <span><?php echo htmlspecialchars($pedido['banco']); ?> - **** <?php echo htmlspecialchars($pedido['ultimos_digitos']); ?></span>
        </div>

        <p class="pago-ok">
            ✓ Pago procesado exitosamente
        </p>
    </div>

    <!-- Detalle de productos -->
    <div class="seccion-pedido">
        <h3>🛍️ Productos del pedido</h3>

        <table class="productos-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                        <td>$<?php echo number_format($detalle['producto_precio'], 2); ?></td>
                        <td><?php echo (int)$detalle['cantidad']; ?></td>
                        <td><strong>$<?php echo number_format($detalle['subtotal'], 2); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totales">
            <div class="linea">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($pedido['subtotal'], 2); ?></span>
            </div>
            <div class="linea">
                <span>Envío:</span>
                <span>
                    <?php if ($pedido['envio'] == 0): ?>
                        <strong class="envio-gratis">¡GRATIS!</strong>
                    <?php else: ?>
                        $<?php echo number_format($pedido['envio'], 2); ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="linea total">
                <span>TOTAL PAGADO:</span>
                <span>$<?php echo number_format($pedido['total'], 2); ?></span>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="seccion-pedido info-extra">
        <h3>ℹ️ Información importante</h3>
        <ul>
            <li>Recibirás un correo con el número de seguimiento cuando tu pedido sea enviado.</li>
            <li>Puedes consultar el estado de tu pedido en tu perfil.</li>
            <li>Si tienes dudas, escribe a: <strong>contacto@balutdeco.com</strong></li>
            <li>Horario de atención: Lunes a Viernes de 9:00 a 18:00 hrs.</li>
        </ul>
    </div>

    <!-- Botones -->
    <div class="botones-accion">
        <a href="index.php" class="btn btn-primario">Volver al inicio</a>
        <a href="catalogo.php" class="btn btn-secundario">Seguir comprando</a>
        <button type="button" class="btn btn-secundario" onclick="window.print()">🖨️ Imprimir pedido</button>
    </div>

    <!-- Mensaje cierre -->
    <div class="agradecimiento-box">
        <h3>¡Gracias por confiar en Balut Deco!</h3>
        <p>Esperamos que tus velas, cerámicas y pósters hagan muy feliz a tu depa/cuartito/cueva creativa.</p>
        <p class="agradecimiento-mini">Si te gustó la experiencia, compártenos con tus amigos ❤️</p>
    </div>

</section>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
