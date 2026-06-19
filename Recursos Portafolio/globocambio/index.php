<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Moneda — GloboCambio</title>
    <link rel="stylesheet" href="globocambio.css">
</head>
<body style="background-image: url('malecon.jpg')">

    <header>
        <img src="globocambio.png" alt="Logo GloboCambio" class="logo">
        <nav>
            <a href="index.php" class="active">Nueva reserva</a>
            <a href="listar.php">Mis reservas</a>
        </nav>
    </header>

    <main class="main">
        <div class="card">
            <h1 class="card__title">Reserva de cambio de moneda</h1>
            <form class="form" action="guardar.php" method="POST">
                <div class="form__grid">
                    <div class="field">
                        <label for="nombre_cliente">Nombre del cliente</label>
                        <input type="text" name="nombre_cliente" id="nombre_cliente"
                               placeholder="Tu nombre completo" required>
                    </div>
                    <div class="field">
                        <label for="metodo_pago">Método de pago</label>
                        <input type="text" name="metodo_pago" id="metodo_pago"
                               placeholder="Ej. Tarjeta, efectivo" required>
                    </div>
                    <div class="field">
                        <label for="moneda_tienes">¿Qué moneda tienes?</label>
                        <input type="text" name="moneda_tienes" id="moneda_tienes"
                               placeholder="Ej. USD, EUR, MXN" required>
                    </div>
                    <div class="field">
                        <label for="cantidad_tienes">¿Cuánto tienes?</label>
                        <input type="number" step="0.01" name="cantidad_tienes"
                               id="cantidad_tienes" placeholder="0.00" required>
                    </div>
                    <div class="field field--span2">
                        <label for="moneda_quieres">¿Qué moneda quieres recibir?</label>
                        <input type="text" name="moneda_quieres" id="moneda_quieres"
                               placeholder="Ej. USD, EUR, MXN" required>
                    </div>
                    <div class="field field--span2">
                        <label for="lugar_retiro">¿Dónde quieres retirar tu moneda?</label>
                        <select name="lugar_retiro" id="lugar_retiro" required>
                            <option value="" disabled selected>Selecciona una sucursal</option>
                            <option value="Aeropuerto Vallarta">Aeropuerto Vallarta</option>
                            <option value="Aeropuerto Guadalajara">Aeropuerto Guadalajara</option>
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn--primary">Guardar reserva →</button>
                    <a href="listar.php" class="btn btn--ghost">Ver reservas guardadas</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
