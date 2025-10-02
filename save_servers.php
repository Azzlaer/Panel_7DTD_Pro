<?php
require_once __DIR__ . '/config.php';

// Asegura que el usuario esté logueado
if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'message'=>'Acceso denegado']);
    exit;
}

$jsonFile = __DIR__ . '/servers.json';
$newJson  = $_POST['json'] ?? '';

$decoded = json_decode($newJson, true);
if ($decoded === null) {
    echo json_encode(['ok'=>false,'message'=>'JSON inválido']);
    exit;
}

if (file_put_contents($jsonFile, json_encode($decoded, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES))) {
    echo json_encode(['ok'=>true,'message'=>'Archivo guardado correctamente']);
} else {
    echo json_encode(['ok'=>false,'message'=>'No se pudo guardar el archivo']);
}
