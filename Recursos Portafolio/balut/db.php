<?php
/**
 * BALUT DECO — Conexión a base de datos
 * Lee credenciales desde .env (nunca las pongas aquí en texto plano).
 *
 * ⚠️ TODO: Rotar contraseña DB en panel InfinityFree — las credenciales
 *           anteriores estuvieron expuestas en el código fuente.
 */

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die('Error: falta el archivo .env con las credenciales de base de datos.');
}

$env = parse_ini_file($envFile);

$servername = $env['DB_HOST'];
$username   = $env['DB_USER'];
$password   = $env['DB_PASS'];
$dbname     = $env['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
