<?php
/**
 * BALUT DECO - Crear nuevo producto
 * Formulario para agregar un producto al catálogo (Panel Admin)
 */

session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Balut Deco</title>

    <!-- Fuente Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS global -->
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        /* --- ESTILO EXCLUSIVO DEL PANEL ADMIN --- */

        .admin-header {
            background-color: #C09BBC;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .admin-header a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .admin-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .admin-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #754d78;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: #faf8fb;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        #preview {
            max-width: 300px;
            border-radius: 8px;
            margin-top: 8px;
            display: none;
        }

        /* Botones */
        .btn-guardar {
            background-color: #78C3C9;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-guardar:hover {
            background-color: #5aa8ae;
        }

        .btn-cancelar {
            background-color: #FFAD99;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 10px;
        }

        .btn-cancelar:hover {
            background-color: #ff9580;
        }

    </style>
</head>
<body>

    <!-- Encabezado Admin -->
    <header class="admin-header">
        <h1>➕ Crear Nuevo Producto</h1>
        <a href="index.php">← Volver al panel</a>
    </header>

    <!-- Contenedor principal -->
    <div class="admin-container">
        <div class="admin-card">

            <h2 style="color:#9d7a9e;">Información del producto</h2>
            <p style="color:#555;">Completa todos los campos para agregar un nuevo producto al catálogo.</p>

            <form action="store.php" method="POST" enctype="multipart/form-data">

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="150" placeholder="Ej: Vela de Soya 'Lavanda Soft'">
                </div>

                <!-- Categoría -->
                <div class="form-group">
                    <label for="categoria">Categoría *</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <option value="velas">Velas</option>
                        <option value="ceramica">Cerámica</option>
                        <option value="posters">Pósters</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe características, aromas, medidas, materiales, etc."></textarea>
                </div>

                <!-- Precio -->
                <div class="form-group">
                    <label for="precio">Precio (MXN) *</label>
                    <input type="number" id="precio" name="precio" min="0" step="0.01" required placeholder="0.00">
                </div>

                <!-- Imagen -->
                <div class="form-group">
                    <label for="imagen">Imagen del producto *</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/jpg,image/png,image/gif" required>
                    <img id="preview" alt="Vista previa">
                </div>

                <!-- Botones -->
                <button type="submit" class="btn-guardar">✓ Guardar producto</button>
                <a href="index.php" class="btn-cancelar">✕ Cancelar</a>

            </form>

        </div>
    </div>

    <script>
        // Vista previa de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('preview');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>
