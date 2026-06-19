<?php
require_once __DIR__ . '/config.php';
// $conexion viene de config.php

$sql = "SELECT * FROM reservas ORDER BY fecha_reserva DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas Guardadas — GloboCambio</title>
    <link rel="stylesheet" href="globocambio.css">
</head>
<body style="background-image: url('oficina.jpg')">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
        <nav>
            <a href="index.php">Nueva reserva</a>
            <a href="listar.php" class="active">Mis reservas</a>
        </nav>
    </header>

    <main class="main">
        <div class="card card--wide">
            <h1 class="card__title">Reservas guardadas</h1>

            <div class="actions" style="max-width:260px; margin: 0 0 8px auto;">
                <a href="index.php" class="btn btn--primary">+ Nueva reserva</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Moneda origen</th>
                            <th>Cantidad</th>
                            <th>Moneda destino</th>
                            <th>Lugar de retiro</th>
                            <th>Método de pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $fila['id'] ?></td>
                            <td><?= htmlspecialchars($fila['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($fila['moneda_tienes']) ?></td>
                            <td><?= number_format($fila['cantidad_tienes'], 2) ?></td>
                            <td><?= htmlspecialchars($fila['moneda_quieres']) ?></td>
                            <td><?= htmlspecialchars($fila['lugar_retiro']) ?></td>
                            <td><?= htmlspecialchars($fila['metodo_pago']) ?></td>
                            <td><?= $fila['fecha_reserva'] ?></td>
                            <td>
                                <a href="editar.php?id=<?= $fila['id'] ?>" class="link-edit">Editar</a>
                                <a href="eliminar.php?id=<?= $fila['id'] ?>" class="link-del"
                                   onclick="return confirm('¿Eliminar esta reserva?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
