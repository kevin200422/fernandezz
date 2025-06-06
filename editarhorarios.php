<?php
require_once 'conexion.php';
session_start();

// Verificar si el usuario es médico
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'medico') {
    header('Location: ');
    exit;
}

// Código para gestionar horarios y citas
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Horarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h3>Gestión de Horarios</h3>
    <!-- Formulario para agregar horarios -->
</div>
</body>
</html>
