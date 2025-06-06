<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar y Gestionar Médicos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="agregar.css">

</head>
<body>
    <header class="header">
        <div class="menu container">
            <a href="#" class="logo">Zona Médica</a>
            <nav class="navbar">
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="nosotros.html">Nosotros</a></li>
                    <li><a href="medicos.php">Médicos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Agregar y Gestionar Médicos</h1>

        <!-- Formulario para agregar un médico -->
        <section>
            <h2>Agregar Médico</h2>
            <form method="POST" action="agregar_medico.php" enctype="multipart/form-data">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="profesion" placeholder="Especialidad" required>
                <input type="number" name="calificacion" placeholder="Calificación (1-5)" min="1" max="5" required>
                <textarea name="descripcion" placeholder="Descripción del médico" required></textarea>
                <input type="text" name="contacto" placeholder="Contacto" required>
                <input type="file" name="foto" required>
                <button type="submit" name="agregar">Agregar Médico</button>
            </form>
        </section>

        <?php
        include 'conexion.php';

        // Función para agregar un médico
        if (isset($_POST['agregar'])) {
            $nombre = $_POST['nombre'];
            $profesion = $_POST['profesion'];
            $calificacion = $_POST['calificacion'];
            $descripcion = $_POST['descripcion'];
            $contacto = $_POST['contacto'];


            $foto = $_FILES['foto']['name'];
            $foto_tmp = $_FILES['foto']['tmp_name'];
            $carpeta_destino = "uploads/";
            $ruta_foto = $carpeta_destino . basename($foto);

            // Mover la foto a la carpeta de destino
            if (move_uploaded_file($foto_tmp, $ruta_foto)) {
                $sql = "INSERT INTO medicos (nombre, profesion, calificacion, descripcion, contacto, foto) 
                        VALUES ('$nombre', '$profesion', $calificacion, '$descripcion', '$contacto', '$ruta_foto')";

                if ($conn->query($sql) === TRUE) {
                    echo "<p>Médico agregado exitosamente.</p>";
                } else {
                    echo "<p>Error al agregar el médico: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Error al cargar la foto.</p>";
            }
        }

        // Función para eliminar un médico
        if (isset($_GET['eliminar_id'])) {
            $id = $_GET['eliminar_id'];
            $sql = "DELETE FROM medicos WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                echo "<p>Médico eliminado exitosamente.</p>";
            } else {
                echo "<p>Error al eliminar el médico: " . $conn->error . "</p>";
            }
        }

        // Función para editar un médico
        if (isset($_POST['editar_id'])) {
            $id = $_POST['editar_id'];
            $nombre = $_POST['nombre'];
            $profesion = $_POST['profesion'];
            $calificacion = $_POST['calificacion'];
            $descripcion = $_POST['descripcion'];
            $contacto = $_POST['contacto'];

            // Verificar si se ha subido una nueva foto
            if (!empty($_FILES['foto']['name'])) {
                $foto = $_FILES['foto']['name'];
                $foto_tmp = $_FILES['foto']['tmp_name'];
                $ruta_foto = $carpeta_destino . basename($foto);
                move_uploaded_file($foto_tmp, $ruta_foto);

                $sql = "UPDATE medicos SET nombre='$nombre', profesion='$profesion', calificacion=$calificacion, descripcion='$descripcion', contacto='$contacto', foto='$ruta_foto' WHERE id=$id";
            } else {
                $sql = "UPDATE medicos SET nombre='$nombre', profesion='$profesion', calificacion=$calificacion, descripcion='$descripcion', contacto='$contacto' WHERE id=$id";
            }

            if ($conn->query($sql) === TRUE) {
                echo "<p>Médico actualizado exitosamente.</p>";
            } else {
                echo "<p>Error al actualizar el médico: " . $conn->error . "</p>";
            }
        }

        // Mostrar médicos con filtros
        $especialidad = isset($_GET['especialidad']) ? $_GET['especialidad'] : '';
        $calificacion = isset($_GET['calificacion']) ? $_GET['calificacion'] : '';

        $sql = "SELECT * FROM medicos WHERE 1=1";
        if ($especialidad != '') {
            $sql .= " AND profesion = '$especialidad'";
        }
        if ($calificacion != '') {
            $sql .= " AND calificacion = $calificacion";
        }

        $result = $conn->query($sql);
        ?>

        <!-- Mostrar lista de médicos -->
        <section>
            <h2>Lista de Médicos</h2>
            <div class="medicos-container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='medico-card'>";
                        echo "<img src='" . htmlspecialchars($row['foto']) . "' alt='Foto de " . htmlspecialchars($row['nombre']) . "' class='medico-foto'>";
                        echo "<div class='medico-info'>";
                        echo "<h3>" . htmlspecialchars($row['nombre']) . "</h3>";
                        echo "<p><strong>Especialidad:</strong> " . htmlspecialchars($row['profesion']) . "</p>";
                        echo "<p><strong>Calificación:</strong> " . htmlspecialchars($row['calificacion']) . " Estrellas</p>";
                        echo "<p><strong>Descripción:</strong> " . htmlspecialchars($row['descripcion']) . "</p>";
                        echo "<p><strong>Contacto:</strong> " . htmlspecialchars($row['contacto']) . "</p>";
                        echo "</div>";

                        // Formulario para editar y botón de eliminar
                        echo "<form method='POST' action='agregar_medico.php' enctype='multipart/form-data'>";
                        echo "<input type='hidden' name='editar_id' value='" . $row['id'] . "'>";
                        echo "<input type='text' name='nombre' value='" . htmlspecialchars($row['nombre']) . "' required>";
                        echo "<input type='text' name='profesion' value='" . htmlspecialchars($row['profesion']) . "' required>";
                        echo "<input type='number' name='calificacion' min='1' max='5' value='" . htmlspecialchars($row['calificacion']) . "' required>";
                        echo "<textarea name='descripcion' required>" . htmlspecialchars($row['descripcion']) . "</textarea>";
                        echo "<input type='text' name='contacto' value='" . htmlspecialchars($row['contacto']) . "' required>";
                        echo "<input type='file' name='foto'>";
                        echo "<button type='submit'>Guardar Cambios</button>";
                        echo "</form>";
                        echo "<a href='agregar_medico.php?eliminar_id=" . $row['id'] . "' class='delete-btn'>Eliminar</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No hay médicos disponibles.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content container">
            <p>&copy; 2024 Zona Médica. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
