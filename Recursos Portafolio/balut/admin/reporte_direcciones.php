<?php
/**
 * BALUT DECO - Reporte de direcciones
 * Lista direcciones de envío agrupadas por usuario
 */

session_start();
require_once '../db.php';

// (Opcional) Verificar que haya sesión iniciada (admin)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener todas las direcciones con datos del usuario
$query = "
    SELECT 
        d.id,
        d.usuario_id,
        d.destinatario,
        d.calle,
        d.ciudad,
        d.codigo_postal,
        d.telefono,
        d.created_at,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email
    FROM direcciones d
    INNER JOIN usuarios u ON d.usuario_id = u.id
    ORDER BY u.nombre, d.created_at DESC
";

$resultado = mysqli_query($conn, $query);
$direcciones = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_close($conn);

// Stats
$total_direcciones = count($direcciones);
$usuarios_con_direcciones = [];
foreach ($direcciones as $dir) {
    $usuarios_con_direcciones[] = $dir['usuario_id'];
}
$usuarios_con_direcciones = array_unique($usuarios_con_direcciones);
$total_usuarios_con_direcciones = count($usuarios_con_direcciones);
$promedio_direcciones = $total_usuarios_con_direcciones > 0
    ? $total_direcciones / $total_usuarios_con_direcciones
    : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de direcciones - Balut Deco</title>

    <!-- CSS global -->
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
            min-width: 220px;
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
            font-size: 0.93rem;
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

        .usuario-label {
            font-weight: 600;
            color: #754d78;
        }
        .fecha-mini {
            font-size: 0.8rem;
            color: var(--text-muted);
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
    <h1>Panel de administración – Direcciones</h1>
    <nav>
        <a href="index.php">🏠 Inicio admin</a>
        <a href="reporte_usuarios.php">👥 Usuarios</a>
        <a href="reporte_inventario.php">📦 Inventario</a>
        <a href="reporte_direcciones.php">📍 Direcciones</a>
    </nav>
</header>

<main class="reporte-container">
    <h2>Reporte de direcciones de envío</h2>
    <p>Lista de direcciones asociadas a cada usuario registrado en la plataforma.</p>

    <div class="resumen-cajas">
        <div class="resumen-caja">
            Total de direcciones guardadas
            <strong><?php echo $total_direcciones; ?></strong>
        </div>
        <div class="resumen-caja">
            Usuarios con al menos una dirección
            <strong><?php echo $total_usuarios_con_direcciones; ?></strong>
        </div>
        <div class="resumen-caja">
            Promedio de direcciones por usuario
            <strong><?php echo number_format($promedio_direcciones, 2); ?></strong>
        </div>
    </div>

    <table class="reporte-tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Destinatario</th>
                <th>Dirección</th>
                <th>Ciudad</th>
                <th>CP</th>
                <th>Teléfono</th>
                <th>Registrada</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_direcciones === 0): ?>
                <tr>
                    <td colspan="8">No hay direcciones registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($direcciones as $d): ?>
                    <tr>
                        <td>#<?php echo $d['id']; ?></td>
                        <td>
                            <span class="usuario-label">
                                <?php echo htmlspecialchars($d['usuario_nombre']); ?>
                            </span><br>
                            <span class="fecha-mini">
                                <?php echo htmlspecialchars($d['usuario_email']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($d['destinatario']); ?></td>
                        <td><?php echo htmlspecialchars($d['calle']); ?></td>
                        <td><?php echo htmlspecialchars($d['ciudad']); ?></td>
                        <td><?php echo htmlspecialchars($d['codigo_postal']); ?></td>
                        <td><?php echo htmlspecialchars($d['telefono']); ?></td>
                        <td class="fecha-mini">
                            <?php echo date('d/m/Y', strtotime($d['created_at'])); ?>
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
