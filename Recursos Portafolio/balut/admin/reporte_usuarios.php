<?php
/**
 * BALUT DECO - Reporte de usuarios
 * Lista los usuarios registrados en el sistema
 */

session_start();
require_once '../db.php'; // ajusta la ruta si tu db.php está en otro lado

// (Opcional) Verificar que haya sesión iniciada
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener todos los usuarios
$query = "SELECT id, nombre, email, foto_perfil FROM usuarios ORDER BY id DESC";
$resultado = mysqli_query($conn, $query);
$usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

mysqli_close($conn);

// Estadísticas sencillas
$total_usuarios = count($usuarios);
$con_foto = 0;
foreach ($usuarios as $u) {
    if (!empty($u['foto_perfil'])) {
        $con_foto++;
    }
}
$sin_foto = $total_usuarios - $con_foto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de usuarios - Balut Deco</title>

    <!-- Estilos globales -->
    <link rel="stylesheet" href="../css/styles.css">

    <!-- Estilos específicos del reporte -->
    <style>
        .admin-header {
            background-color: var(--accent);
            color: #fff;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.6rem;
        }

        .admin-header nav a {
            color: #fdfdfd;
            margin-left: 15px;
            text-decoration: none;
            font-weight: 500;
        }

        .admin-header nav a:hover {
            text-decoration: underline;
        }

        .reporte-container {
            max-width: 1100px;
            margin: 25px auto 40px;
            background: #fff;
            border-radius: var(--radius);
            padding: 20px 22px 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-soft);
        }

        .reporte-container h2 {
            margin-top: 0;
            color: var(--accent);
        }

        .resumen-cajas {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .resumen-caja {
            flex: 1;
            min-width: 180px;
            background: var(--bg-light);
            border-radius: 10px;
            padding: 12px 14px;
            border: 1px solid var(--border-soft);
            font-size: 0.95rem;
        }

        .resumen-caja strong {
            display: block;
            font-size: 1.2rem;
            margin-top: 4px;
        }

        .reporte-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.95rem;
        }

        .reporte-tabla th,
        .reporte-tabla td {
            border: 1px solid var(--border-soft);
            padding: 8px 10px;
            text-align: left;
        }

        .reporte-tabla th {
            background: #fce7ae;
        }

        .reporte-tabla tbody tr:nth-child(even) {
            background-color: #faf8fb;
        }

        .avatar-mini {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .acciones-reporte {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .acciones-reporte button {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 999px;
        }

        @media (max-width: 768px) {
            .resumen-cajas {
                flex-direction: column;
            }

            .reporte-container {
                margin: 15px;
                padding: 15px;
            }

            .reporte-tabla {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <h1>Panel de administración – Reporte de usuarios</h1>
    <nav>
        <a href="index.php">🏠 Inicio admin</a>
        <a href="reporte_usuarios.php">👥 Usuarios</a>
        <a href="reporte_inventario.php">📦 Inventario</a>
        <a href="reporte_direcciones.php">📍 Direcciones</a>
    </nav>
</header>

<main class="reporte-container">
    <h2>Usuarios registrados</h2>
    <p>Este reporte muestra la lista de usuarios registrados en Balut Deco.</p>

    <!-- Resumen rápido -->
    <div class="resumen-cajas">
        <div class="resumen-caja">
            Total de usuarios
            <strong><?php echo $total_usuarios; ?></strong>
        </div>
        <div class="resumen-caja">
            Usuarios con foto de perfil
            <strong><?php echo $con_foto; ?></strong>
        </div>
        <div class="resumen-caja">
            Usuarios sin foto de perfil
            <strong><?php echo $sin_foto; ?></strong>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <table class="reporte-tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_usuarios === 0): ?>
                <tr>
                    <td colspan="4">No hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td>
                            <?php if (!empty($u['foto_perfil']) && file_exists('../'.$u['foto_perfil'])): ?>
                                <img src="<?php echo '../' . htmlspecialchars($u['foto_perfil']); ?>" class="avatar-mini" alt="Foto">
                            <?php else: ?>
                                <span style="font-size:0.8rem;color:var(--text-muted);">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Acciones del reporte -->
    <div class="acciones-reporte">
        <button type="button" onclick="window.print()">🖨️ Imprimir</button>
        <!-- Si después quieres CSV, aquí podrías poner un link a reporte_usuarios_csv.php -->
    </div>
</main>

<footer>
    <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
</footer>

</body>
</html>
