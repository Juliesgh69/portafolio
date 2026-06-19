<?php
require_once __DIR__ . '/config.php';
// $conexion viene de config.php

$mensaje = "";
$mostrarFormulario = true;

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conexion->prepare("SELECT * FROM reservas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        $mensaje = "Reserva no encontrada.";
        $mostrarFormulario = false;
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id             = intval($_POST["id"]);
    $nombre_cliente = $_POST["nombre_cliente"];
    $moneda_tienes  = $_POST["moneda_tienes"];
    $cantidad_tienes = $_POST["cantidad_tienes"];
    $moneda_quieres = $_POST["moneda_quieres"];
    $lugar_retiro   = $_POST["lugar_retiro"];
    $metodo_pago    = $_POST["metodo_pago"];

    $stmt = $conexion->prepare(
        "UPDATE reservas SET nombre_cliente=?, moneda_tienes=?, cantidad_tienes=?,
         moneda_quieres=?, lugar_retiro=?, metodo_pago=? WHERE id=?"
    );
    $stmt->bind_param('ssdsssi',
        $nombre_cliente, $moneda_tienes, $cantidad_tienes,
        $moneda_quieres, $lugar_retiro, $metodo_pago, $id
    );

    if ($stmt->execute()) {
        $mensaje = "✓ Reserva actualizada con éxito";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva — GloboCambio</title>
    <link rel="stylesheet" href="globocambio.css">
</head>
<body style="background-image: url('oficina.jpg')">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
        <nav>
            <a href="index.php">Nueva reserva</a>
            <a href="listar.php">Mis reservas</a>
        </nav>
    </header>

    <main class="main">
        <div class="card">

            <?php if ($mensaje !== ""): ?>
                <p class="msg"><?= $mensaje ?></p>
                <div class="actions">
                    <a href="listar.php" class="btn btn--primary">Volver a la lista</a>
                </div>
            <?php endif; ?>

            <?php if ($mostrarFormulario): ?>
            <h1 class="card__title">Editar reserva</h1>
            <form class="form" method="POST" action="editar.php">
                <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                <div class="form__grid">
                    <div class="field">
                        <label for="nombre_cliente">Nombre del cliente</label>
                        <input type="text" name="nombre_cliente" id="nombre_cliente"
                               value="<?= htmlspecialchars($fila['nombre_cliente']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="metodo_pago">Método de pago</label>
                        <input type="text" name="metodo_pago" id="metodo_pago"
                               value="<?= htmlspecialchars($fila['metodo_pago']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="moneda_tienes">Moneda que tienes</label>
                        <input type="text" name="moneda_tienes" id="moneda_tienes"
                               value="<?= htmlspecialchars($fila['moneda_tienes']) ?>" required>
                    </div>
                    <div class="field">
                        <label for="cantidad_tienes">Cantidad que tienes</label>
                        <input type="number" step="0.01" name="cantidad_tienes" id="cantidad_tienes"
                               value="<?= $fila['cantidad_tienes'] ?>" required>
                    </div>
                    <div class="field field--span2">
                        <label for="moneda_quieres">Moneda que quieres recibir</label>
                        <input type="text" name="moneda_quieres" id="moneda_quieres"
                               value="<?= htmlspecialchars($fila['moneda_quieres']) ?>" required>
                    </div>
                    <div class="field field--span2">
                        <label for="lugar_retiro">Lugar de retiro</label>
                        <select name="lugar_retiro" id="lugar_retiro" required>
                            <option value="Aeropuerto Vallarta"
                                <?= $fila['lugar_retiro'] === 'Aeropuerto Vallarta' ? 'selected' : '' ?>>
                                Aeropuerto Vallarta
                            </option>
                            <option value="Aeropuerto Guadalajara"
                                <?= $fila['lugar_retiro'] === 'Aeropuerto Guadalajara' ? 'selected' : '' ?>>
                                Aeropuerto Guadalajara
                            </option>
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn--primary">Actualizar reserva →</button>
                    <a href="listar.php" class="btn btn--ghost">Cancelar</a>
                </div>
            </form>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>
