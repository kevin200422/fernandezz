<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = $_POST['rol'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $telefono = $_POST['telefono'] ?? null;
    $direccion = $_POST['direccion'] ?? null;

    try {
        if ($rol === 'medico') {
            // Subir archivos del médico
            $certificado = $_FILES['certificado']['name'];
            $foto = $_FILES['foto']['name'];

            move_uploaded_file($_FILES['certificado']['tmp_name'], "uploads/certificados/$certificado");
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/fotos/$foto");

            // Insertar datos en la tabla `medicos`
            $especialidad = $_POST['especialidad'];
            $stmt = $pdo->prepare('
                INSERT INTO medicos (nombre, email, password, especialidad, certificado, foto, telefono, direccion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$nombre, $email, $password, $especialidad, $certificado, $foto, $telefono, $direccion]);
        } else if ($rol === 'paciente') {
            // Insertar datos en la tabla `pacientes`
            $stmt = $pdo->prepare('
                INSERT INTO pacientes (nombre, email, password, telefono, direccion) 
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([$nombre, $email, $password, $telefono, $direccion]);
        }

        // Redireccionar a login.php
        header("Location: login.php");
        exit(); // Asegura que el script se detiene después de la redirección
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}
?>
