<?php
require_once __DIR__ . '/../config.php';

$type = $_POST['type'] ?? '';
if (!$type) exit;

$log = sys_get_temp_dir() . '/backup_status.json';
file_put_contents($log, json_encode(['percent' => 0, 'msg' => 'Preparando…']));

// Lanza proceso PHP en background
$cmd = 'php "' . __DIR__ . '/backup_worker.php" ' . escapeshellarg($type) . ' > NUL 2>&1 &';
pclose(popen($cmd, 'r'));
echo "OK";
