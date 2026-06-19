<?php
/**
 * BALUT DECO - Reporte de inventario
 * Lista productos, precios y stock
 */

session_start();
require_once '../db.php'; // ajusta la ruta si es necesario

// (Opcional) Verificar que haya sesión iniciada (admin)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener productos
$query = "SELECT id, nombre, categoria, precio, stock FROM productos ORDER BY categoria, nombre";
$resultado = mysqli_query($conn, $query);
$productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_close($conn);

// Stats
$total_productos = count($productos);
$total_stock = 0;
$valor_inventario = 0;
$precio_min = null;
$precio_max = null;
$suma_precios = 0;

foreach ($productos as $p) {
    $stock = isset($p['stock']) ? (int)$p['stock'] : 0;
    $precio = (float)$p['precio'];

    $total_stock += $stock;
    $valor_inventario += $precio * $stock;
    $suma_precios += $precio;

    if ($precio_min === null || $precio < $precio_min) {
        $precio_min = $precio;
    }
    if ($precio_max === null || $precio > $precio_max) {
        $precio_max = $precio;
    }
}

$precio_promedio = $total_productos > 0 ? $suma_precios / $total_productos : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de inventario - Balut Deco</title>

    <link rel="stylesheet" href="../css/styles.css">

    <style>
        .admin-header {
            background-color: var(--accent);
            color: #fff;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.6rem;
        }
        .admin-header nav a {
            color: #fdfdfd;
            margin-left: 15px;
            text-decoration: none;
            font-weight: 500;
        }
        .admin-header nav a:hover {
            text-decoration: underline;
        }

        .reporte-container {
            max-width: 1100px;
            margin: 25px auto 40px;
            background: #fff;
            border-radius: var(--radius);
            padding: 20px 22px 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-soft);
        }
        .reporte-container h2 {
            margin-top: 0;
            color: var(--accent);
        }

        .resumen-cajas {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }
        .resumen-caja {
            flex: 1;
            min-width: 200px;
            background: var(--bg-light);
            border-radius: 10px;
            padding: 12px 14px;
            border: 1px solid var(--border-soft);
            font-size: 0.95rem;
        }
        .resumen-caja strong {
            display: block;
            font-size: 1.15rem;
            margin-top: 4px;
        }

        .reporte-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.95rem;
        }
        .reporte-tabla th,
        .reporte-tabla td {
            border: 1px solid var(--border-soft);
            padding: 8px 10px;
            text-align: left;
        }
        .reporte-tabla th {
            background: #fce7ae;
        }
        .reporte-tabla tbody tr:nth-child(even) {
            background-color: #faf8fb;
        }

        .badge-bajo {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background-color: #f8d7da;
            color: #721c24;
            font-size: 0.75rem;
            margin-left: 4px;
        }

        .acciones-reporte {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .acciones-reporte button {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 999px;
        }

        @media (max-width: 768px) {
            .resumen-cajas {
                flex-direction: column;
            }
            .reporte-container {
                margin: 15px;
                padding: 15px;
            }
            .reporte-tabla {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <h1>Panel de administración – Inventario</h1>
    <nav>
        <a href="index.php">🏠 Inicio admin</a>
        <a href="reporte_usuarios.php">👥 Usuarios</a>
        <a href="reporte_inventario.php">📦 Inventario</a>
        <a href="reporte_direcciones.php">📍 Direcciones</a>
    </nav>
</header>

<main class="reporte-container">
    <h2>Reporte de inventario de productos</h2>
    <p>Resumen de los productos registrados, precios y existencias.</p>

    <div class="resumen-cajas">
        <div class="resumen-caja">
            Total de productos
            <strong><?php echo $total_productos; ?></strong>
        </div>
        <div class="resumen-caja">
            Unidades totales en stock
            <strong><?php echo $total_stock; ?></strong>
        </div>
        <div class="resumen-caja">
            Valor estimado del inventario
            <strong>$<?php echo number_format($valor_inventario, 2); ?></strong>
        </div>
        <div class="resumen-caja">
            Precio promedio
            <strong>$<?php echo number_format($precio_promedio, 2); ?></strong>
            <span style="font-size:0.8rem;display:block;margin-top:2px;">
                Mín: $<?php echo number_format($precio_min ?? 0, 2); ?> • Máx: $<?php echo number_format($precio_max ?? 0, 2); ?>
            </span>
        </div>
    </div>

    <table class="reporte-tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_productos === 0): ?>
                <tr>
                    <td colspan="5">No hay productos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                        <td>$<?php echo number_format($p['precio'], 2); ?></td>
                        <td>
                            <?php
                                $stock = isset($p['stock']) ? (int)$p['stock'] : 0;
                                echo $stock;
                                if ($stock <= 3) {
                                    echo ' <span class="badge-bajo">Bajo</span>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="acciones-reporte">
        <button type="button" onclick="window.print()">🖨️ Imprimir</button>
    </div>
</main>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
