<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $xml = simplexml_load_file(SERVER_ADMIN_XML);
    if (!$xml) throw new Exception('No se pudo abrir el archivo XML.');

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $u = $xml->users->addChild('user');
        $u->addAttribute('platform', $_POST['platform']);
        $u->addAttribute('userid', $_POST['userid']);
        $u->addAttribute('name', $_POST['name'] ?? '');
        $u->addAttribute('permission_level', $_POST['permission_level']);
        $xml->asXML(SERVER_ADMIN_XML);
        echo json_encode(['ok'=>true]);
        exit;
    }

    if ($action === 'delete') {
        $index = (int)$_POST['index'];
        unset($xml->users->user[$index]);
        $xml->asXML(SERVER_ADMIN_XML);
        echo json_encode(['ok'=>true]);
        exit;
    }

    throw new Exception('Acción no válida.');
} catch(Exception $e){
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
