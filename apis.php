<?php
// URL del endpoint con un parámetro de ejemplo
$url = "http://tu-dominio/tu-archivo.php?id=1"; // Cambia la URL y el ID según tu necesidad

// Inicializar cURL
$ch = curl_init();

// Configurar cURL
curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Recibir respuesta como string
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Tiempo máximo de espera
curl_setopt($ch, CURLOPT_HTTPGET, true); // Usar método GET

// Ejecutar la solicitud
$response = curl_exec($ch);

// Manejar errores
if (curl_errno($ch)) {
    echo "Error en la solicitud: " . curl_error($ch);
} else {
    // Mostrar respuesta del servidor
    echo "Respuesta del servidor: <br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// Cerrar la conexión cURL
curl_close($ch);
?>
