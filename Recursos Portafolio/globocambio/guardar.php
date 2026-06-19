<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
// $conexion viene de config.php

$nombre_cliente = $_POST["nombre_cliente"];
$moneda_tienes = $_POST["moneda_tienes"];
$cantidad_tienes = $_POST["cantidad_tienes"];
$moneda_quieres = $_POST["moneda_quieres"];
$lugar_retiro = $_POST["lugar_retiro"];
$metodo_pago = $_POST["metodo_pago"];

$sql = "INSERT INTO reservas (
    nombre_cliente, moneda_tienes, cantidad_tienes, moneda_quieres, lugar_retiro, metodo_pago
) VALUES (
    '$nombre_cliente', '$moneda_tienes', '$cantidad_tienes', '$moneda_quieres', '$lugar_retiro', '$metodo_pago'
)";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Guardada</title>
    <link rel="stylesheet" href="formulario.css">
</head>
<body style="background-image: url('malecon.jpg');">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
    </header>

    <div class="container">
        <?php
        if ($conexion->query($sql) === TRUE) {
            echo "<h2>Reserva guardada con éxito</h2>";
        } else {
            echo "<h2>Error al guardar: " . $conexion->error . "</h2>";
        }
        $conexion->close();
        ?>

        <div class="botones">
            <a href="index.php"><button>Hacer otra reserva</button></a>
            <a href="listar.php"><button>Ver reservas guardadas</button></a>
        </div>
    </div>
</body>
</html>
