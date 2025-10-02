<?php
require_once __DIR__ . '/../config.php';

$type = $_GET['type'] ?? '';
$path = '';

if ($type === 'steam') {
    $path = STEAM_LOG;
} elseif ($type === 'server') {
    $path = SERVER_LOG;
} else {
    http_response_code(400);
    exit('Tipo de log inválido');
}

if (!file_exists($path)) {
    exit("⚠️ Archivo no encontrado: $path");
}

// Leer las últimas ~2000 líneas para evitar archivos gigantes
$lines = @file($path);
if (!$lines) {
    exit("⚠️ No se pudo leer el log.");
}

$maxLines = 2000;
if (count($lines) > $maxLines) {
    $lines = array_slice($lines, -$maxLines);
}

echo htmlspecialchars(implode("", $lines));
