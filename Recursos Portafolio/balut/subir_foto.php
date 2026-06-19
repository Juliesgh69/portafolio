<?php
session_start();
require_once 'db.php';

// Si no hay usuario logueado → fuera
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Validar que sí viene archivo
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
    header("Location: perfil.php?error=1");
    exit();
}

// Carpeta donde guardaremos fotos
$carpeta = "uploads/fotos_perfil/";

if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Nombre único
$extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$nombre_archivo = "perfil_" . $usuario_id . "_" . time() . "." . $extension;

$ruta_final = $carpeta . $nombre_archivo;

// Mover archivo
move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_final);

// Guardar en BD
$query = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $ruta_final, $usuario_id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: perfil.php?ok=1");
exit();
