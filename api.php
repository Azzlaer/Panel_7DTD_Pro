<?php
require_once __DIR__ . "/config.php";

header("Content-Type: application/json; charset=utf-8");

// Requiere sesión activa
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}


// --- Utilidades ---
function json_out(array $arr): void {
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Verifica sesión
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    json_out(['ok' => false, 'error' => 'Acceso denegado']);
}

// Acción solicitada
$action = $_GET['action'] ?? $_POST['action'] ?? '';
switch ($action) {

    /* =========================
       🔄 Actualizar Servidor
       ========================= */
    case 'steam_update':
        if (!defined('STEAMCMD_PATH') || !STEAMCMD_PATH) {
            json_out(['ok' => false, 'error' => 'STEAMCMD_PATH no definido en config.php']);
        }

        // Comprobar que el servidor NO esté corriendo
        $out = [];
        exec('tasklist /FI "IMAGENAME eq 7DaysToDieServer.exe"', $out);
        foreach ($out as $line) {
            if (stripos($line, '7DaysToDieServer.exe') !== false) {
                json_out(['ok' => false, 'error' => 'El servidor está en ejecución, deténlo antes de actualizar.']);
            }
        }

        // Ejecutar actualización
        $cmd = '"' . STEAMCMD_PATH . '" +login anonymous +app_update 294420 validate +quit';
        $output = [];
        $ret    = 0;
        exec($cmd, $output, $ret);

        if ($ret === 0) {
            json_out(['ok' => true, 'message' => 'Actualización completada con éxito.']);
        } else {
            json_out([
                'ok'    => false,
                'error' => 'Falló la actualización.',
                'log'   => implode("\n", $output)
            ]);
        }
        break;

    /* =========================
       🟢 Estado Servidor
       ========================= */
    case 'status_servers':
        $running = false;
        $out = [];
        exec('tasklist /FI "IMAGENAME eq 7DaysToDieServer.exe"', $out);
        foreach ($out as $line) {
            if (stripos($line, '7DaysToDieServer.exe') !== false) {
                $running = true;
                break;
            }
        }
        json_out(['ok' => true, 'status' => $running ? 'running' : 'stopped']);
        break;

    /* ======== Default ======== */
    default:
        json_out(['ok' => false, 'error' => 'Acción no reconocida']);
}
