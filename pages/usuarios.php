<?php
require_once __DIR__ . '/../config.php';

/* ---- Funciones ---- */
function obtenerJugadoresOnline() {
    // Usa las constantes definidas en config.php
    $host  = TELNET_HOST;
    $puerto = TELNET_PORT;
    $clave  = TELNET_PASSWORD;

    $socket = @fsockopen($host, $puerto, $errno, $errstr, 5);
    if (!$socket) return [];

    fgets($socket);                   // "Please enter password:"
    fwrite($socket, $clave . "\n");
    fgets($socket);                   // confirmación
    fwrite($socket, "lp\n");          // lista de jugadores
    usleep(500000);                   // 0.5s para que responda

    $output = '';
    while (!feof($socket)) {
        $line = fgets($socket);
        if (strpos($line, 'Total of') !== false) break;
        $output .= $line;
    }
    fclose($socket);

    return parseJugadores($output);
}


function parseJugadores($output) {
    $jugadores = [];
    foreach (explode("\n", $output) as $linea) {
        if (preg_match('/^\d+\.\s+id=(\d+),\s+([^,]+),/', $linea, $m)) {
            $j = [
                'id'      => $m[1],
                'nombre'  => $m[2],
                'steamid' => '',
                'ip'      => '',
                'ping'    => '',
                'level'   => '',
                'health'  => '',
                'deaths'  => '',
                'zombies' => '',
                'score'   => ''
            ];
            if (preg_match('/pltfmid=Steam_(\d+)/', $linea, $x)) $j['steamid'] = $x[1];
            if (preg_match('/ip=([\d\.]+)/',     $linea, $x)) $j['ip']      = $x[1];
            if (preg_match('/ping=(\d+)/',       $linea, $x)) $j['ping']    = $x[1];
            if (preg_match('/level=(\d+)/',      $linea, $x)) $j['level']   = $x[1];
            if (preg_match('/health=(\d+)/',     $linea, $x)) $j['health']  = $x[1];
            if (preg_match('/deaths=(\d+)/',     $linea, $x)) $j['deaths']  = $x[1];
            if (preg_match('/zombies=(\d+)/',    $linea, $x)) $j['zombies'] = $x[1];
            if (preg_match('/score=(-?\d+)/',    $linea, $x)) $j['score']   = $x[1];
            $jugadores[] = $j;
        }
    }
    return $jugadores;
}

$jugadores = obtenerJugadoresOnline();
?>

<div class="container-fluid p-3">
    <div class="d-flex justify-content-between mb-3">
        <h2>🧟 Usuarios Online</h2>
        <button class="btn btn-secondary btn-sm" id="btnRefrescar">🔄 Actualizar</button>
    </div>

    <?php if (!empty($jugadores)): ?>
    <table class="table table-dark table-striped table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>SteamID</th>
                <th>IP / Ping</th>
                <th>Lv / ❤️ / 💀 / 🧟 / 🏅</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jugadores as $i => $p): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>🧟 <?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['steamid']) ?></td>
                <td><?= htmlspecialchars($p['ip']) ?> / <?= htmlspecialchars($p['ping']) ?> ms</td>
                <td>
                    ⚔️ <?= $p['level'] ?> /
                    ❤️ <?= $p['health'] ?> /
                    💀 <?= $p['deaths'] ?> /
                    🧟 <?= $p['zombies'] ?> /
                    🏅 <?= $p['score'] ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            ☠️ No hay supervivientes conectados en este momento.
        </div>
    <?php endif; ?>
</div>

<script>
$('#btnRefrescar').on('click', function(){
    $('#main').load('pages/usuarios.php');
});
</script>
