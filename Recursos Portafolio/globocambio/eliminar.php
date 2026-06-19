<?php
$conexion = new mysqli("sql202.infinityfree.com", "if0_38951911", "globocambio666", "if0_38951911_globocambiomx");
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Reserva</title>
    <link rel="stylesheet" href="estiloglobo.css">
</head>
<body style="background-image: url('oficina.jpg');">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
    </header>

    <div class="container">
        <?php
        if (isset($_GET["id"])) {
            $id = $_GET["id"];

            $sql = "DELETE FROM reservas WHERE id = $id";

            if ($conexion->query($sql) === TRUE) {
                echo "<h2>Reserva eliminada con éxito</h2>";
            } else {
                echo "<h2>Error al eliminar: " . $conexion->error . "</h2>";
            }
        } else {
            echo "<h2>ID no especificado.</h2>";
        }

        $conexion->close();
        ?>

        <div class="botones">
            <a href="listar.php"><button>Volver a la lista</button></a>
        </div>
    </div>

</body>
</html>

