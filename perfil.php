<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Validar si se recibe el parámetro 'id' en la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Preparar la consulta con PDO
        $query = "SELECT * FROM medicos WHERE id = :id";
        $stmt = $pdo->prepare($query);

        // Asociar el parámetro y ejecutar la consulta
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener los resultados
        $medico = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validar si se encontraron datos
        if (!$medico) {
            echo "Médico no encontrado";
            exit;
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
} else {
    echo "ID de médico no proporcionado";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($medico['nombre']); ?> | Zona Médica</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Estilos CSS aquí */
        /* Agrega los estilos del código que ya compartiste */
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container text-center">
            <h1 class="animate__animated animate__fadeInDown">Zona Médica</h1>
            <p class="text-white mt-2 animate__animated animate__fadeInUp">Perfil Médico</p>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medicos.php">
                            <i class="fas fa-arrow-left"></i> Volver a Médicos
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <!-- Perfil Header -->
        <div class="profile-header animate__animated animate__fadeIn">
            <img src="<?php echo htmlspecialchars($medico['foto']); ?>" alt="<?php echo htmlspecialchars($medico['nombre']); ?>" class="profile-photo">
            <h1 class="doctor-name"><?php echo htmlspecialchars($medico['nombre']); ?></h1>
            <div class="doctor-specialty">
                <i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($medico['profesion']); ?>
            </div>
            <div class="rating-display">
                <?php for ($i = 0; $i < $medico['calificacion']; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
                <span class="ml-2"><?php echo htmlspecialchars($medico['calificacion']); ?>.0</span>
            </div>
        </div>

        <!-- Información Principal -->
        <div class="profile-section animate__animated animate__fadeIn">
            <h2 class="mb-4"><i class="fas fa-info-circle"></i> Información del Médico</h2>
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <strong>Correo:</strong><br>
                    <?php echo htmlspecialchars($medico['email']); ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <strong>Cédula Profesional:</strong><br>
                    <?php echo htmlspecialchars($medico['cedula']); ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <strong>Teléfono:</strong><br>
                    <?php echo htmlspecialchars($medico['telefono']); ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Ubicación:</strong><br>
                    <?php echo htmlspecialchars($medico['ubicacion']); ?>
                </div>
            </div>
        </div>

        <!-- Descripción -->
        <div class="description-section animate__animated animate__fadeIn">
            <h2 class="mb-4"><i class="fas fa-user-md"></i> Acerca del Doctor</h2>
            <p><?php echo htmlspecialchars($medico['descripcion']) ? htmlspecialchars($medico['descripcion']) : "No hay descripción disponible."; ?></p>
        </div>

        <div class="action-buttons">
            <a href="agendar_cita.php?id=<?php echo htmlspecialchars($medico['id']); ?>" class="btn-action btn-primary-action">
                <i class="fas fa-calendar-alt"></i> Agendar Cita
            </a>
            <a href="contactar.php?id=<?php echo htmlspecialchars($medico['id']); ?>" class="btn-action btn-secondary-action">
                <i class="fas fa-comment"></i> Contactar
            </a>
            <button onclick="dejarResena()" class="btn-action btn-secondary-action">
                <i class="fas fa-star"></i> Dejar Reseña
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function dejarResena() {
            alert('Próximamente: Sistema de reseñas');
        }
    </script>
</body>
</html>


<style> 
    /* Variables globales */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --accent-color: #e74c3c;
    --text-color: #2c3e50;
    --light-gray: #f5f6fa;
    --border-radius: 8px;
    --box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

/* Estilos generales */
body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: var(--text-color);
    background-color: var(--light-gray);
    margin: 0;
    padding: 0;
}

/* Header */
.site-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.site-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/* Navbar */
.custom-navbar {
    background-color: white;
    box-shadow: var(--box-shadow);
}

.custom-navbar .nav-link {
    color: var(--primary-color);
    font-weight: 500;
    padding: 1rem 1.5rem;
    transition: var(--transition);
}

.custom-navbar .nav-link:hover {
    color: var(--secondary-color);
    transform: translateY(-2px);
}

.custom-navbar .nav-link i {
    margin-right: 0.5rem;
}

/* Contenedor principal */
.profile-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Header del perfil */
.profile-header {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    box-shadow: var(--box-shadow);
    margin-bottom: 2rem;
}

.profile-photo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--secondary-color);
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.doctor-name {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.doctor-specialty {
    color: var(--secondary-color);
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.rating-display {
    color: #f1c40f;
    font-size: 1.2rem;
}

/* Secciones de información */
.profile-section, 
.description-section {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--box-shadow);
}

.profile-section h2,
.description-section h2 {
    color: var(--primary-color);
    font-size: 1.5rem;
    border-bottom: 2px solid var(--light-gray);
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}

/* Grid de información */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.info-item {
    padding: 1.5rem;
    background: var(--light-gray);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.info-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow);
}

.info-item i {
    font-size: 1.5rem;
    color: var(--secondary-color);
    margin-bottom: 1rem;
}

.info-item strong {
    display: block;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

/* Botones de acción */
.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.btn-action {
    padding: 1rem 2rem;
    border-radius: var(--border-radius);
    border: none;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-action i {
    margin-right: 0.5rem;
}

.btn-primary-action {
    background: var(--secondary-color);
    color: white;
}

.btn-primary-action:hover {
    background: #2980b9;
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.btn-secondary-action {
    background: white;
    color: var(--secondary-color);
    border: 2px solid var(--secondary-color);
}

.btn-secondary-action:hover {
    background: var(--secondary-color);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

/* Animaciones personalizadas */
.animate__animated {
    animation-duration: 0.8s;
}

/* Responsive */
@media (max-width: 768px) {
    .site-header h1 {
        font-size: 2rem;
    }

    .profile-photo {
        width: 150px;
        height: 150px;
    }

    .doctor-name {
        font-size: 1.5rem;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        grid-template-columns: 1fr;
    }
}

/* Modo oscuro */
@media (prefers-color-scheme: dark) {
    :root {
        --primary-color: #1a1a2e;
        --text-color: #e1e1e1;
        --light-gray: #16213e;
    }

    body {
        background-color: #0f3460;
    }

    .custom-navbar,
    .profile-header,
    .profile-section,
    .description-section {
        background-color: var(--primary-color);
    }

    .info-item {
        background-color: #16213e;
    }

    .btn-secondary-action {
        background-color: var(--primary-color);
    }
}
</style>