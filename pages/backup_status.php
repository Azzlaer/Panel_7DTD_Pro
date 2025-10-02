<?php
$log = sys_get_temp_dir() . '/backup_status.json';
if (!file_exists($log)) {
    echo json_encode(['percent' => 0, 'msg' => 'Esperando...']);
    exit;
}
header('Content-Type: application/json');
echo file_get_contents($log);
