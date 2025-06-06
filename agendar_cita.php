<?php
include 'conexion.php'; // Asegúrate de que este archivo configura la conexión PDO como $pdo.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos enviados por el formulario
    $id_medico = $_POST['id_medico'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $paciente_nombre = $_POST['paciente_nombre'];
    $paciente_email = $_POST['paciente_email'];
    $descripcion = $_POST['descripcion'];

    // Validar los datos
    if (empty($id_medico) || empty($fecha) || empty($hora)) {
        $error = "Por favor, rellena todos los campos obligatorios.";
    } else {
        try {
            // Insertar la cita en la base de datos
            $query = "INSERT INTO citas (id_medico, fecha, hora, paciente_nombre, paciente_email, descripcion) 
                      VALUES (:id_medico, :fecha, :hora, :paciente_nombre, :paciente_email, :descripcion)";
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':id_medico' => $id_medico,
                ':fecha' => $fecha,
                ':hora' => $hora,
                ':paciente_nombre' => $paciente_nombre,
                ':paciente_email' => $paciente_email,
                ':descripcion' => $descripcion
            ]);

            $success = "Cita agendada con éxito.";
        } catch (PDOException $e) {
            $error = "Error al agendar la cita: " . $e->getMessage();
        }
    }
}

// Obtener el id del médico desde la URL (por ejemplo, ?id=1)
$id_medico = $_GET['id'] ?? null;

// Consultar datos del médico
if ($id_medico) {
    try {
        $query_medico = "SELECT * FROM medicos WHERE id = :id";
        $stmt_medico = $pdo->prepare($query_medico);
        $stmt_medico->execute([':id' => $id_medico]);
        $medico = $stmt_medico->fetch(PDO::FETCH_ASSOC);

        if (!$medico) {
            die("Médico no encontrado.");
        }

        // Consultar citas del médico
        $query_citas = "SELECT * FROM citas WHERE id_medico = :id_medico";
        $stmt_citas = $pdo->prepare($query_citas);
        $stmt_citas->execute([':id_medico' => $id_medico]);
        $result_citas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener datos: " . $e->getMessage());
    }
} else {
    die("ID del médico no proporcionado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos CSS aquí */
    </style>
</head>
<body>
    <div class="container">
        <h1>Agendar Cita para <?php echo htmlspecialchars($medico['nombre']); ?></h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="agendar_cita.php?id=<?php echo $id_medico; ?>" method="POST">
            <input type="hidden" name="id_medico" value="<?php echo $id_medico; ?>">
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>
            </div>
            <div class="form-group">
                <label for="paciente_nombre">Nombre del Paciente:</label>
                <input type="text" id="paciente_nombre" name="paciente_nombre" required>
            </div>
            <div class="form-group">
                <label for="paciente_email">Email del Paciente:</label>
                <input type="email" id="paciente_email" name="paciente_email">
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción o Motivo:</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>
            <button type="submit">Agendar Cita</button>
        </form>

        <div class="citas-list">
            <h2>Citas Agendadas</h2>
            <?php if (!empty($result_citas)): ?>
                <?php foreach ($result_citas as $cita): ?>
                    <div class="cita-item">
                        <strong>Fecha:</strong> <?php echo htmlspecialchars($cita['fecha']); ?><br>
                        <strong>Hora:</strong> <?php echo htmlspecialchars($cita['hora']); ?><br>
                        <strong>Paciente:</strong> <?php echo htmlspecialchars($cita['paciente_nombre']); ?><br>
                        <strong>Motivo:</strong> <?php echo htmlspecialchars($cita['descripcion']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay citas agendadas.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.5rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #3498db;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #2980b9;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-size: 1rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .citas-list {
            margin-top: 2rem;
        }
        .cita-item {
            background: #f9f9f9;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agendar Cita para <?php echo htmlspecialchars($medico['nombre']); ?></h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="agendar_cita.php?id=<?php echo $id_medico; ?>" method="POST">
            <input type="hidden" name="id_medico" value="<?php echo $id_medico; ?>">
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" id="hora" name="hora" required>
            </div>
            <div class="form-group">
                <label for="paciente_nombre">Nombre del Paciente:</label>
                <input type="text" id="paciente_nombre" name="paciente_nombre" required>
            </div>
            <div class="form-group">
                <label for="paciente_email">Email del Paciente:</label>
                <input type="email" id="paciente_email" name="paciente_email">
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción o Motivo:</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>
            <button type="submit">Agendar Cita</button>
        </form>

        <div class="citas-list">
            <h2>Citas Agendadas</h2>
            <?php if ($result_citas->num_rows > 0): ?>
                <?php while ($cita = $result_citas->fetch_assoc()): ?>
                    <div class="cita-item">
                        <strong>Fecha:</strong> <?php echo htmlspecialchars($cita['fecha']); ?><br>
                        <strong>Hora:</strong> <?php echo htmlspecialchars($cita['hora']); ?><br>
                        <strong>Paciente:</strong> <?php echo htmlspecialchars($cita['paciente_nombre']); ?><br>
                        <strong>Motivo:</strong> <?php echo htmlspecialchars($cita['descripcion']); ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay citas agendadas.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
