<?php
/**
 * BALUT DECO - Página principal (Home)
 * Muestra productos destacados y navegación principal
 */

// Incluir archivo de conexión
require_once 'db.php';

// Consultar productos destacados (últimos 6 productos agregados)
$query = "SELECT * FROM productos ORDER BY created_at DESC LIMIT 6";
$resultado = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balut Deco - Decoración moderna y minimalista</title>
    <meta name="description" content="Tienda de decoración: velas, cerámica y pósters decorativos con estilo minimalista">

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Hoja de estilos principal (global) -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Estilos específicos del HOME -->
    <link rel="stylesheet" href="css/home.css">
</head>
<body>

<div class="home-container">
    <!-- HEADER: Logo, navegación y búsqueda -->
    <header class="header-home">
        <!-- Logo -->
        <h1><a href="index.php">Balut Deco</a></h1>

        <!-- Navegación principal -->
        <nav>
            <a href="index.php">Inicio</a>
            <a href="catalogo.php">Catálogo</a>
            <a href="carrito.php">Carrito</a>
            <a href="login.php">Iniciar Sesión</a>
        </nav>

        <!-- Barra de búsqueda -->
        <form class="home-search" action="catalogo.php" method="GET">
            <input
                type="search"
                name="buscar"
                placeholder="¿Qué deseas comprar hoy?"
                aria-label="Buscar productos"
            >
            <button type="submit">Buscar</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <!-- Sección Hero / Destacado -->
        <section class="home-section home-hero">
            <h2>Decoración que transforma espacios</h2>
            <p>Descubre nuestra colección de velas, cerámica y pósters minimalistas</p>
            <a class="home-cta" href="catalogo.php">Explorar catálogo →</a>
        </section>

        <!-- Categorías principales -->
        <section class="home-section">
            <h2>Categorías</h2>
            <div class="categorias-grid">
                <article class="categoria-card">
                    <h3>Velas</h3>
                    <p>Aromáticas y decorativas</p>
                    <a href="catalogo.php?categoria=velas">Ver productos</a>
                </article>
                <article class="categoria-card">
                    <h3>Cerámica</h3>
                    <p>Jarrones y piezas únicas</p>
                    <a href="catalogo.php?categoria=ceramica">Ver productos</a>
                </article>
                <article class="categoria-card">
                    <h3>Pósters</h3>
                    <p>Arte para tus paredes</p>
                    <a href="catalogo.php?categoria=posters">Ver productos</a>
                </article>
            </div>
        </section>

        <!-- Productos destacados (traídos desde la base de datos) -->
        <section class="home-section">
            <h2>Productos destacados</h2>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
                <!-- Grid de productos -->
                <div class="productos-grid">
                    <?php while ($producto = mysqli_fetch_assoc($resultado)): ?>
                        <!-- Card de producto -->
                        <article class="producto-card">
                            <!-- Imagen del producto -->
                            <?php if (!empty($producto['imagen']) && file_exists($producto['imagen'])): ?>
                                <img 
                                    src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                    alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                >
                            <?php else: ?>
                                <img 
                                    src="uploads/placeholder.jpg" 
                                    alt="Imagen no disponible"
                                >
                            <?php endif; ?>

                            <!-- Información del producto -->
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="categoria"><?php echo htmlspecialchars($producto['categoria']); ?></p>
                            <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                            
                            <!-- Botones de acción -->
                            <div class="acciones">
                                <a href="producto.php?id=<?php echo $producto['id']; ?>">Ver detalles</a>

                                <!-- ✅ Formulario corregido para que hable con carrito.php -->
                                <form action="carrito.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <input type="hidden" name="accion" value="agregar">
                                    <button type="submit" class="btn-primario">Añadir al carrito</button>
                                </form>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <!-- Mensaje si no hay productos -->
                <p>No hay productos disponibles en este momento. Por favor, vuelve más tarde.</p>
                <p><a href="admin/index.php">Administrar productos</a> (acceso para administradores)</p>
            <?php endif; ?>
        </section>

        <!-- Sección informativa: métodos de pago -->
        <section class="home-section">
            <h2>Métodos de pago aceptados</h2>
            <div class="pago-iconos">
                <div class="pago-item">
                    <span>💳</span>
                    <p>Mastercard</p>
                </div>
                <div class="pago-item">
                    <span>💳</span>
                    <p>VISA</p>
                </div>
                <div class="pago-item">
                    <span>💰</span>
                    <p>PayPal</p>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- FOOTER: Información de contacto y enlaces -->
<footer class="footer-home">
    <div class="footer-inner">
        <div class="footer-container">
            <!-- Información de contacto -->
            <div class="footer-section">
                <h3>Contacto</h3>
                <p>Email: contacto@balutdeco.com</p>
                <p>Teléfono: +52 123 456 7890</p>
                <p>San José del Valle, Nayarit, México</p>
            </div>

            <!-- Enlaces rápidos -->
            <div class="footer-section">
                <h3>Enlaces rápidos</h3>
                <ul>
                    <li><a href="catalogo.php">Catálogo</a></li>
                    <li><a href="login.php">Mi cuenta</a></li>
                    <li><a href="carrito.php">Carrito</a></li>
                </ul>
            </div>

            <!-- Información legal -->
            <div class="footer-section">
                <h3>Información</h3>
                <ul>
                    <li><a href="#">Términos y condiciones</a></li>
                    <li><a href="#">Política de privacidad</a></li>
                    <li><a href="#">Envíos y devoluciones</a></li>
                </ul>
            </div>

            <!-- Enlace a administración (CRUD) -->
            <div class="footer-section">
                <h3>Administración</h3>
                <p><a href="admin/index.php">Panel de control</a></p>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>&copy; 2025 Balut Deco. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Bottom Navigation (Mobile-first) -->
<nav class="bottom-nav">
    <a href="index.php">
        <span>🏠</span>
        <span>Inicio</span>
    </a>
    <a href="#">
        <span>❤️</span>
        <span>Favoritos</span>
    </a>
    <a href="catalogo.php">
        <span>📦</span>
        <span>Catálogo</span>
    </a>
    <a href="carrito.php">
        <span>🛒</span>
        <span>Carrito</span>
    </a>
    <a href="login.php">
        <span>👤</span>
        <span>Perfil</span>
    </a>
</nav>

</body>
</html>
<?php
// Cerrar conexión
mysqli_close($conn);
?>
