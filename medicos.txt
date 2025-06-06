<?php
// Configuración de conexión a la base de datos
$host = '127.0.0.1';
$dbname = 'clinica';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Manejo de rutas para la API
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $requestUri[0] ?? null;
$id = $requestUri[1] ?? null;

// API - Gestión de Médicos
if ($resource === 'api' && isset($requestUri[1]) && $requestUri[1] === 'medicos') {
    header('Content-Type: application/json; charset=utf-8');

    if ($requestMethod === 'GET') {
        if ($id) {
            // Obtener un médico por ID
            $stmt = $pdo->prepare('SELECT * FROM medicos WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $medico = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($medico) {
                echo json_encode($medico);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Médico no encontrado']);
            }
        } else {
            // Obtener todos los médicos o con filtros
            $filters = [];
            $sql = 'SELECT * FROM medicos WHERE 1=1';

            // Filtros opcionales
            if (!empty($_GET['profesion'])) {
                $sql .= ' AND profesion = :profesion';
                $filters[':profesion'] = $_GET['profesion'];
            }

            if (!empty($_GET['ubicacion'])) {
                $sql .= ' AND ubicacion = :ubicacion';
                $filters[':ubicacion'] = $_GET['ubicacion'];
            }

            if (!empty($_GET['calificacion'])) {
                $sql .= ' AND calificacion = :calificacion';
                $filters[':calificacion'] = (int)$_GET['calificacion'];
            }

            $stmt = $pdo->prepare($sql);
            foreach ($filters as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($medicos);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
    exit;
}

// Página principal con frontend existente
try {
    $total_medicos_query = $pdo->query('SELECT COUNT(*) FROM medicos');
    $total_medicos = $total_medicos_query->fetchColumn();

    $medicos_por_pagina = 8;
    $total_pages = ceil($total_medicos / $medicos_por_pagina);
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $medicos_por_pagina;

    $stmt = $pdo->prepare('SELECT * FROM medicos LIMIT :offset, :limit');
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $medicos_por_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Médica | Encuentra tu Especialista</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        /* Header Styles */
        .site-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .site-header h1 {
            font-weight: 700;
            color: white;
            margin: 0;
            font-size: 2.5rem;
        }

        /* Navbar Styles */
        .custom-navbar {
            background-color: rgba(255,255,255,0.95);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .custom-navbar .nav-link {
            color: var(--primary-color) !important;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .custom-navbar .nav-link:hover {
            color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        /* Filter Section */
        .filters-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 2rem 0;
        }

        .custom-select {
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .custom-select:focus {
            border-color: var(--secondary-color);
            box-shadow: none;
        }

        /* Doctor Cards */
        .doctor-card {
            background: white;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .doctor-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 1.5rem auto;
            border: 3px solid var(--secondary-color);
        }

        .doctor-info {
            padding: 1.5rem;
        }

        .doctor-name {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .doctor-specialty {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .rating {
            color: #ffc107;
            margin-bottom: 1rem;
        }

        .doctor-location {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }

        .doctor-contact {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn-custom {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-schedule {
            background-color: var(--secondary-color);
            color: white;
            border: none;
        }

        .btn-schedule:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-contact {
            background-color: white;
            color: var(--secondary-color);
            border: 2px solid var(--secondary-color);
        }

        .btn-contact:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        /* Pagination */
        .custom-pagination {
            margin: 2rem 0;
        }

        .page-link {
            color: var(--secondary-color);
            border: none;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 5px;
        }

        .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Footer */
        .site-footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        /* Animations */
        .animate-card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .filters-section {
                flex-direction: column;
            }
            
            .custom-select {
                margin-bottom: 1rem;
            }

            .doctor-card img {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container text-center">
            <h1 class="animate__animated animate__fadeInDown">Zona Médica</h1>
            <p class="text-white mt-2 animate__animated animate__fadeInUp">Encuentra al especialista ideal para tu salud</p>
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
                        <a class="nav-link" href="nosotros.html">
                            <i class="fas fa-users"></i> Nosotros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medicos.php">
                            <i class="fas fa-user-md"></i> Médicos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.html">
                            <i class="fas fa-envelope"></i> Contacto
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Filters -->
    <div class="container">
        <div class="filters-section d-flex justify-content-center align-items-center flex-wrap">
            <select class="custom-select mx-2" id="filtro-profesion" onchange="filtrarMedicos()">
                <option value="">Especialidad</option>
                <option value="cardiologo">Cardiólogo</option>
                <option value="psiquiatra">Psiquiatra</option>
                <option value="dermatologo">Dermatólogo</option>
            </select>
            <select class="custom-select mx-2" id="filtro-calificacion" onchange="filtrarMedicos()">
                <option value="">Calificación</option>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
            </select>
            <select class="custom-select mx-2" id="filtro-ubicacion" onchange="filtrarMedicos()">
                <option value="">Ubicación</option>
                <option value="san juan nepomuceno">San Juan Nepomuceno</option>
                <option value="cartagena">Cartagena</option>
                <option value="barranquilla">Barranquilla</option>
            </select>
        </div>

<!-- Grid de doctores con mejor proporción -->
<div class="container py-4">
    
    </div>

    <!-- Doctors Grid con mejor proporción y espaciado -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($medicos as $medico): ?>
        <div class="col medico-card" 
             data-profesion="<?= strtolower($medico['profesion']) ?>" 
             data-calificacion="<?= $medico['calificacion'] ?>" 
             data-ubicacion="<?= strtolower($medico['ubicacion']) ?>">
            <div class="doctor-card animate-card h-100">
                <div class="card-body d-flex flex-column p-3">
                    <div class="doctor-header text-center">
                        <div class="doctor-image-wrapper mb-3">
                            <img src="<?= $medico['foto'] ?>" 
                                 alt="<?= $medico['nombre'] ?>" 
                                 class="doctor-image">
                        </div>
                        <h3 class="doctor-name mb-1"><?= $medico['nombre'] ?></h3>
                        <div class="doctor-specialty mb-2">
                            <i class="fas fa-stethoscope me-1"></i><?= $medico['profesion'] ?>
                        </div>
                    </div>
                    
                    <div class="doctor-info">
                        <div class="doctor-details">
                            <div class="rating-wrapper mb-2">
                                <div class="rating me-2">
                                    <?php for($i = 0; $i < $medico['calificacion']; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-text"><?= $medico['calificacion'] ?>.0</span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span><?= $medico['ubicacion'] ?></span>
                            </div>
                            <div class="info-item mb-2">
                                <i class="fas fa-phone me-2"></i>
                                <span><?= $medico['telefono'] ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="doctor-actions mt-auto">
                    <button class="btn btn-schedule mb-2" onclick="window.location.href='agendar.php?id=<?= $medico['id'] ?>'">
    <i class="fas fa-calendar-alt me-2"></i> Agendar cita
</button>
    <div class="d-flex gap-2">
        <button class="btn btn-contact flex-grow-1">
            <i class="fas fa-comment-alt me-2"></i> Contactar
        </button>
        <a href="perfil.php?id=<?= $medico['id'] ?>" class="btn btn-profile flex-grow-1">
            <i class="fas fa-user me-2"></i> Ver perfil
        </a>
    </div>
</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>



<style>
:root {
    --primary-color: #2196F3;
    --secondary-color: #1976D2;
    --accent-color: #64B5F6;
    --text-primary: #333333;
    --text-secondary: #666666;
    --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    --hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    --border-radius: 12px;
}

.doctor-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
    width: 100%;
    max-width: 350px; /* Limitamos el ancho máximo */
    margin: 0 auto; /* Centramos las tarjetas */
}

.doctor-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.card-body {
    padding: 1.25rem !important;
}

.doctor-header {
    margin-bottom: 1rem;
}

.doctor-image-wrapper {
    width: 120px; /* Reducimos el tamaño de la imagen */
    height: 120px;
    margin: 0 auto;
}

.doctor-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--accent-color);
    transition: transform 0.3s ease;
}

.doctor-card:hover .doctor-image {
    transform: scale(1.05);
}

.doctor-name {
    font-size: 1.25rem;
    color: var(--text-primary);
    font-weight: 600;
}

.doctor-specialty {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.doctor-info {
    padding: 0.75rem 0;
}

.doctor-details {
    text-align: center;
}

.rating-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.rating {
    color: #ffc107;
    font-size: 0.9rem;
}

.rating-text {
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.9rem;
}

.info-item {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.doctor-actions {
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
}

.btn {
    width: 100%;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-schedule {
    background-color: var(--primary-color);
    color: white;
    border: none;
}

.btn-schedule:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
}

.btn-contact {
    background-color: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-contact:hover {
    background-color: rgba(33, 150, 243, 0.1);
    transform: translateY(-2px);
}

/* Ajustes responsive mejorados */
@media (min-width: 768px) {
    .row-cols-md-2 > * {
        flex: 0 0 auto;
        width: 50%;
    }
    
    .doctor-card {
        max-width: none; /* Permitimos que ocupe el ancho del col en pantallas medianas */
    }
}

@media (min-width: 992px) {
    .row-cols-lg-3 > * {
        flex: 0 0 auto;
        width: 33.33333%;
    }
}

@media (max-width: 767px) {
    .container {
        padding: 0.75rem;
    }
    
    .doctor-card {
        max-width: 320px;
    }
    
    .card-body {
        padding: 1rem !important;
    }
}


.btn-profile {
    background-color: transparent;
    color: var(--accent-color);
    border: 2px solid var(--accent-color);
}

.btn-profile:hover {
    background-color: var(--accent-color);
    color: white;
    transform: translateY(-2px);
}

.gap-2 {
    gap: 0.5rem;
}

</style>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p><i class="fas fa-phone"></i> +57 123 456 7890</p>
                    <p><i class="fas fa-envelope"></i> contacto@zonamedica.com</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text
                        