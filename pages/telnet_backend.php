<?php
require_once __DIR__ . '/../config.php';

// Solo usuarios logueados
session_start();
if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

$command = trim($_POST['command'] ?? '');
if ($command === '') {
    http_response_code(400);
    exit('Comando vacío');
}

$fp = @fsockopen(TELNET_HOST, TELNET_PORT, $errno, $errstr, 5);
if (!$fp) {
    http_response_code(500);
    exit("Error de conexión: $errstr ($errno)");
}

// Autenticación Telnet
fgets($fp);
fwrite($fp, TELNET_PASS . "\n");
fgets($fp); // Confirmación

// Enviar comando
fwrite($fp, $command . "\n");

// Leer respuesta (timeout de 2 seg)
stream_set_timeout($fp, 2);
$response = '';
while (!feof($fp)) {
    $line = fgets($fp);
    if ($line === false) break;
    $response .= $line;
}
fclose($fp);

header('Content-Type: application/json');
echo json_encode(['output' => $response]);
