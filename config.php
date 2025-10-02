<?php
/**
 * config.php
 * Configuración principal del Panel 7 Days to Die
 */

// ---- INICIO DE SESIÓN ----
if (session_status() === PHP_SESSION_NONE) {
    // Ajusta el nombre de la sesión si quieres aislarla
    session_name('7dtd_panel');
    session_start();
}


// Rutas de logs
define('STEAM_LOG', 'C:\\Servidores\\Steam\\logs\\console_log.txt');
define('SERVER_LOG', 'C:\\Servidores\\Steam\\steamapps\\common\\7 Days to Die Dedicated Server\\logs\\connection_log_26900.txt');


// Ruta al ejecutable de SteamCMD para actualizar el servidor
define('STEAMCMD_PATH', 'C:\\Servidores\\Steam\\steamcmd.exe');

// ---- CREDENCIALES DEL ADMIN ----
// ⚠️  Cambia estos valores antes de usar en producción
define('ADMIN_USER', 'Azzlaer');
define('ADMIN_PASS', '35027595'); // Usa una contraseña robusta

// ---- CONFIGURACIONES ----
define('SERVER_XML', 'C:\\Servidores\\Steam\\steamapps\\common\\7 Days to Die Dedicated Server\\serverconfig.xml');
define('PLAYERS_XML', 'C:\\Users\\Guardia\\AppData\\Roaming\\7DaysToDie\\Saves\\Pregen06k4\\guardia\\players.xml');
define('SERVER_ADMIN_XML', 'C:\\Users\\Guardia\\AppData\\Roaming\\7DaysToDie\\Saves\\serveradmin.xml');

// --- RESPALDOS ---
define('WORLD_NAME', 'Pregen06k4');
define('WORLD_DIR', 'C:\\Users\\Guardia\\AppData\\Roaming\\7DaysToDie\\Saves\\' . WORLD_NAME);
define('BACKUPS_DIR', 'C:\\Servidores\\Steam\\steamapps\\common\\7 Days to Die Dedicated Server\\Backups');


// Frecuencia de la Blood Moon (normalmente 7 días)
define('BLOODMOON_FREQ', 7);

// === Rutas de estadísticas ===
define('PLAYER_TPP_DIR', 'C:\\Users\\Guardia\\AppData\\Roaming\\7DaysToDie\\Saves\\Pregen06k4\\guardia\\Player');
define('MODS_DIR', 'C:\\Servidores\\Steam\\steamapps\\common\\7 Days to Die Dedicated Server\\Mods');

// === Códigos de Gametracker (HTML / BBCode) ===
define('GAMETRACKER_HTML', '<a href="https://www.gametracker.com/server_info/131.221.35.10:27026/" target="_blank"><img src="https://cache.gametracker.com/server_info/131.221.35.10:27026/b_560_95_1.png" border="0" width="560" height="95" alt=""/></a>');
define('GAMETRACKER_BBCODE', '[url=https://www.gametracker.com/server_info/131.221.35.10:27026/][img]https://cache.gametracker.com/server_info/131.221.35.10:27026/b_560_95_1.png[/img][/url]');


// Datos de Telnet
define('TELNET_HOST', '127.0.0.1');
define('TELNET_PORT', 8086);
define('TELNET_PASSWORD', '35027595*');

// --- FTP Manager ---
define('FTP_HOST', '127.0.0.1');     // ej: 127.0.0.1
define('FTP_USER', '7days');
define('FTP_PASS', '35027595*');
define('FTP_PORT', 21);




// ---- ZONA HORARIA ----
date_default_timezone_set('America/Argentina/Buenos_Aires');

// ---- RUTAS IMPORTANTES ----
// Archivo JSON que contiene la lista de servidores
define('SERVERS_JSON', __DIR__ . '/servers.json');

// Puedes definir más rutas o variables globales aquí si lo necesitas.
// Ejemplo: define('LOG_PATH', __DIR__.'/logs/');

// ---- FUNCIONES AUXILIARES ----

/**
 * Redirige a una URL de forma segura.
 */
function redirect(string $url) {
    header("Location: " . $url);
    exit;
}

/**
 * Verifica si el usuario está logueado.
 */
function is_logged_in(): bool {
    return !empty($_SESSION['logged_in']);
}
