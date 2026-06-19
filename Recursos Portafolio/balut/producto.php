<?php
/**
 * BALUT DECO - Detalle de producto
 */

session_start();
require_once 'db.php';

// Obtener ID del producto
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($producto_id <= 0) {
    header("Location: catalogo.php");
    exit();
}

// Consultar producto
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $producto_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Si no existe el producto, redirigir
if (!$producto) {
    header("Location: catalogo.php");
    exit();
}

// Productos relacionados
$query_relacionados = "SELECT * FROM productos WHERE categoria = ? AND id != ? LIMIT 3";
$stmt_relacionados = mysqli_prepare($conn, $query_relacionados);
mysqli_stmt_bind_param($stmt_relacionados, "si", $producto['categoria'], $producto_id);
mysqli_stmt_execute($stmt_relacionados);
$resultado_relacionados = mysqli_stmt_get_result($stmt_relacionados);
$productos_relacionados = mysqli_fetch_all($resultado_relacionados, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_relacionados);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Balut Deco</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS global -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- CSS específico -->
    <link rel="stylesheet" href="css/producto.css">
</head>

<body>

<!-- Breadcrumb -->
<div class="producto-page">

    <div class="producto-breadcrumb">
        <a href="catalogo.php">← Volver al catálogo</a>
    </div>

    <!-- Layout principal -->
    <div class="producto-layout">

        <!-- Tarjeta imagen -->
        <div class="producto-media-card">
            <?php if (!empty($producto['imagen']) && file_exists($producto['imagen'])): ?>
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <?php else: ?>
                <img src="uploads/placeholder.jpg" alt="Sin imagen">
            <?php endif; ?>
        </div>

        <!-- Tarjeta info -->
        <div class="producto-info-card">

            <span class="producto-categoria-tag">
                <?php echo htmlspecialchars($producto['categoria']); ?>
            </span>

            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>

            <p class="producto-subtitulo">Decoración premium Balut Deco</p>

            <p class="producto-precio">
                $<?php echo number_format($producto['precio'], 2); ?>
            </p>

            <!-- Badges opcionales -->
            <div class="producto-badges">
                <div class="producto-badge">Hecho a mano</div>
                <div class="producto-badge">Alta calidad</div>
            </div>

            <!-- Descripción -->
            <h3>Descripción</h3>
            <p>
                <?php 
                echo !empty($producto['descripcion']) 
                    ? nl2br(htmlspecialchars($producto['descripcion']))
                    : "Sin descripción disponible.";
                ?>
            </p>

            <!-- Acciones -->
            <form action="carrito.php" method="POST">
                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <input type="hidden" name="accion" value="agregar">

                <div class="producto-acciones">

                    <div class="producto-cantidad">
                        <label>Cant.</label>
                        <input type="number" name="cantidad" value="1" min="1" max="99">
                    </div>

                    <button type="submit" class="btn-primario">Añadir al carrito</button>

                    <a href="catalogo.php" class="btn-secundario">Seguir comprando</a>
                </div>
            </form>

            <!-- Tabs informativos -->
            <div class="producto-tabs">
                <div class="tab-pill"><span>Cuidados del producto</span> 📘</div>
                <div class="tab-pill"><span>Envíos y devoluciones</span> 🚚</div>
            </div>

        </div>
    </div>

    <!-- Relacionados -->
    <?php if (count($productos_relacionados) > 0): ?>
        <div class="producto-relacionados">
            <h2>Te puede gustar</h2>

            <div class="relacionados-grid">
                <?php foreach ($productos_relacionados as $rel): ?>
                    <div class="relacionado-card">
                        <a href="producto.php?id=<?php echo $rel['id']; ?>">
                            <img src="<?php echo !empty($rel['imagen']) && file_exists($rel['imagen']) ? $rel['imagen'] : 'uploads/placeholder.jpg'; ?>">
                        </a>

                        <h3><?php echo htmlspecialchars($rel['nombre']); ?></h3>

                        <p class="precio">
                            $<?php echo number_format($rel['precio'], 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<footer class="footer">
    <p>&copy; 2025 Balut Deco — Todos los derechos reservados.</p>
</footer>

</body>
</html>
