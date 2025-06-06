<?php
require_once 'conexion.php';
session_start();

// Verificar si el médico está autenticado
if (!isset($_SESSION['medico_id'])) {
    header('Location: ');
    exit;
}

$doctor_id = $_SESSION['medico_id'];

// Procesar agregar nuevo horario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_schedule') {
    $dia_semana = $_POST['dia_semana'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $intervalo_citas = $_POST['intervalo_citas'];
    $direccion = $_POST['direccion'];

    try {
        $stmt = $pdo->prepare('
            INSERT INTO horarios_medicos 
            (medico_id, dia_semana, hora_inicio, hora_fin, intervalo_citas, direccion, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ');
        $stmt->execute([
            $doctor_id, $dia_semana, $hora_inicio, $hora_fin, 
            $intervalo_citas, $direccion
        ]);
        $success = "Horario agregado exitosamente";
    } catch (PDOException $e) {
        $error = "Error al agregar horario: " . $e->getMessage();
    }
}

// Obtener horarios existentes
$stmt = $pdo->prepare('SELECT * FROM horarios_medicos WHERE medico_id = ?');
$stmt->execute([$doctor_id]);
$schedules = $stmt->fetchAll();

// Obtener citas pendientes
$stmt = $pdo->prepare('
    SELECT c.*, p.nombre as nombre_paciente 
    FROM citas c 
    JOIN pacientes p ON c.paciente_id = p.id 
    WHERE c.medico_id = ? AND c.estado = "Pendiente"
');
$stmt->execute([$doctor_id]);
$pending_appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Horarios - Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Agregar Nuevo Horario</div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_schedule">
                            <div class="mb-3">
                                <label>Día de la Semana</label>
                                <select name="dia_semana" class="form-control" required>
                                    <option value="Monday">Lunes</option>
                                    <option value="Tuesday">Martes</option>
                                    <option value="Wednesday">Miércoles</option>
                                    <option value="Thursday">Jueves</option>
                                    <option value="Friday">Viernes</option>
                                    <option value="Saturday">Sábado</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Hora de Inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Hora de Fin</label>
                                <input type="time" name="hora_fin" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Intervalo de Citas (minutos)</label>
                                <input type="number" name="intervalo_citas" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Dirección de Consulta</label>
                                <textarea name="direccion" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Horario</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Mis Horarios Actuales</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora Inicio</th>
                                    <th>Hora Fin</th>
                                    <th>Intervalo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($schedules as $schedule): ?>
                                <tr>
                                    <td><?= $schedule['dia_semana'] ?></td>
                                    <td><?= $schedule['hora_inicio'] ?></td>
                                    <td><?= $schedule['hora_fin'] ?></td>
                                    <td><?= $schedule['intervalo_citas'] ?> min</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">Citas Pendientes</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($pending_appointments as $appointment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appointment['nombre_paciente']) ?></td>
                                    <td><?= $appointment['fecha'] ?></td>
                                    <td><?= $appointment['hora_inicio'] ?></td>
                                    <td>
                                        <form method="POST" action="confirmar_cita.php">
                                            <input type="hidden" name="cita_id" value="<?= $appointment['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>