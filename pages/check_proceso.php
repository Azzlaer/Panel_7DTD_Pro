<?php
/**
 * Script para comprobar si el proceso 7DaysToDieServer.exe está en ejecución
 * Compatible con Windows (tasklist) y Linux (pgrep)
 */

function estaActivo($exeName = '7DaysToDieServer.exe'): bool {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $out = [];
        // Ejecuta tasklist y captura cualquier idioma (inglés/español)
        exec('tasklist /FI "IMAGENAME eq ' . $exeName . '" 2>&1', $out);

        // Si solo hay cabecera o mensaje de "no tasks"/"ninguna", está detenido
        if (count($out) <= 1) return false;
        if (isset($out[1]) &&
            (stripos($out[1], 'no tasks') !== false || stripos($out[1], 'ninguna') !== false)) {
            return false;
        }
        return true;
    } else {
        // Para Linux
        $out = [];
        exec('pgrep -f ' . escapeshellarg($exeName), $out);
        return !empty($out);
    }
}

// --- Ejecución del script ---
header('Content-Type: text/plain; charset=utf-8');
echo "Comprobando proceso 7DaysToDieServer.exe...\n";

if (estaActivo()) {
    echo "✅ El proceso está ACTIVO.\n";
} else {
    echo "❌ El proceso está DETENIDO.\n";
}
