<?php
/**
 * BALUT DECO - Guardar nuevo producto
 * Procesa el formulario de creación y guarda en la base de datos
 */

session_start();
require_once __DIR__ . '/../db.php';

// Proteger admin: solo usuarios logueados pueden crear productos
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // Validación
    if (empty($nombre) || empty($categoria) || $precio <= 0) {
        header("Location: create.php?error=Todos los campos obligatorios deben estar completos");
        exit();
    }

    // Subida de imagen
    $imagen_ruta = '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $imagen_temp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_size = $_FILES['imagen']['size'];
        $imagen_tipo = $_FILES['imagen']['type'];

        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (!in_array($imagen_tipo, $tipos_permitidos)) {
            header("Location: create.php?error=Formato de imagen no permitido");
            exit();
        }

        if ($imagen_size > 5 * 1024 * 1024) {
            header("Location: create.php?error=La imagen no debe superar 5MB");
            exit();
        }

        $extension = pathinfo($imagen_nombre, PATHINFO_EXTENSION);
        $nombre_unico = uniqid('producto_') . '.' . $extension;

        $directorio_destino = __DIR__ . '/../uploads/';
        $ruta_completa = $directorio_destino . $nombre_unico;

        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        if (move_uploaded_file($imagen_temp, $ruta_completa)) {
            $imagen_ruta = 'uploads/' . $nombre_unico;
        } else {
            header("Location: create.php?error=Error al subir la imagen");
            exit();
        }

    } else {
        header("Location: create.php?error=Debes seleccionar una imagen");
        exit();
    }

    // Guardar producto en BD
    $query = "INSERT INTO productos (nombre, categoria, descripcion, precio, imagen) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssds", $nombre, $categoria, $descripcion, $precio, $imagen_ruta);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: index.php?mensaje=Producto creado exitosamente&tipo=success");
        exit();

    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // Borrar archivo si ya estaba subido pero falló el insert
        if (!empty($imagen_ruta) && file_exists(__DIR__ . '/../' . $imagen_ruta)) {
            unlink(__DIR__ . '/../' . $imagen_ruta);
        }

        header("Location: create.php?error=Error al guardar el producto en la base de datos");
        exit();
    }

} else {
    header("Location: create.php");
    exit();
}
?>
