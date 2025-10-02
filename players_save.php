<?php
require_once 'config.php';

if (!defined('PLAYERS_XML') || !file_exists(PLAYERS_XML)) {
    die("Archivo players.xml no encontrado o constante PLAYERS_XML no definida.");
}

$xml = simplexml_load_file(PLAYERS_XML);
if (!$xml) {
    die("No se pudo cargar el XML de jugadores.");
}

$userid = $_POST['userid'] ?? '';
if ($userid === '') {
    die("Usuario inválido.");
}

foreach ($xml->player as $player) {
    if ((string)$player['userid'] === $userid) {

        // Datos básicos
        $player['playername']     = $_POST['playername']     ?? $player['playername'];
        $player['nativeplatform'] = $_POST['nativeplatform'] ?? $player['nativeplatform'];
        $player['lastlogin']      = $_POST['lastlogin']      ?? $player['lastlogin'];

        // Backpacks
        unset($player->backpack);
        if (!empty($_POST['backpack']) && is_array($_POST['backpack'])) {
            foreach ($_POST['backpack'] as $bp) {
                if (!empty($bp['id'])) {
                    $new = $player->addChild('backpack');
                    $new->addAttribute('id', $bp['id']);
                    $new->addAttribute('pos', $bp['pos'] ?? '');
                    $new->addAttribute('timestamp', $bp['timestamp'] ?? '');
                }
            }
        }

        // Quest Positions
        unset($player->questpositions);
        if (!empty($_POST['questpositions']) && is_array($_POST['questpositions'])) {
            $qpNode = $player->addChild('questpositions');
            foreach ($_POST['questpositions'] as $qp) {
                $pos = $qpNode->addChild('position');
                $pos->addAttribute('id', $qp['id'] ?? '');
                $pos->addAttribute('positiondatatype', $qp['positiondatatype'] ?? '');
                $pos->addAttribute('pos', $qp['pos'] ?? '');
            }
        }

        break;
    }
}

$xml->asXML(PLAYERS_XML);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guardar Jugador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="4; url=players.php">
    <style>
        body {
            background: #121212;
            color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .card {
            background: #1e1e1e;
            border: 1px solid #333;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px #000;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="text-success">✅ Cambios guardados correctamente</h1>
        <p>Serás redirigido a la lista de jugadores en unos segundos…</p>
        <a href="dashboard.php" class="btn btn-primary mt-3">Volver ahora</a>
    </div>
</body>
</html>
