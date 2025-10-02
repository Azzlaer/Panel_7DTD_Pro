<?php
require_once __DIR__ . '/../config.php';

// ----------------------------
// 1. Conteos de usuarios y mods
// ----------------------------
$userCount = is_dir(PLAYER_TPP_DIR)
    ? count(glob(PLAYER_TPP_DIR . DIRECTORY_SEPARATOR . '*.tpp', GLOB_NOSORT))
    : 0;

$modsCount = is_dir(MODS_DIR)
    ? count(glob(MODS_DIR . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR))
    : 0;

// ----------------------------
// 2. Detección Blood Moon
// ----------------------------
function getBloodMoonStatus(): array {
    $fp = @fsockopen(TELNET_HOST, TELNET_PORT, $errno, $errstr, 5);
    if (!$fp) return ['error' => true];

    // Autenticación Telnet
    fgets($fp);
    fwrite($fp, TELNET_PASS . "\n");
    fgets($fp);

    // Obtener tiempo actual del servidor
    fwrite($fp, "gettime\n");
    stream_set_timeout($fp, 2);
    $out = '';
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false) break;
        $out .= $line;
    }
    fclose($fp);

    if (preg_match('/Day\s+(\d+),\s+(\d+):(\d+)/i', $out, $m)) {
        $day  = (int)$m[1];
        $hour = (int)$m[2];
        $freq = BLOODMOON_FREQ; // Definido en config.php
        $active = ($day % $freq === 0 && $hour >= 22);
        return ['day' => $day, 'hour' => $hour, 'active' => $active, 'freq' => $freq];
    }
    return ['error' => true];
}

$bm = getBloodMoonStatus();
if (!empty($bm['error'])) {
    $bloodText = "<span class='text-warning'>No se pudo obtener el estado del servidor</span>";
} elseif ($bm['active']) {
    $bloodText = "<span class='text-danger fw-bold'>🌕 ¡Blood Moon en curso! (Día {$bm['day']} - {$bm['hour']}:00)</span>";
} else {
    $next = $bm['day'] + ($bm['freq'] - ($bm['day'] % $bm['freq']));
    $bloodText = "<span class='text-success'>Próxima Blood Moon prevista para el día $next</span>";
}

// ----------------------------
// 3. Gametracker
// ----------------------------
$modo = $_GET['modo'] ?? 'html';
?>
<div class="container mt-4 text-light">
    <h2 class="mb-4 text-center">📊 Estadísticas del Servidor</h2>

    <!-- BLOOD MOON -->
    <div class="card bg-dark border-danger p-4 mb-4 text-center">
        <h4 class="text-white">🩸 Estado Blood Moon</h4>
        <p class="fs-5"><?= $bloodText ?></p>
    </div>

    <!-- USUARIOS Y MODS -->
    <div class="row text-center mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-dark border-secondary p-4">
                <h4 class="text-white">🧟‍♂️ Cantidad de Usuarios</h4>
                <p class="display-6 text-success"><?= $userCount ?></p>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-dark border-secondary p-4">
                <h4 class="text-white">🛠️ Cantidad de Mods</h4>
                <p class="display-6 text-info"><?= $modsCount ?></p>
            </div>
        </div>
    </div>

    <hr class="border-secondary my-4">

    <!-- GAMETRACKER -->
    <h3 class="mb-3 text-center">🎮 Gametracker</h3>

    <form id="modoForm" class="text-center mb-3">
        <label for="modo" class="form-label">Formato:</label>
        <select name="modo" id="modo" class="form-select d-inline-block w-auto bg-dark text-light border-secondary">
            <option value="html"   <?= $modo === 'html'   ? 'selected' : '' ?>>HTML</option>
            <option value="bbcode" <?= $modo === 'bbcode' ? 'selected' : '' ?>>BBCode</option>
            <option value="ambos"  <?= $modo === 'ambos'  ? 'selected' : '' ?>>Ambos</option>
        </select>
        <button type="submit" class="btn btn-primary ms-2">Mostrar</button>
    </form>

    <div class="bg-dark p-3 rounded border border-secondary text-center">
        <?php if ($modo === 'html' || $modo === 'ambos'): ?>
            <h5 class="text-warning">HTML</h5>
            <div class="mb-3 text-center"><?= GAMETRACKER_HTML ?></div>
            <pre class="bg-secondary text-dark p-2 rounded small"><?= htmlspecialchars(GAMETRACKER_HTML) ?></pre>
        <?php endif; ?>

        <?php if ($modo === 'bbcode' || $modo === 'ambos'): ?>
            <h5 class="text-warning mt-4">BBCode</h5>
            <pre class="bg-secondary text-dark p-2 rounded small"><?= GAMETRACKER_BBCODE ?></pre>
        <?php endif; ?>
    </div>
</div>

<script>
$(function(){
    $('#modoForm').on('submit', function(e){
        e.preventDefault();
        const m = $('#modo').val();
        $('#main').load('pages/estadisticas.php?modo=' + encodeURIComponent(m));
    });
});
</script>
