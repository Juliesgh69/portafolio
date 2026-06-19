<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Moneda</title>
    <link rel="stylesheet" href="formulario.css">
</head>
<body style="background-image: url('malecon.jpg');">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
    </header>

    <div class="container">
        <h1>Reserva de cambio de moneda</h1>
        <form action="guardar.php" method="POST">
            <label for="nombre_cliente">Nombre del cliente</label>
            <input type="text" name="nombre_cliente" id="nombre_cliente" required>

            <label for="moneda_tienes">¿Qué moneda tienes?</label>
            <input type="text" name="moneda_tienes" id="moneda_tienes" required>

            <label for="cantidad_tienes">¿Cuánto tienes?</label>
            <input type="number" step="0.01" name="cantidad_tienes" id="cantidad_tienes" required>

            <label for="moneda_quieres">¿Qué moneda quieres?</label>
            <input type="text" name="moneda_quieres" id="moneda_quieres" required>

            <label for="lugar_retiro">¿Dónde quieres retirar tu moneda?</label>
            <select name="lugar_retiro" id="lugar_retiro" required>
                <option value="" disabled selected>Selecciona una opción</option>
                <option value="Aeropuerto Vallarta">Aeropuerto Vallarta</option>
                <option value="Aeropuerto Guadalajara">Aeropuerto Guadalajara</option>
            </select>

            <label for="metodo_pago">Método de pago</label>
            <input type="text" name="metodo_pago" id="metodo_pago" required>

            <div class="botones">
                <button type="submit">Guardar reserva</button>
                <a href="listar.php"><button type="button">Ver reservas guardadas</button></a>
            </div>
        </form>
    </div>

</body>
</html>
