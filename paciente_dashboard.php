<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Obtener los datos del paciente logueado
try {
    $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        // Si no se encuentra el paciente, redirigir a la página de inicio
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die('Error al cargar el perfil: ' . $e->getMessage());
}

// Procesar la subida de la foto de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $foto = $_FILES['foto_perfil'];
    if ($foto['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . "_" . basename($foto['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;
        
        if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
            // Actualizar la ruta de la foto en la base de datos
            $stmt = $pdo->prepare('UPDATE pacientes SET foto = ? WHERE id = ?');
            $stmt->execute([$nombreArchivo, $_SESSION['usuario_id']]);
            header('Location: paciente_dashboard.php'); // Recargar la página
            exit;
        } else {
            $error = "Error al subir la foto.";
        }
    } else {
        $error = "No se pudo procesar la subida de la foto.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            background-color: #f9fafb;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: #2563eb;
            color: white;
            padding: 1.25rem;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar h1 i {
            font-size: 1.25rem;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-header {
            text-align: center;
            padding: 2.5rem;
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .profile-header:hover {
            transform: translateY(-5px);
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1.5rem;
            border: 4px solid #2563eb;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.05);
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }

        .profile-picture img:hover {
            filter: brightness(1.1);
        }

        .profile-header h2 {
            font-size: 1.75rem;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .upload-form {
            background: white;
            padding: 2rem;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .upload-form input[type="file"] {
            display: none;
        }

        .upload-label {
            background-color: #2563eb;
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .upload-label:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }

        .upload-form button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            font-weight: 500;
            margin-left: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-form button:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }

        .motivational-message {
            background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%);
            padding: 2.5rem;
            border-radius: 1.25rem;
            border: 1px solid #bfdbfe;
            text-align: center;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .motivational-message:hover {
            transform: translateY(-5px);
        }

        .motivational-message h3 {
            color: #2563eb;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .motivational-message p {
            color: #1f2937;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .motivational-message a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #2563eb;
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .motivational-message a:hover {
            background-color: #1e40af;
            transform: translateY(-2px);
        }

        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        footer {
            margin-top: auto;
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .profile-header {
                padding: 1.5rem;
            }

            .profile-picture {
                width: 120px;
                height: 120px;
            }

            .upload-form button {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
            }

            .upload-label {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>
            <i class="fas fa-user-circle"></i>
            Bienvenido, <?php echo htmlspecialchars($paciente['nombre']); ?>
        </h1>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <?php if (!empty($paciente['foto'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($paciente['foto']); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="Foto de perfil por defecto">
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($paciente['nombre']); ?></h2>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($paciente['email']); ?></p>
        </div>

        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <label class="upload-label" for="foto_perfil">
                    <i class="fas fa-camera"></i> Cambiar foto de perfil
                </label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                <button type="submit">
                    <i class="fas fa-check"></i> Guardar cambios
                </button>
            </form>
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="motivational-message">
            <h3><i class="fas fa-heart"></i> ¡Estamos aquí para ayudarte!</h3>
            <p>Encuentra al profesional de salud ideal para ti y agenda tu cita con solo unos clics.</p>
            <a href="medicos.php">
                <i class="fas fa-user-md"></i> Explorar médicos disponibles
            </a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Sistema Médico | Todos los derechos reservados</p>
    </footer>

    <script>
        // Actualizar vista previa de la imagen al seleccionar un archivo
        document.getElementById('foto_perfil').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture img').src = e.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</body>
</html>