<?php
// pages/actualizacion.php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit('Acceso denegado');
}

// Comprobar si el proceso del servidor esta en ejecucion
function isRunning($exe = '7DaysToDieServer.exe'): bool {
    $out = @shell_exec('tasklist /FI "IMAGENAME eq ' . $exe . '"');
    return $out && stripos($out, $exe) !== false;
}

$running = isRunning();
?>
<div class="container mt-4 text-light">
    <div class="d-flex align-items-center gap-2 mb-3">
        <h2 class="mb-0">Actualizacion del Servidor 7 Days to Die</h2>
        <span class="badge <?= $running ? 'bg-success' : 'bg-danger' ?>">
            <?= $running ? 'En ejecucion' : 'Apagado' ?>
        </span>
        <button class="btn btn-sm btn-outline-light ms-auto" onclick="checkStatus()">Verificar estado</button>
    </div>

<p class="text-light">
    El servidor debe estar <strong>apagado</strong> para poder actualizarse con SteamCMD.
</p>


    <div class="table-responsive">
        <table class="table table-dark table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>Canal</th>
                    <th>Comando</th>
                    <th style="width:160px">Accion</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Normal</td>
                    <td class="text-start">
                        <small>
                            <code><?= htmlspecialchars(STEAMCMD_PATH) ?> +login anonymous +app_update 294420 validate +quit</code>
                        </small>
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm"
                                onclick="doUpdate('normal')"
                                <?= $running ? 'disabled' : '' ?>>
                            Actualizar
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="updMsg" class="mt-3"></div>
</div>

<script>
function msg(html, cls="info") {
    const box = document.getElementById('updMsg');
    const map = {
        info: 'alert-info',
        success: 'alert-success',
        warn: 'alert-warning',
        error: 'alert-danger'
    };
    box.className = 'alert ' + (map[cls] || 'alert-info');
    box.innerHTML = html;
}

function doUpdate(type) {
    if (!confirm('Iniciar actualizacion "' + type + '" con SteamCMD?')) return;

    fetch('api.php?action=steam_update', {
        method: 'POST',
        headers: { 'Content-Type':'application/x-www-form-urlencoded' },
        body: 'type=' + encodeURIComponent(type),
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(j => {
        if (j && j.ok) {
            msg('Actualizacion iniciada. Revisa el log de SteamCMD para ver el progreso.', 'success');
        } else {
            msg('Error: ' + (j && j.error ? j.error : 'Error desconocido'), 'error');
        }
    })
    .catch(() => msg('Error de red al iniciar la actualizacion.', 'error'));
}

function checkStatus() {
    fetch('api.php?action=status_servers', { credentials: 'same-origin' })
      .then(r => r.json())
      .then(j => {
          if (!j || !j.ok) {
              msg('No se pudo consultar el estado.', 'warn');
              return;
          }
          const anyRunning = Object.values(j.status || {}).some(v => v === 'running');
          msg(anyRunning ? 'Al menos un servidor esta en ejecucion.'
                         : 'No hay servidores en ejecucion.',
              anyRunning ? 'success' : 'info');
      })
      .catch(() => msg('Error de red al consultar estado.', 'warn'));
}
</script>
