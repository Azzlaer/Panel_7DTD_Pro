<?php
require_once __DIR__ . '/../config.php';

// Solo administradores

if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

$message = "";

// --- 1. Subida y extracción de ZIP ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['modzip'])) {
    $zipFile = $_FILES['modzip']['tmp_name'];
    $name    = $_FILES['modzip']['name'];

    if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'zip') {
        $message = "<div class='alert alert-danger'>El archivo debe ser .ZIP</div>";
    } else {
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo(MODS_DIR);
            $zip->close();
            $message = "<div class='alert alert-success'>✅ Mod extraído correctamente en <b>Mods</b>.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error al extraer el ZIP.</div>";
        }
    }
}

// --- 2. Eliminación de un mod ---
if (isset($_GET['delete'])) {
    $modName = basename($_GET['delete']);
    $path = MODS_DIR . DIRECTORY_SEPARATOR . $modName;

    if (is_dir($path)) {
        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
        rmdir($path);
        $message = "<div class='alert alert-warning'>🗑️ Mod <b>$modName</b> eliminado.</div>";
    }
}

// --- 3. Listar mods instalados ---
$mods = [];
if (is_dir(MODS_DIR)) {
    foreach (glob(MODS_DIR . '/*', GLOB_ONLYDIR) as $dir) {
        $mods[] = basename($dir);
    }
}
?>
<div class="container text-light mt-4">
    <h2 class="mb-4 text-center">🛠️ Gestión de Mods</h2>

    <?= $message ?>

    <div class="alert alert-info">
        <b>Importante:</b> Antes de subir, asegúrate de que el ZIP contenga:
        <ul>
            <li>Una carpeta con el <b>nombre del mod</b>.</li>
            <li>Dentro de esa carpeta: <code>modinfo.xml</code>, <code>desktop.ini</code> y la carpeta <code>Config</code> con los archivos XML.</li>
        </ul>
        Si los archivos están sueltos y no dentro de una carpeta, el servidor no reconocerá el mod.
    </div>

    <!-- Formulario de subida -->
    <form method="post" enctype="multipart/form-data" class="mb-5 bg-dark p-4 border border-secondary rounded">
        <div class="mb-3">
            <label for="modzip" class="form-label">Subir archivo ZIP del Mod</label>
            <input type="file" name="modzip" id="modzip" class="form-control bg-dark text-light border-secondary" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir y Extraer</button>
    </form>

    <!-- Listado de mods -->
    <h3 class="mb-3">📂 Mods Instalados</h3>
    <?php if (empty($mods)): ?>
        <p class="text-warning">No hay mods instalados.</p>
    <?php else: ?>
        <table class="table table-dark table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre del Mod</th>
                    <th style="width: 150px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mods as $mod): ?>
                    <tr>
                        <td><?= htmlspecialchars($mod) ?></td>
                        <td class="text-center">
                            <a href="?delete=<?= urlencode($mod) ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Eliminar el mod <?= htmlspecialchars($mod) ?>?');">
                               Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
