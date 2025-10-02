<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['ok'=>false, 'error'=>'Acceso denegado']);
    exit;
}

$action = $_GET['action'] ?? '';
$fileKey = $_GET['file'] ?? ($_POST['file'] ?? '');
$path = '';

if ($fileKey === 'steam') {
    $path = STEAM_LOG;
} elseif ($fileKey === 'server') {
    $path = SERVER_LOG;
} else {
    echo json_encode(['ok'=>false, 'error'=>'Archivo inválido']);
    exit;
}

if (!file_exists($path)) {
    echo json_encode(['ok'=>false, 'error'=>'Archivo no encontrado']);
    exit;
}

if ($action === 'view') {
    // Leer últimas 2000 líneas
    $lines = @file($path);
    if (!$lines) {
        echo json_encode(['ok'=>false, 'error'=>'No se pudo leer el archivo']);
        exit;
    }
    $max = 2000;
    if (count($lines) > $max) {
        $lines = array_slice($lines, -$max);
    }
    echo json_encode(['ok'=>true, 'content'=>implode('', $lines)]);
    exit;
}

if ($action === 'clear' && $_SERVER['REQUEST_METHOD']==='POST') {
    if (@file_put_contents($path, '') !== false) {
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['ok'=>false, 'error'=>'No se pudo limpiar el archivo']);
    }
    exit;
}

echo json_encode(['ok'=>false, 'error'=>'Acción inválida']);
