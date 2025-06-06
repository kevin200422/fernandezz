<?php
require_once 'config.php';
session_start();

// Verificar si el usuario es médico
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'medico') {
    header('Location: login.php');
    exit;
}

// Código para mostrar horarios, citas pendientes, etc.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h3>Bienvenido, Médico</h3>
    <!-- Contenido de gestión de horarios y citas -->
</div>
</body>
</html>
