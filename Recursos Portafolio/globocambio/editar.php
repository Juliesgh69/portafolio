<?php
require_once __DIR__ . '/config.php';
// $conexion viene de config.php

$mensaje = "";
$mostrarFormulario = true;

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "SELECT * FROM reservas WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        $mensaje = "Reserva no encontrada.";
        $mostrarFormulario = false;
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $nombre_cliente = $_POST["nombre_cliente"];
    $moneda_tienes = $_POST["moneda_tienes"];
    $cantidad_tienes = $_POST["cantidad_tienes"];
    $moneda_quieres = $_POST["moneda_quieres"];
    $lugar_retiro = $_POST["lugar_retiro"];
    $metodo_pago = $_POST["metodo_pago"];

    $sql = "UPDATE reservas SET 
                nombre_cliente='$nombre_cliente',
                moneda_tienes='$moneda_tienes',
                cantidad_tienes='$cantidad_tienes',
                moneda_quieres='$moneda_quieres',
                lugar_retiro='$lugar_retiro',
                metodo_pago='$metodo_pago'
            WHERE id=$id";

    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Reserva actualizada con éxito";
        $mostrarFormulario = false;
    } else {
        $mensaje = "Error: " . $conexion->error;
        $mostrarFormulario = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reserva</title>
    <link rel="stylesheet" href="formulario.css">
</head>
<body style="background-image: url('oficina.jpg');">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
    </header>

    <div class="container">
        <?php if ($mensaje !== ""): ?>
            <h2><?php echo $mensaje; ?></h2>
            <div class="botones">
                <a href="listar.php"><button>Volver a la lista</button></a>
            </div>
        <?php endif; ?>

        <?php if ($mostrarFormulario): ?>
        <h1>Editar Reserva</h1>
        <form method="POST" action="editar.php">
            <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

            <label for="nombre_cliente">Nombre del cliente</label>
            <input type="text" name="nombre_cliente" id="nombre_cliente" value="<?php echo $fila['nombre_cliente']; ?>" required>

            <label for="moneda_tienes">Moneda que tienes</label>
            <input type="text" name="moneda_tienes" id="moneda_tienes" value="<?php echo $fila['moneda_tienes']; ?>" required>

            <label for="cantidad_tienes">Cantidad que tienes</label>
            <input type="number" step="0.01" name="cantidad_tienes" id="cantidad_tienes" value="<?php echo $fila['cantidad_tienes']; ?>" required>

            <label for="moneda_quieres">Moneda que quieres</label>
            <input type="text" name="moneda_quieres" id="moneda_quieres" value="<?php echo $fila['moneda_quieres']; ?>" required>

            <label for="lugar_retiro">¿Dónde quieres retirar tu moneda?</label>
            <select name="lugar_retiro" id="lugar_retiro" required>
                <option value="Aeropuerto Vallarta" <?php if ($fila['lugar_retiro'] === 'Aeropuerto Vallarta') echo 'selected'; ?>>Aeropuerto Vallarta</option>
                <option value="Aeropuerto Guadalajara" <?php if ($fila['lugar_retiro'] === 'Aeropuerto Guadalajara') echo 'selected'; ?>>Aeropuerto Guadalajara</option>
            </select>

            <label for="metodo_pago">Método de pago</label>
            <input type="text" name="metodo_pago" id="metodo_pago" value="<?php echo $fila['metodo_pago']; ?>" required>

            <div class="botones">
                <button type="submit">Actualizar reserva</button>
                <a href="listar.php"><button type="button">Cancelar</button></a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
