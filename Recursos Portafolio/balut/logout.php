<?php
/**
 * BALUT DECO - Cerrar sesión
 * Destruye la sesión de forma segura y redirige al login
 */

session_start();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión por completo
session_destroy();

// Evitar que el navegador guarde la página anterior
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirigir
header("Location: login.php?logout=1");
exit();
