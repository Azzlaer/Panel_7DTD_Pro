<?php
require_once __DIR__ . '/config.php';
$file = basename($_GET['file'] ?? '');
$path = BACKUPS_DIR . "\\" . $file;
if (!is_file($path)) {
    die("Archivo no encontrado");
}
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
