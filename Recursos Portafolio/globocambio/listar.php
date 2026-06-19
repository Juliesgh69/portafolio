<?php
$conexion = new mysqli("sql202.infinityfree.com", "if0_38951911", "globocambio666", "if0_38951911_globocambiomx");
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

$sql = "SELECT * FROM reservas";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Reservas</title>
    <link rel="stylesheet" href="estiloglobo.css">
</head>
<body style="background-image: url('oficina.jpg');">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
    </header>

    <div class="container">
        <h1>Reservas Guardadas</h1>

        <div class="botones">
            <a href="index.php"><button>Agregar nueva reserva</button></a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre del Cliente</th>
                <th>Moneda que tienes</th>
                <th>Cantidad que tienes</th>
                <th>Moneda que quieres</th>
                <th>Lugar de retiro</th>
                <th>Método de pago</th>
                <th>Fecha</th>
                <th style="width: 120px;">Acciones</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila["id"]; ?></td>
                    <td><?php echo $fila["nombre_cliente"]; ?></td>
                    <td><?php echo $fila["moneda_tienes"]; ?></td>
                    <td><?php echo $fila["cantidad_tienes"]; ?></td>
                    <td><?php echo $fila["moneda_quieres"]; ?></td>
                    <td><?php echo $fila["lugar_retiro"]; ?></td>
                    <td><?php echo $fila["metodo_pago"]; ?></td>
                    <td><?php echo $fila["fecha_reserva"]; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                        <a href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta reserva?');">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>