<?php
/**
 * GloboCambio — Conexión a base de datos
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

$conexion = new mysqli(
    $env['DB_HOST'],
    $env['DB_USER'],
    $env['DB_PASS'],
    $env['DB_NAME']
);

if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}

$conexion->set_charset('utf8mb4');
