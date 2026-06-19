<?php
/**
 * BALUT DECO - Conexión a la base de datos
 * Manejo seguro y estándar de conexión MySQLi
 */

$servername = "sql100.infinityfree.com";
$username   = "if0_40335375";
$password   = "balutFree69";
$dbname     = "if0_40335375_balut_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Forzar UTF-8 (muy importante para nombres con acentos y emojis)
$conn->set_charset("utf8mb4");

// OPCIONAL: evitar warnings molestos
// mysqli_report(MYSQLI_REPORT_OFF);
?>
