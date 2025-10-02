<?php
require_once __DIR__ . '/../config.php';

$xml = simplexml_load_file(PLAYERS_XML);
if (!$xml) {
    echo "<div class='alert alert-danger'>No se pudo cargar players.xml</div>";
    exit;
}

$userid   = $_GET['userid'] ?? '';
$selected = null;

// Buscar jugador
foreach ($xml->player as $player) {
    if ((string)$player['userid'] === $userid) {
        $selected = $player;
        break;
    }
}

if (!$selected): ?>
    <div class="alert alert-danger">Jugador no encontrado.</div>
<?php else: ?>
<style>
    body { background: #1a1a1a; color: #f1f1f1; font-family: 'Courier New', monospace; }
    h2 { color: #ff5555; text-shadow: 1px 1px 2px #000; margin-bottom:20px; }
    form { background:#2a2a2a; border:2px solid #444; border-radius:10px; padding:20px; max-width:700px; margin:auto; box-shadow:0 0 10px #000; }
    input[type="text"] { width:100%; background:#111; color:#0f0; border:1px solid #333; padding:8px; margin:4px 0 12px; border-radius:5px; }
    input[type="submit"] { background:#8b0000; color:#fff; border:none; padding:12px 20px; border-radius:5px; cursor:pointer; font-weight:bold; }
    input[type="submit"]:hover { background:#ff0000; box-shadow:0 0 10px red; }
    fieldset { border:1px solid #444; padding:10px; margin-bottom:15px; background:#1f1f1f; border-left:5px solid #8b0000; }
    .zombie-button { display:inline-block; padding:10px 20px; background:#28a745; color:white; text-decoration:none; font-weight:bold;
                     border-radius:8px; transition:background 0.3s, box-shadow 0.3s; font-size:16px; border:2px solid #1c642d; box-shadow:0 0 5px #1c642d;}
    .zombie-button:hover { background:#34d058; box-shadow:0 0 15px limegreen; text-shadow:0 0 5px black; cursor:pointer; }
</style>

<h2>🎒 Mochilas de: <?= htmlspecialchars($selected['playername']) ?></h2>

<form method="post" action="players_save.php">
    <input type="hidden" name="userid" value="<?= htmlspecialchars($selected['userid']) ?>">

    <p><b>Datos básicos:</b></p>
    UserID: <input type="text" name="userid_visible" value="<?= htmlspecialchars($selected['userid']) ?>" readonly><br>
    NativeUserID: <input type="text" name="nativeuserid" value="<?= htmlspecialchars($selected['nativeuserid']) ?>"><br>
    Playername: <input type="text" name="playername" value="<?= htmlspecialchars($selected['playername']) ?>"><br>
    Native Platform: <input type="text" name="nativeplatform" value="<?= htmlspecialchars($selected['nativeplatform']) ?>"><br>
    Last Login: <input type="text" name="lastlogin" value="<?= htmlspecialchars($selected['lastlogin']) ?>"><br><br>

    <p><b>Mochilas (Backpack):</b></p>
    <?php foreach ($selected->backpack as $i => $bp): ?>
        <fieldset>
            ID: <input type="text" name="backpack[<?= $i ?>][id]" value="<?= htmlspecialchars($bp['id']) ?>">
            Pos: <input type="text" name="backpack[<?= $i ?>][pos]" value="<?= htmlspecialchars($bp['pos']) ?>">
            Timestamp: <input type="text" name="backpack[<?= $i ?>][timestamp]" value="<?= htmlspecialchars($bp['timestamp']) ?>">
        </fieldset><br>
    <?php endforeach; ?>

    <p><b>Posiciones de misión (Quest Positions):</b></p>
    <?php $questPositions = $selected->questpositions->position ?? [];
    foreach ($questPositions as $j => $qp): ?>
        <fieldset>
            ID: <input type="text" name="questpositions[<?= $j ?>][id]" value="<?= htmlspecialchars($qp['id']) ?>">
            Tipo: <input type="text" name="questpositions[<?= $j ?>][positiondatatype]" value="<?= htmlspecialchars($qp['positiondatatype']) ?>">
            Pos: <input type="text" name="questpositions[<?= $j ?>][pos]" value="<?= htmlspecialchars($qp['pos']) ?>">
        </fieldset><br>
    <?php endforeach; ?>

    <input type="submit" value="Guardar cambios">
    <a href="#" class="zombie-button" onclick="$('#main').load('pages/control_usuarios.php');return false;">🏠 Volver</a>
</form>
<?php endif; ?>
