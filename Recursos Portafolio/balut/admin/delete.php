<?php
/**
 * BALUT DECO - Eliminar producto
 * Elimina un producto de la base de datos y su imagen asociada
 */

session_start();
require_once '../db.php';

// Obtener ID del producto
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($producto_id <= 0) {
    header("Location: index.php?mensaje=ID de producto no válido&tipo=error");
    exit();
}

// Obtener información del producto antes de eliminar (para borrar la imagen)
$query = "SELECT imagen FROM productos WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $producto_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$producto) {
    mysqli_close($conn);
    header("Location: index.php?mensaje=Producto no encontrado&tipo=error");
    exit();
}

// Eliminar producto de la base de datos
$query_delete = "DELETE FROM productos WHERE id = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $producto_id);

if (mysqli_stmt_execute($stmt_delete)) {
    // Eliminar imagen asociada si existe
    if (!empty($producto['imagen']) && file_exists('../' . $producto['imagen'])) {
        unlink('../' . $producto['imagen']);
    }
    
    mysqli_stmt_close($stmt_delete);
    mysqli_close($conn);
    
    // Redirigir con mensaje de éxito
    header("Location: index.php?mensaje=Producto eliminado exitosamente&tipo=success");
    exit();
} else {
    mysqli_stmt_close($stmt_delete);
    mysqli_close($conn);
    
    // Error al eliminar
    header("Location: index.php?mensaje=Error al eliminar el producto&tipo=error");
    exit();
}
?>