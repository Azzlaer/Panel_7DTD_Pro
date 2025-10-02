<?php
require_once __DIR__ . '/../config.php';

// Conexión FTP
$conn = ftp_connect(FTP_HOST, FTP_PORT ?? 21, 30);
if (!$conn) die("<div class='text-danger'>No se pudo conectar al servidor FTP.</div>");
if (!ftp_login($conn, FTP_USER, FTP_PASS)) die("<div class='text-danger'>Error de autenticación FTP.</div>");
ftp_pasv($conn, true);

// Path actual
$path = isset($_GET['path']) ? $_GET['path'] : '/';

// --- Subir archivo ---
if (!empty($_FILES['upload']['tmp_name'])) {
    $tmp  = $_FILES['upload']['tmp_name'];
    $name = basename($_FILES['upload']['name']);
    if (ftp_put($conn, $path . '/' . $name, $tmp, FTP_BINARY)) {
        echo "<script>alert('Archivo subido correctamente');</script>";
    } else {
        echo "<script>alert('Error al subir el archivo');</script>";
    }
}

// --- Eliminar ---
if (!empty($_GET['delete'])) {
    $target = $path . '/' . $_GET['delete'];
    // Intentar eliminar archivo, si falla intentamos como carpeta
    if (!@ftp_delete($conn, $target)) {
        @ftp_rmdir($conn, $target);
    }
}

// --- Guardar edición de archivo ---
if (!empty($_POST['save_file']) && isset($_POST['content'])) {
    $localTmp = tempnam(sys_get_temp_dir(), 'ftpedit_');
    file_put_contents($localTmp, $_POST['content']);
    ftp_put($conn, $path . '/' . $_POST['save_file'], $localTmp, FTP_ASCII);
    unlink($localTmp);
    echo "<script>alert('Archivo guardado correctamente');</script>";
}

// --- Listado ---
$files = ftp_nlist($conn, $path);
?>
<div class="container-fluid text-light">
    <h2 class="mt-3">🌐 FTP Manager</h2>
    <p>Ruta actual: <b><?= htmlspecialchars($path) ?></b></p>

    <!-- Formulario de subida -->
    <form method="post" enctype="multipart/form-data" class="mb-3">
        <input type="file" name="upload" class="form-control mb-2" required>
        <button class="btn btn-primary">⬆️ Subir archivo aquí</button>
    </form>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tamaño</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($files) {
            foreach ($files as $f) {
                $base = basename($f);
                if ($base === '.' || $base === '..') continue;

                $isDir = @ftp_size($conn, $path.'/'.$base) === -1;
                $size  = $isDir ? '📁 Carpeta' : (ftp_size($conn, $path.'/'.$base).' bytes');
                echo "<tr>
                        <td>";
                if ($isDir) {
                    echo "<a href='#' class='ftp-folder text-info' data-path='" . htmlspecialchars($path.'/'.$base) . "'>$base</a>";
                } else {
                    echo htmlspecialchars($base);
                }
                echo "</td>
                        <td>$size</td>
                        <td>
                            <a href='?path=" . urlencode($path) . "&delete=" . urlencode($base) . "' class='btn btn-sm btn-danger' 
                               onclick=\"return confirm('¿Eliminar $base?');\">🗑️ Eliminar</a>";
                // Botón Editar para archivos de texto
                if (!$isDir && preg_match('/\.(xml|ini|txt|cfg|log|json|php|bat|py|)$/i', $base)) {
                    echo " <button class='btn btn-sm btn-warning edit-btn' 
                                data-file='" . htmlspecialchars($base) . "'>✏️ Editar</button>";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Carpeta vacía</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-dark text-light">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Editar archivo</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <textarea id="fileContent" name="content" class="form-control bg-black text-light" style="height:400px;"></textarea>
          <input type="hidden" name="save_file" id="saveFile">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Navegación AJAX en carpetas
$(document).on('click', '.ftp-folder', function(e){
    e.preventDefault();
    const p = $(this).data('path');
    $('#main').html('<div class="p-5 text-center text-light">Cargando…</div>');
    $('#main').load('pages/ftp_manager.php?path=' + encodeURIComponent(p));
});

// Abrir modal de edición
$(document).on('click', '.edit-btn', function(){
    const file = $(this).data('file');
    const path = <?= json_encode($path) ?>;
    $.get('pages/ftp_readfile.php', {path: path, file: file}, function(data){
        $('#fileContent').val(data);
        $('#saveFile').val(file);
        $('#editModal').modal('show');
    });
});
</script>
<?php ftp_close($conn); ?>
