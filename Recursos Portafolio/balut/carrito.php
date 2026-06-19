<?php
/**
 * BALUT DECO - Carrito de compras
 * Gestiona productos agregados y calcula totales
 */

session_start();
require_once 'db.php';

// Inicializar carrito en sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$mensaje = '';
$error = '';

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    
    switch ($accion) {
        case 'agregar':
            $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
            
            if ($producto_id > 0 && $cantidad > 0) {
                if (isset($_SESSION['carrito'][$producto_id])) {
                    $_SESSION['carrito'][$producto_id] += $cantidad;
                } else {
                    $_SESSION['carrito'][$producto_id] = $cantidad;
                }
                $mensaje = "Producto agregado al carrito";
            }
            break;
            
        case 'actualizar':
            $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
            
            if ($producto_id > 0) {
                if ($cantidad > 0) {
                    $_SESSION['carrito'][$producto_id] = $cantidad;
                    $mensaje = "Cantidad actualizada";
                } else {
                    unset($_SESSION['carrito'][$producto_id]);
                    $mensaje = "Producto eliminado del carrito";
                }
            }
            break;
            
        case 'eliminar':
            if ($producto_id > 0 && isset($_SESSION['carrito'][$producto_id])) {
                unset($_SESSION['carrito'][$producto_id]);
                $mensaje = "Producto eliminado del carrito";
            }
            break;
            
        case 'vaciar':
            $_SESSION['carrito'] = [];
            $mensaje = "Carrito vaciado";
            break;
    }
}

// Obtener información de productos en el carrito
$productos_carrito = [];
$subtotal = 0;

if (!empty($_SESSION['carrito'])) {
    $ids = array_keys($_SESSION['carrito']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT id, nombre, precio, imagen, categoria FROM productos WHERE id IN ($placeholders)";
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
$envio = $subtotal >= 1000 ? 0 : 100; // Envío gratis en compras mayores a $1000
$total = $subtotal + $envio;

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- CSS principal -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- ✅ CSS específico del carrito -->
    <link rel="stylesheet" href="css/carrito.css">
</head>
<body>

    <!-- Header -->
    <header>
        <h1><a href="index.php" style="text-decoration: none; color: white;">Balut Deco</a></h1>
        <nav>
            <a href="index.php">Inicio</a> | 
            <a href="catalogo.php">Catálogo</a> | 
            <a href="carrito.php">🛒 Carrito</a> | 
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="perfil.php">👤 Mi Perfil</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <!-- Contenedor del carrito -->
    <div class="carrito-container">
        <h2>🛒 Mi Carrito de Compras</h2>

        <?php if (empty($productos_carrito)): ?>
            <!-- Carrito vacío -->
            <div class="carrito-vacio">
                <h2>Tu carrito está vacío</h2>
                <p>¡Descubre nuestros productos y comienza a comprar!</p>
                <a href="catalogo.php" class="btn-primario">
                    Ir al catálogo
                </a>
            </div>
        <?php else: ?>
            <!-- Lista de productos en el carrito -->
            <div class="carrito-items">
                <?php foreach ($productos_carrito as $item): ?>
                    <div class="carrito-item">
                        <!-- Imagen -->
                        <div>
                            <?php if (!empty($item['imagen']) && file_exists($item['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                            <?php else: ?>
                                <img src="uploads/placeholder.jpg" alt="Sin imagen">
                            <?php endif; ?>
                        </div>

                        <!-- Información del producto -->
                        <div>
                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <p class="categoria"><?php echo htmlspecialchars($item['categoria']); ?></p>
                        </div>

                        <!-- Precio unitario -->
                        <div class="precio">
                            $<?php echo number_format($item['precio'], 2); ?>
                        </div>

                        <!-- Cantidad -->
                        <div>
                            <form action="carrito.php" method="POST" class="form-cantidad">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="0" max="99">
                                <button type="submit">Actualizar</button>
                            </form>
                        </div>

                        <!-- Subtotal -->
                        <div class="precio">
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>

                        <!-- Eliminar -->
                        <div>
                            <form action="carrito.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn-eliminar" onclick="return confirm('¿Eliminar este producto?')">✕</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumen del carrito -->
            <div class="carrito-resumen">
                <h3>Resumen del pedido</h3>
                
                <div class="resumen-linea">
                    <span>Subtotal (<?php echo count($productos_carrito); ?> productos):</span>
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

                <?php if ($subtotal < 1000 && $subtotal > 0): ?>
                    <p class="nota-envio">
                        💡 Agrega $<?php echo number_format(1000 - $subtotal, 2); ?> más para obtener envío gratis
                    </p>
                <?php endif; ?>
                
                <div class="resumen-linea total">
                    <span>TOTAL:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>

                <!-- Botones de acción -->
                <div class="botones-accion">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="checkout.php" class="btn-primario">Proceder al pago</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-primario">Iniciar sesión para comprar</a>
                    <?php endif; ?>
                    
                    <a href="catalogo.php" class="btn-secundario">Seguir comprando</a>
                    
                    <form action="carrito.php" method="POST">
                        <input type="hidden" name="accion" value="vaciar">
                        <button type="submit" class="btn-danger" onclick="return confirm('¿Vaciar todo el carrito?')">
                            Vaciar carrito
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
