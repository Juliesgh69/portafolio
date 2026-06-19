<?php
/**
 * BALUT DECO - Editar producto
 * Formulario para editar un producto existente
 */

session_start();
require_once '../db.php';

// Proteger el admin (solo usuarios logueados)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener ID del producto
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($producto_id <= 0) {
    header("Location: index.php?mensaje=ID de producto no válido&tipo=error");
    exit();
}

// Obtener datos del producto
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $producto_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Producto inexistente
if (!$producto) {
    mysqli_close($conn);
    header("Location: index.php?mensaje=Producto no encontrado&tipo=error");
    exit();
}

mysqli_close($conn);

// Mensaje de error opcional
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Balut Deco</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .header {
            background-color: #C09BBC;
            color: white;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
        }
        .form-card {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .imagen-actual img {
            max-width: 200px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
        .btn {
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .btn-warning {
            background-color: #ffc107;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .botones {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>✏️ Editar Producto</h1>
    <p><a href="index.php" style="color:white;">← Volver al panel</a></p>
</div>

<div class="container">
    <div class="form-card">

        <h2>Modificar información del producto</h2>
        <p style="color:#666;">ID del producto: <strong>#<?php echo $producto['id']; ?></strong></p>

        <?php if (!empty($error)): ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="update.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto['imagen']); ?>">

            <!-- Nombre -->
            <div class="form-group">
                <label>Nombre <span class="required">*</span></label>
                <input type="text" name="nombre" required maxlength="150"
                       value="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label>Categoría <span class="required">*</span></label>
                <select name="categoria" required>
                    <option value="">-- Selecciona --</option>
                    <option value="velas" <?php echo $producto['categoria'] === 'velas' ? 'selected' : ''; ?>>Velas</option>
                    <option value="ceramica" <?php echo $producto['categoria'] === 'ceramica' ? 'selected' : ''; ?>>Cerámica</option>
                    <option value="posters" <?php echo $producto['categoria'] === 'posters' ? 'selected' : ''; ?>>Pósters</option>
                </select>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <!-- Precio -->
            <div class="form-group">
                <label>Precio (MXN) <span class="required">*</span></label>
                <input type="number" step="0.01" min="0" name="precio" required
                       value="<?php echo $producto['precio']; ?>">
            </div>

            <!-- Imagen actual -->
            <div class="form-group imagen-actual">
                <label>Imagen actual:</label>
                <?php if (!empty($producto['imagen']) && file_exists('../' . $producto['imagen'])): ?>
                    <p><?php echo basename($producto['imagen']); ?></p>
                    <img src="../<?php echo htmlspecialchars($producto['imagen']); ?>">
                <?php else: ?>
                    <p style="color:#999;">No hay imagen cargada</p>
                <?php endif; ?>
            </div>

            <!-- Nueva imagen -->
            <div class="form-group">
                <label>Cambiar imagen (opcional)</label>
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif">
            </div>

            <!-- Botones -->
            <div class="botones">
                <button type="submit" class="btn btn-warning">✓ Actualizar producto</button>
                <a href="index.php" class="btn btn-secondary">✕ Cancelar</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
