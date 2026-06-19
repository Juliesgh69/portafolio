<?php
/**
 * BALUT DECO - Catálogo de productos
 * Muestra todos los productos con búsqueda y filtros por categoría
 */

session_start();
require_once 'db.php';

// Parámetros de búsqueda y filtros
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

// Construir consulta con filtros
$query = "SELECT * FROM productos WHERE 1=1";
$params = [];
$types = '';

// Filtro de búsqueda
if (!empty($buscar)) {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $buscar_param = "%$buscar%";
    $params[] = $buscar_param;
    $params[] = $buscar_param;
    $types .= 'ss';
}

// Filtro de categoría
if (!empty($categoria)) {
    $query .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

// Preparar y ejecutar consulta
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Obtener categorías
$query_categorias = "SELECT DISTINCT categoria FROM productos ORDER BY categoria";
$resultado_categorias = mysqli_query($conn, $query_categorias);
$categorias = mysqli_fetch_all($resultado_categorias, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Balut Deco</title>

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/catalogo.css">
</head>

<body>

<div class="catalogo-container">

    <!-- Header -->
    <header>
        <h1><a href="index.php" style="text-decoration:none;color:white;">Balut Deco</a></h1>

        <nav>
            <a href="index.php">Inicio</a> | 
            <a href="catalogo.php">Catálogo</a> | 
            <a href="carrito.php">🛒 Carrito</a> | 
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="perfil.php">👤 Mi Perfil</a> | 
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Búsqueda y Filtros -->
    <section class="search-filters">
        <h2>Catálogo de productos</h2>
        
        <div class="search-bar">
            <form action="catalogo.php" method="GET">
                <input 
                    type="search" 
                    name="buscar" 
                    placeholder="¿Qué deseas comprar hoy?" 
                    value="<?php echo htmlspecialchars($buscar); ?>"
                >
                <button type="submit">🔍 Buscar</button>
            </form>
        </div>

        <div class="filtros">
            <strong>Filtrar por categoría:</strong>
            <a href="catalogo.php" class="<?php echo empty($categoria) ? 'active' : ''; ?>">Todas</a>

            <?php foreach ($categorias as $cat): ?>
                <a 
                    href="catalogo.php?categoria=<?php echo urlencode($cat['categoria']); ?>" 
                    class="<?php echo $categoria === $cat['categoria'] ? 'active' : ''; ?>"
                >
                    <?php echo ucfirst(htmlspecialchars($cat['categoria'])); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($buscar) || !empty($categoria)): ?>
            <p>
                <strong>Resultados:</strong> 
                <?php echo count($productos); ?> producto(s)
                <?php if (!empty($buscar)): ?>
                    para "<em><?php echo htmlspecialchars($buscar); ?></em>"
                <?php endif; ?>
                <?php if (!empty($categoria)): ?>
                    en categoría "<em><?php echo htmlspecialchars($categoria); ?></em>"
                <?php endif; ?>
                | <a href="catalogo.php">Limpiar filtros</a>
            </p>
        <?php endif; ?>
    </section>

    <!-- Productos -->
    <?php if (count($productos) > 0): ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <article class="producto-card">

                    <a href="producto.php?id=<?php echo $producto['id']; ?>">
                        <?php if (!empty($producto['imagen']) && file_exists($producto['imagen'])): ?>
                            <img 
                                src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                            >
                        <?php else: ?>
                            <img src="uploads/placeholder.jpg" alt="Imagen no disponible">
                        <?php endif; ?>
                    </a>

                    <p class="categoria"><?php echo htmlspecialchars($producto['categoria']); ?></p>

                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>

                    <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>

                    <a href="producto.php?id=<?php echo $producto['id']; ?>">Ver detalles</a>

                    <form action="carrito.php" method="POST" style="display:inline;">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <input type="hidden" name="cantidad" value="1">
                        <button class="btn-primario" type="submit">🛒 Añadir al carrito</button>
                    </form>

                </article>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="no-resultados">
            <h2>😔 No se encontraron productos</h2>
            <p>Intenta con otra búsqueda o categoría</p>
            <a href="catalogo.php">Ver todos los productos</a>
        </div>
    <?php endif; ?>

</div> <!-- cierre .catalogo-container -->

<!-- Footer -->
<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
