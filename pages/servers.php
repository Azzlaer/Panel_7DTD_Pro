<?php
require_once __DIR__ . '/../config.php';

$jsonFile   = __DIR__ . '/../servers.json';
$servidores = json_decode(@file_get_contents($jsonFile), true) ?: [];

/**
 * Verifica si un proceso Windows está en ejecución.
 * Devuelve 'Activo' o 'Detenido'.
 */
function estadoProceso(string $exeName = '7DaysToDieServer.exe'): string {
    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
        $out = [];
        // /NH = sin cabecera, así evitamos falsos positivos
        exec('tasklist /NH /FI "IMAGENAME eq ' . $exeName . '" 2>&1', $out);
        // Buscamos el nombre exacto del exe en cualquier línea
        foreach ($out as $line) {
            if (stripos($line, $exeName) !== false) {
                return 'Activo';
            }
        }
        return 'Detenido';
    } else {
        $out = [];
        exec('pgrep -f ' . escapeshellarg($exeName), $out);
        return !empty($out) ? 'Activo' : 'Detenido';
    }
}

/* --- Iniciar / Detener --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id     = (int)($_POST['id'] ?? -1);

    if (isset($servidores[$id])) {
        $ruta  = $servidores[$id]['ruta_ejecutable'];
        $param = $servidores[$id]['parametros'];
        $exe   = basename($ruta);

        if ($accion === 'iniciar') {
            pclose(popen('start "" "' . $ruta . '" ' . $param, 'r'));
        } elseif ($accion === 'detener') {
            exec('taskkill /F /IM "' . $exe . '" 2>&1', $killOut, $killCode);
        }
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true]);
        exit;
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<div class="container-fluid p-3">
  <div class="d-flex justify-content-between mb-3">
      <h2>🖥️ Servidores</h2>
      <button class="btn btn-secondary" id="btnEditar">Editar Configuración</button>
  </div>

  <table class="table table-dark table-bordered text-center align-middle">
      <thead>
          <tr>
              <th>Estado</th>
              <th>Nombre</th>
              <th>Ejecutable</th>
              <th>Parámetros</th>
              <th>Acciones</th>
          </tr>
      </thead>
      <tbody>
      <?php foreach ($servidores as $i => $srv):
          $estado = estadoProceso(basename($srv['ruta_ejecutable']));
      ?>
          <tr>
              <td>
                  <?= $estado === 'Activo'
                      ? '<span class="badge bg-success">Activo</span>'
                      : '<span class="badge bg-danger">Detenido</span>' ?>
              </td>
              <td><?= htmlspecialchars($srv['nombre']) ?></td>
              <td><small><?= htmlspecialchars($srv['ruta_ejecutable']) ?></small></td>
              <td><small><?= htmlspecialchars($srv['parametros']) ?></small></td>
              <td>
                  <form method="POST" class="d-flex justify-content-center gap-2 server-action-form">
                      <input type="hidden" name="id" value="<?= $i ?>">
                      <button name="accion" value="iniciar" class="btn btn-success btn-sm" <?= $estado==="Activo"?"disabled":"" ?>>Iniciar</button>
                      <button name="accion" value="detener" class="btn btn-danger btn-sm" <?= $estado==="Detenido"?"disabled":"" ?>>Detener</button>
                  </form>
              </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
  </table>
</div>

<!-- Modal edición JSON -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header">
        <h5 class="modal-title">Editar servers.json</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="jsonText" class="form-control bg-dark text-light" rows="15"><?= htmlspecialchars(json_encode($servidores, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)) ?></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="saveJson">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
    $('#btnEditar').on('click', function(){
        $('#editModal').modal('show');
    });

    $('#saveJson').on('click', function(){
        $.post('save_servers.php', {json: $('#jsonText').val()}, function(resp){
            alert(resp.message);
            if(resp.ok) location.reload();
        }, 'json').fail(()=>alert('Error guardando el archivo'));
    });

    // Ajax para iniciar/detener sin recargar
    $('.server-action-form').on('submit', function(e){
        e.preventDefault();
        $.post('pages/servers.php', $(this).serialize(), function(){
            location.reload();
        });
    });
});
</script>
