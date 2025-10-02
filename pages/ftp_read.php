<?php
require_once __DIR__ . '/../config.php';

$file = $_GET['file'] ?? '';
if (!$file) exit;

$conn = ftp_connect(FTP_HOST, FTP_PORT, 10);
if (!$conn || !ftp_login($conn, FTP_USER, FTP_PASS)) exit;

$tmp = tmpfile();
ftp_fget($conn, $tmp, $file, FTP_ASCII);
rewind($tmp);
echo stream_get_contents($tmp);
fclose($tmp);
ftp_close($conn);
