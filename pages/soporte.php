<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit('Acceso denegado');
}

// Carpeta raíz del proyecto (ajústala si tu instalación está en otro lugar)
$rootDir = realpath(__DIR__ . '/..');

/**
 * Generar HTML en forma de árbol de archivos
 */
function buildTreeView($dir, $base = '')
{
    $html = "<ul class='ms-3'>";
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $rel  = $base === '' ? $item : $base . '/' . $item;
        if (is_dir($path)) {
            $html .= "<li><strong>📁 $item</strong>";
            $html .= buildTreeView($path, $rel);
            $html .= "</li>";
        } else {
            $html .= "<li>📄 $item</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}
?>
<div class="container mt-4 text-light">
    <h2>🤝 Soporte y Créditos</h2>

    <p class="mt-3">
        Este proyecto fue desarrollado en conjunto por <b>ChatGPT</b> y <b>Azzlaer</b> 
        para <a href="https://www.latinbattle.com" target="_blank">LatinBattle.com</a> 
        y su amplia comunidad de juegos online.
    </p>

    <p>
        El panel incluye administración de servidores de <b>7 Days to Die</b> con:
        inicio de sesión, control de procesos, gestión de usuarios y mochilas,
        backups, gestión de mods, visor de logs en tiempo real, FTP manager,
        actualizaciones automáticas, estadísticas, integración con Telnet,
        y más herramientas personalizadas.
    </p>

    <p>
        Para futuras actualizaciones y código fuente oficial visita: <br>
        <a href="https://github.com/Azzlaer/7DaysToDie_PRO" target="_blank">
            https://github.com/Azzlaer/7DaysToDie_PRO
        </a>
    </p>

    <hr class="border-secondary">

    <h4 class="mt-4">📂 Estructura del proyecto</h4>
    <p>Vista tipo árbol de todos los archivos y carpetas:</p>

    <div class="treeview bg-dark p-3 rounded" style="max-height:500px;overflow:auto;">
        <?php echo buildTreeView($rootDir); ?>
    </div>
</div>

<style>
.treeview ul {
    list-style-type: none;
    padding-left: 1em;
    margin: 0;
}
.treeview li {
    margin: 2px 0;
    color: #fff;
    font-family: monospace;
}
.treeview li strong {
    color: #0f0;
}
</style>
