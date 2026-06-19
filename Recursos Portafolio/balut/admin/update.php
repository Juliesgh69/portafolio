<?php
/**
 * BALUT DECO - Actualizar producto
 * Procesa el formulario de edición y actualiza en la base de datos
 */

session_start();
require_once __DIR__ . '/../db.php';

// Proteger admin: solo usuarios logueados pueden actualizar productos
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verificar que se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener datos del formulario
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $imagen_actual = isset($_POST['imagen_actual']) ? $_POST['imagen_actual'] : '';
    
    // Validar campos obligatorios
    if ($id <= 0 || empty($nombre) || empty($categoria) || $precio <= 0) {
        header("Location: edit.php?id=$id&error=Todos los campos obligatorios deben estar completos");
        exit();
    }
    
    // Por defecto mantener la imagen actual
    $imagen_ruta = $imagen_actual;
    
    // Verificar si se subió una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $imagen_temp  = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_size  = $_FILES['imagen']['size'];
        $imagen_tipo  = $_FILES['imagen']['type'];
        
        // Validar tipo de archivo
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($imagen_tipo, $tipos_permitidos)) {
            header("Location: edit.php?id=$id&error=Formato de imagen no permitido");
            exit();
        }
        
        // Validar tamaño (máximo 5MB)
        if ($imagen_size > 5 * 1024 * 1024) {
            header("Location: edit.php?id=$id&error=La imagen no debe superar 5MB");
            exit();
        }
        
        // Generar nombre único para la nueva imagen
        $extension = pathinfo($imagen_nombre, PATHINFO_EXTENSION);
        $nombre_unico = uniqid('producto_') . '.' . $extension;
        
        // Ruta de destino (segura con __DIR__)
        $directorio_destino = __DIR__ . '/../uploads/';
        $ruta_completa = $directorio_destino . $nombre_unico;
        
        // Crear directorio si no existe
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }
        
        // Mover archivo
        if (move_uploaded_file($imagen_temp, $ruta_completa)) {
            // Eliminar imagen anterior si existe físicamente
            if (!empty($imagen_actual) && file_exists(__DIR__ . '/../' . $imagen_actual)) {
                unlink(__DIR__ . '/../' . $imagen_actual);
            }
            
            // Guardar nueva ruta relativa
            $imagen_ruta = 'uploads/' . $nombre_unico;
        } else {
            header("Location: edit.php?id=$id&error=Error al subir la imagen");
            exit();
        }
    }
    
    // Actualizar producto en la base de datos
    $query = "UPDATE productos 
              SET nombre = ?, categoria = ?, descripcion = ?, precio = ?, imagen = ? 
              WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssdsi", $nombre, $categoria, $descripcion, $precio, $imagen_ruta, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        // Redirigir con mensaje de éxito
        header("Location: index.php?mensaje=Producto actualizado exitosamente&tipo=success");
        exit();
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        header("Location: edit.php?id=$id&error=Error al actualizar el producto en la base de datos");
        exit();
    }
    
} else {
    // Si no se envió por POST, redirigir
    header("Location: index.php");
    exit();
}
?>
