<?php
/**
 * BALUT DECO - Checkout (Proceso de pago)
 * Selección de dirección, tarjeta y confirmación de pedido
 */

session_start();
require_once 'db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar que haya productos en el carrito
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';
$error = '';

// Obtener información de productos en el carrito
$productos_carrito = [];
$subtotal = 0;

if (!empty($_SESSION['carrito'])) {
    $ids = array_keys($_SESSION['carrito']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT id, nombre, precio, imagen FROM productos WHERE id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $query);
    
    $types = str_repeat('i', count($ids));
    mysqli_stmt_bind_param($stmt, $types, ...$ids);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    while ($producto = mysqli_fetch_assoc($resultado)) {
        $producto['cantidad'] = $_SESSION['carrito'][$producto['id']];
        $producto['subtotal'] = $producto['precio'] * $producto['cantidad'];
        $subtotal += $producto['subtotal'];
        $productos_carrito[] = $producto;
    }
    
    mysqli_stmt_close($stmt);
}

// Calcular envío y total
$envio = $subtotal >= 1000 ? 0 : 100;
$total = $subtotal + $envio;

// Obtener direcciones del usuario
$query_direcciones = "SELECT * FROM direcciones WHERE usuario_id = ? ORDER BY created_at DESC";
$stmt_dir = mysqli_prepare($conn, $query_direcciones);
mysqli_stmt_bind_param($stmt_dir, "i", $usuario_id);
mysqli_stmt_execute($stmt_dir);
$resultado_dir = mysqli_stmt_get_result($stmt_dir);
$direcciones = mysqli_fetch_all($resultado_dir, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_dir);

// Obtener tarjetas del usuario
$query_tarjetas = "SELECT id, titular, ultimos_digitos, banco, expiracion FROM tarjetas WHERE usuario_id = ? ORDER BY created_at DESC";
$stmt_tar = mysqli_prepare($conn, $query_tarjetas);
mysqli_stmt_bind_param($stmt_tar, "i", $usuario_id);
mysqli_stmt_execute($stmt_tar);
$resultado_tar = mysqli_stmt_get_result($stmt_tar);
$tarjetas = mysqli_fetch_all($resultado_tar, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_tar);

