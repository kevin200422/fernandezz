<?php
require_once 'conexion.php';

try {
    $stmt = $pdo->query('SELECT id, nombre, email, profesion, ubicacion, telefono, foto, calificacion FROM medicos');
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Error fetching doctors: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Citas Médicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hospital-user me-2"></i>
                Sistema de Citas Médicas
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-4 mb-4">
                <div class="card doctor-card h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="<?= htmlspecialchars($doctor['foto']) ?>" 
                                 alt="<?= htmlspecialchars($doctor['nombre']) ?>" 
                                 class="doctor-image">
                        </div>
                        <h5 class="card-title text-center"><?= htmlspecialchars($doctor['nombre']) ?></h5>
                        <p class="card-text text-center text-muted mb-2">
                            <i class="fas fa-stethoscope me-2"></i>
                            <?= htmlspecialchars($doctor['profesion']) ?>
                        </p>
                        <div class="doctor-rating text-center mb-3">
                            <?php for($i = 0; $i < $doctor['calificacion']; $i++): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="doctor-info">
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?= htmlspecialchars($doctor['ubicacion']) ?>
                            </p>
                            <p class="mb-3">
                                <i class="fas fa-phone me-2"></i>
                                <?= htmlspecialchars($doctor['telefono']) ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <a href="agendar.php?id=<?= $doctor['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Agendar Cita
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>