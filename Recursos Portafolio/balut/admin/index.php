<?php
/**
 * BALUT DECO - Panel de Administración
 * Lista todos los productos con opciones de editar/eliminar
 */

session_start();
require_once '../db.php';

// Proteger el panel: solo usuarios logueados
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Mensaje de éxito/error si viene de otra página
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'success';

// Obtener todos los productos
$query = "SELECT * FROM productos ORDER BY created_at DESC";
$resultado = mysqli_query($conn, $query);
$productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Balut Deco</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .header {
            background-color: #C09BBC;
            color: white;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .header nav {
            margin-top: 10px;
        }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        .mensaje {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .acciones {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #78C3C9;
            color: white;
        }
        .btn-primary:hover {
            background-color: #5aa8ae;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 14px;
        }
        .tabla-productos {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .tabla-productos th {
            background-color: #C09BBC;
            color: white;
            padding: 15px;
            text-align: left;
        }
        .tabla-productos td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .tabla-productos tr:hover {
            background-color: #f8f9fa;
        }
        .tabla-productos img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        .sin-productos {
            text-align: center;
            padding: 50px;
            background-color: white;
            color: #666;
        }
        .categoria-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #FCE7AE;
            color: #856404;
            border-radius: 3px;
            font-size: 12px;
            text-transform: capitalize;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #C09BBC;
            font-size: 36px;
        }
        .stat-card p {
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>🛠️ Panel de Administración - Balut Deco</h1>
        <nav>
            <a href="index.php">📦 Productos</a>
            <a href="../index.php">🏠 Ver tienda</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="../perfil.php">👤 Mi Perfil</a>
                <a href="../logout.php">Cerrar sesión</a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Container principal -->
    <div class="container">
        
        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo count($productos); ?></h3>
                <p>Total de productos</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_unique(array_column($productos, 'categoria'))); ?></h3>
                <p>Categorías</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format(array_sum(array_column($productos, 'precio')), 2); ?></h3>
                <p>Valor inventario</p>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo htmlspecialchars($tipo); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Acciones -->
        <div class="acciones">
            <a href="create.php" class="btn btn-success">+ Agregar nuevo producto</a>
            <a href="../catalogo.php" class="btn btn-primary">Ver catálogo público</a>
        </div>

        <h2>Gestión de Productos</h2>

        <!-- Tabla de productos -->
        <?php if (count($productos) > 0): ?>
            <table class="tabla-productos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Fecha creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><strong>#<?php echo $producto['id']; ?></strong></td>
                            <td>
                                <?php if (!empty($producto['imagen']) && file_exists('../' . $producto['imagen'])): ?>
                                    <img src="../<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <?php else: ?>
                                    <img src="../uploads/placeholder.jpg" alt="Sin imagen">
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                                <?php if (!empty($producto['descripcion'])): ?>
                                    <br><small style="color: #666;"><?php echo substr(htmlspecialchars($producto['descripcion']), 0, 60); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="categoria-badge"><?php echo htmlspecialchars($producto['categoria']); ?></span>
                            </td>
                            <td>
                                <strong style="color: #C09BBC;">$<?php echo number_format($producto['precio'], 2); ?></strong>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($producto['created_at'])); ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $producto['id']; ?>" class="btn btn-warning btn-small">✏️ Editar</a>
                                <a href="delete.php?id=<?php echo $producto['id']; ?>" 
                                   class="btn btn-danger btn-small"
                                   onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                    🗑️ Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sin-productos">
                <h2>No hay productos registrados</h2>
                <p>Comienza agregando tu primer producto</p>
                <a href="create.php" class="btn btn-success" style="margin-top: 20px;">+ Agregar producto</a>
            </div>
        <?php endif; ?>

    </div>

    <footer style="background-color: #2B2B2B; color: white; padding: 20px; text-align: center; margin-top: 40px;">
        <p>&copy; 2025 Balut Deco - Panel de Administración</p>
    </footer>

</body>
</html>
