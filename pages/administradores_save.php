<?php
declare(strict_types=1);

// Asegura JSON y UTF-8
header('Content-Type: application/json; charset=utf-8');

// IMPORTANTÍSIMO: nada de espacios ni BOM antes de <?php
require_once __DIR__ . '/../config.php';

// Limpia buffers (por si hubo avisos previos)
while (ob_get_level()) { ob_end_clean(); }

// No muestres warnings/notices en la salida JSON
$oldDisplay = ini_get('display_errors');
ini_set('display_errors', '0');

function jout(array $a){ echo json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

// Sesión
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  http_response_code(403);
  jout(['ok'=>false, 'error'=>'Acceso denegado']);
}

try {
  if (!defined('SERVER_ADMIN_XML') || !is_file(SERVER_ADMIN_XML)) {
    throw new Exception('No se encontró SERVER_ADMIN_XML.');
  }

  $xml = @simplexml_load_file(SERVER_ADMIN_XML);
  if (!$xml) throw new Exception('No se pudo cargar el XML.');

  $action = $_POST['action'] ?? '';
  if ($action === 'add') {
    $platform = trim($_POST['platform'] ?? '');
    $userid   = trim($_POST['userid'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $level    = trim($_POST['permission_level'] ?? '');

    if ($platform === '' || $userid === '' || $level === '') {
      throw new Exception('Faltan campos obligatorios.');
    }

    $u = $xml->users->addChild('user');
    $u->addAttribute('platform', $platform);
    $u->addAttribute('userid', $userid);
    $u->addAttribute('name', $name);
    $u->addAttribute('permission_level', $level);

    if ($xml->asXML(SERVER_ADMIN_XML) === false) {
      throw new Exception('No se pudo guardar el archivo.');
    }
    jout(['ok'=>true, 'message'=>'Agregado']);
  }
  elseif ($action === 'delete') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
    if ($index < 0 || !isset($xml->users->user[$index])) {
      throw new Exception('Índice inválido');
    }

    unset($xml->users->user[$index]);
    if ($xml->asXML(SERVER_ADMIN_XML) === false) {
      throw new Exception('No se pudo guardar el archivo.');
    }
    jout(['ok'=>true, 'message'=>'Eliminado']);
  }
  else {
    throw new Exception('Acción no válida');
  }

} catch (Exception $e) {
  // Restaura display_errors como estaba
  ini_set('display_errors', $oldDisplay);
  jout(['ok'=>false, 'error'=>$e->getMessage()]);
}
