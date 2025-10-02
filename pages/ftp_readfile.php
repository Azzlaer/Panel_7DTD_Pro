<?php
require_once __DIR__ . '/../config.php';

$path = $_GET['path'] ?? '/';
$file = $_GET['file'] ?? '';
$full = rtrim($path, '/').'/'.$file;

$conn = ftp_connect(FTP_HOST, FTP_PORT ?? 21, 30);
if (!$conn || !ftp_login($conn, FTP_USER, FTP_PASS)) {
    http_response_code(500);
    exit('Error de conexión FTP');
}
ftp_pasv($conn, true);

// Descargar a un tmp y leer
$tmp = tmpfile();
$meta = stream_get_meta_data($tmp);
ftp_get($conn, $meta['uri'], $full, FTP_ASCII);
rewind($tmp);
$content = stream_get_contents($tmp);
fclose($tmp);
ftp_close($conn);

// Salida sin escapar, solo con cabecera de texto plano UTF-8
header('Content-Type: text/plain; charset=UTF-8');
echo $content;
?>
