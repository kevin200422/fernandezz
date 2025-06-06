<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = '127.0.0.1';
$dbname = 'clinica';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Detectamos la ruta solicitada
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $endpoint = isset($uri[1]) ? $uri[1] : 'medicos';

    switch ($endpoint) {
        case 'medicos':
            // Paginación
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $medicos_por_pagina = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
            $offset = ($page - 1) * $medicos_por_pagina;

            // Filtros
            $where = [];
            if (!empty($_GET['especialidad'])) {
                $where[] = 'profesion = :especialidad';
            }
            if (!empty($_GET['calificacion'])) {
                $where[] = 'calificacion = :calificacion';
            }
            if (!empty($_GET['ubicacion'])) {
                $where[] = 'ubicacion = :ubicacion';
            }

            $sql = 'SELECT * FROM medicos';
            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }
            $sql .= ' LIMIT :offset, :limit';

            $stmt = $pdo->prepare($sql);

            if (!empty($_GET['especialidad'])) {
                $stmt->bindValue(':especialidad', $_GET['especialidad'], PDO::PARAM_STR);
            }
            if (!empty($_GET['calificacion'])) {
                $stmt->bindValue(':calificacion', $_GET['calificacion'], PDO::PARAM_INT);
            }
            if (!empty($_GET['ubicacion'])) {
                $stmt->bindValue(':ubicacion', $_GET['ubicacion'], PDO::PARAM_STR);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $medicos_por_pagina, PDO::PARAM_INT);
            $stmt->execute();

            $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Total médicos
            $total_stmt = $pdo->query('SELECT COUNT(*) FROM medicos');
            $total_medicos = $total_stmt->fetchColumn();
            $total_pages = ceil($total_medicos / $medicos_por_pagina);

            echo json_encode([
                'data' => $medicos,
                'pagination' => [
                    'total' => $total_medicos,
                    'page' => $page,
                    'total_pages' => $total_pages,
                ],
            ]);
            break;

        case 'medico':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere el parámetro ID']);
                exit;
            }
            $id = (int)$_GET['id'];
            $stmt = $pdo->prepare('SELECT * FROM medicos WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $medico = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($medico) {
                echo json_encode($medico);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Médico no encontrado']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}
?>