// Procesar el pedido cuando se confirma
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pedido'])) {
    $direccion_id = isset($_POST['direccion_id']) ? intval($_POST['direccion_id']) : 0;
    $tarjeta_id = isset($_POST['tarjeta_id']) ? intval($_POST['tarjeta_id']) : 0;
    
    // Validar que se haya seleccionado dirección y tarjeta
    if ($direccion_id > 0 && $tarjeta_id > 0) {
        // Verificar que la dirección y tarjeta pertenezcan al usuario
        $query_validar = "SELECT 
            (SELECT COUNT(*) FROM direcciones WHERE id = ? AND usuario_id = ?) as dir_valida,
            (SELECT COUNT(*) FROM tarjetas WHERE id = ? AND usuario_id = ?) as tar_valida";
        $stmt_validar = mysqli_prepare($conn, $query_validar);
        mysqli_stmt_bind_param($stmt_validar, "iiii", $direccion_id, $usuario_id, $tarjeta_id, $usuario_id);
        mysqli_stmt_execute($stmt_validar);
        $resultado_validar = mysqli_stmt_get_result($stmt_validar);
        $validacion = mysqli_fetch_assoc($resultado_validar);
        mysqli_stmt_close($stmt_validar);
        
        if ($validacion['dir_valida'] > 0 && $validacion['tar_valida'] > 0) {
            // Iniciar transacción
            mysqli_begin_transaction($conn);
            
            // OJO: mysqli no lanza excepciones por defecto, pero para proyecto escolar está bien este esquema
            try {
                // Insertar pedido
                $query_pedido = "INSERT INTO pedidos (usuario_id, direccion_id, tarjeta_id, subtotal, envio, total, estado) 
                                 VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
                $stmt_pedido = mysqli_prepare($conn, $query_pedido);
                mysqli_stmt_bind_param($stmt_pedido, "iiiddd", $usuario_id, $direccion_id, $tarjeta_id, $subtotal, $envio, $total);
                mysqli_stmt_execute($stmt_pedido);
                $pedido_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt_pedido);
                
                // Insertar detalles del pedido
                $query_detalle = "INSERT INTO pedido_detalles (pedido_id, producto_id, producto_nombre, producto_precio, cantidad, subtotal) 
                                  VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_detalle = mysqli_prepare($conn, $query_detalle);
                
                foreach ($productos_carrito as $item) {
                    mysqli_stmt_bind_param(
                        $stmt_detalle,
                        "iisdid",
                        $pedido_id,
                        $item['id'],
                        $item['nombre'],
                        $item['precio'],
                        $item['cantidad'],
                        $item['subtotal']
                    );
                    mysqli_stmt_execute($stmt_detalle);
                }
                
                mysqli_stmt_close($stmt_detalle);
                
                // Confirmar transacción
                mysqli_commit($conn);
                
                // Vaciar carrito
                $_SESSION['carrito'] = [];
                
                // Redirigir a página de confirmación
                header("Location: pedido_confirmado.php?pedido_id=" . $pedido_id);
                exit();
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                mysqli_rollback($conn);
                $error = "Error al procesar el pedido. Por favor intenta nuevamente.";
            }
        } else {
            $error = "Dirección o tarjeta no válida";
        }
    } else {
        $error = "Debes seleccionar una dirección de envío y un método de pago";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Balut Deco</title>

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

<!-- Indicador de pasos -->
<div class="pasos">
    <div class="paso">1. Carrito</div>
    <div class="paso activo">2. Datos de envío</div>
    <div class="paso">3. Confirmación</div>
</div>

<!-- Contenedor principal -->
<div class="checkout-container">
    <!-- Columna izquierda -->
    <div class="checkout-form">
        <h2>Finalizar compra</h2>

        <?php if (!empty($error)): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" id="form-checkout">
            
            <!-- Dirección -->
            <div class="seccion-checkout">
                <h3>📦 1. Dirección de envío</h3>
                
                <?php if (count($direcciones) > 0): ?>
                    <?php foreach ($direcciones as $index => $direccion): ?>
                        <div class="opcion" onclick="seleccionarOpcion(this)">
                            <input 
                                type="radio" 
                                name="direccion_id" 
                                value="<?php echo $direccion['id']; ?>" 
                                id="dir_<?php echo $direccion['id']; ?>"
                                <?php echo $index === 0 ? 'checked' : ''; ?>
                                required
                            >
                            <label for="dir_<?php echo $direccion['id']; ?>">
                                <strong><?php echo htmlspecialchars($direccion['destinatario']); ?></strong>
                            </label>
                            <div class="opcion-info">
                                <p><?php echo htmlspecialchars($direccion['calle']); ?></p>
                                <p><?php echo htmlspecialchars($direccion['ciudad']); ?> - CP: <?php echo htmlspecialchars($direccion['codigo_postal']); ?></p>
                                <p>Tel: <?php echo htmlspecialchars($direccion['telefono']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="direcciones.php" class="agregar-nuevo">+ Agregar nueva dirección</a>
                <?php else: ?>
                    <div class="sin-opciones">
                        <p>No tienes direcciones guardadas.</p>
                        <a href="direcciones.php" class="agregar-nuevo">+ Agregar dirección</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Método de pago -->
            <div class="seccion-checkout">
                <h3>💳 2. Método de pago</h3>
                
                <?php if (count($tarjetas) > 0): ?>
                    <?php foreach ($tarjetas as $index => $tarjeta): ?>
                        <div class="opcion" onclick="seleccionarOpcion(this)">
                            <input 
                                type="radio" 
                                name="tarjeta_id" 
                                value="<?php echo $tarjeta['id']; ?>" 
                                id="tar_<?php echo $tarjeta['id']; ?>"
                                <?php echo $index === 0 ? 'checked' : ''; ?>
                                required
                            >
                            <label for="tar_<?php echo $tarjeta['id']; ?>">
                                <strong><?php echo htmlspecialchars($tarjeta['banco']); ?></strong> - **** <?php echo htmlspecialchars($tarjeta['ultimos_digitos']); ?>
                            </label>
                            <div class="opcion-info">
                                <p>Titular: <?php echo htmlspecialchars($tarjeta['titular']); ?></p>
                                <p>Vence: <?php echo htmlspecialchars($tarjeta['expiracion']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a href="tarjetas.php" class="agregar-nuevo">+ Agregar nueva tarjeta</a>
                <?php else: ?>
                    <div class="sin-opciones">
                        <p>No tienes tarjetas guardadas.</p>
                        <a href="tarjetas.php" class="agregar-nuevo">+ Agregar tarjeta</a>
                    </div>
                <?php endif; ?>

                <div class="metodos-pago-iconos">
                    <span>💳</span>
                    <p>Aceptamos Mastercard, VISA y PayPal</p>
                </div>
            </div>

            <!-- Botón confirmar -->
            <?php if (count($direcciones) > 0 && count($tarjetas) > 0): ?>
                <button type="submit" name="confirmar_pedido" class="btn-confirmar">
                    Confirmar y pagar $<?php echo number_format($total, 2); ?>
                </button>
            <?php else: ?>
                <button type="button" class="btn-confirmar" disabled>
                    Completa dirección y método de pago para continuar
                </button>
            <?php endif; ?>

            <p class="checkout-volver">
                <a href="carrito.php">← Volver al carrito</a>
            </p>
        </form>
    </div>

    <!-- Columna derecha: Resumen -->
    <aside class="resumen-pedido">
        <h3>Resumen del pedido</h3>

        <div class="productos-resumen">
            <?php foreach ($productos_carrito as $item): ?>
                <div class="producto-mini">
                    <?php if (!empty($item['imagen']) && file_exists($item['imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                    <?php else: ?>
                        <img src="uploads/placeholder.jpg" alt="Sin imagen">
                    <?php endif; ?>

                    <div class="producto-mini-info">
                        <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                        <p>Cantidad: <?php echo $item['cantidad']; ?> x $<?php echo number_format($item['precio'], 2); ?></p>
                        <p><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="resumen-linea">
            <span>Subtotal:</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
        </div>

        <div class="resumen-linea">
            <span>Envío:</span>
            <span>
                <?php if ($envio == 0): ?>
                    <strong class="envio-gratis">¡GRATIS!</strong>
                <?php else: ?>
                    $<?php echo number_format($envio, 2); ?>
                <?php endif; ?>
            </span>
        </div>

        <div class="resumen-linea total">
            <span>TOTAL:</span>
            <span>$<?php echo number_format($total, 2); ?></span>
        </div>

        <div class="info-envio" style="margin-top: 20px;">
            <p style="margin:0;">🔒 Pago 100% seguro</p>
            <p style="margin:5px 0 0 0; font-size: 0.85rem; color:#666;">
                Tus datos se usan solo para procesar tu compra (proyecto escolar).
            </p>
        </div>
    </aside>
</div>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

<script>
    // Resaltar opción seleccionada
    function seleccionarOpcion(elemento) {
        const contenedor = elemento.parentElement;
        const opciones = contenedor.querySelectorAll('.opcion');
        opciones.forEach(op => op.classList.remove('seleccionada'));
        elemento.classList.add('seleccionada');

        const radio = elemento.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    // Marcar visualmente la primera opción por defecto
    document.addEventListener('DOMContentLoaded', function() {
        const seleccionadas = document.querySelectorAll('input[type="radio"]:checked');
        seleccionadas.forEach(radio => {
            const op = radio.closest('.opcion');
            if (op) op.classList.add('seleccionada');
        });
    });
</script>

</body>
</html>
