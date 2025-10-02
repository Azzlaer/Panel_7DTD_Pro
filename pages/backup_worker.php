<?php
require_once __DIR__ . '/../config.php';

$type = $argv[1] ?? '';
$log = sys_get_temp_dir() . '/backup_status.json';

function zipWithProgress($source, $destination, $log){
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $fileList = iterator_to_array($files);
    $total = count($fileList);
    $done = 0;

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) return false;
    $sourceReal = realpath($source);

    foreach ($fileList as $file) {
        $filePath = $file->getRealPath();
        $relative = ltrim(str_replace('\\', '/', substr($filePath, strlen($sourceReal))), '/');
        if ($file->isDir()) {
            $zip->addEmptyDir($relative);
        } else {
            $zip->addFile($filePath, $relative);
        }
        $done++;
        $percent = round(($done / $total) * 100);
        file_put_contents($log, json_encode([
            'percent' => $percent,
            'msg' => "Comprimidos $done de $total archivos..."
        ]));
    }
    $zip->close();
    return true;
}

$stamp = date('Y-m-d_H-i-s');
if ($type === 'mods') {
    $dest = BACKUPS_DIR . "/mods_backup_$stamp.zip";
    zipWithProgress(MODS_DIR, $dest, $log);
} elseif ($type === 'world') {
    $dest = BACKUPS_DIR . "/world_backup_$stamp.zip";
    zipWithProgress(WORLD_DIR, $dest, $log);
}

file_put_contents($log, json_encode(['percent' => 100, 'msg' => 'Respaldo finalizado.']));
