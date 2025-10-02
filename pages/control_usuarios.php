<?php
require_once __DIR__ . '/../config.php';

$xml = simplexml_load_file(PLAYERS_XML);
if (!$xml) {
    echo "<div class='alert alert-danger'>No se pudo cargar players.xml</div>";
    exit;
}

// --- Guardar cambios vía AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_save'])) {
    $userid = $_POST['userid'];
    foreach ($xml->player as $p) {
        if ((string)$p['userid'] === $userid) {
            $p['playername']     = $_POST['playername'];
            $p['nativeplatform'] = $_POST['nativeplatform'];
            $p['lastlogin']      = $_POST['lastlogin'];
            break;
        }
    }
    $xml->asXML(PLAYERS_XML);
    echo json_encode(['ok'=>true]);
    exit;
}

// --- Datos de un jugador para el modal ---
if (isset($_GET['ajax_get']) && $_GET['ajax_get']) {
    $id = $_GET['userid'] ?? '';
    foreach ($xml->player as $p) {
        if ((string)$p['userid'] === $id) {
            echo json_encode([
                'userid'        => (string)$p['userid'],
                'playername'    => (string)$p['playername'],
                'nativeplatform'=> (string)$p['nativeplatform'],
                'lastlogin'     => (string)$p['lastlogin']
            ]);
            exit;
        }
    }
    echo json_encode(['error' => 'Jugador no encontrado']);
    exit;
}

// --- Listado ---
$filter = strtolower($_GET['filter'] ?? '');
?>
<div class="container-fluid p-3">
    <h2>👥 Control de Usuarios</h2>
    <form class="mb-3">
        <div class="input-group">
            <input type="text" name="filter" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($filter) ?>">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <table class="table table-dark table-hover text-center align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Plataforma</th>
                <th>Último login</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($xml->player as $p):
            $name     = (string)$p['playername'];
            $platform = (string)$p['nativeplatform'];
            $last     = (string)$p['lastlogin'];
            if (!$filter || str_contains(strtolower($name),$filter) ||
                str_contains(strtolower($platform),$filter) ||
                str_contains(strtolower($last),$filter)): ?>
            <tr>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= htmlspecialchars($platform) ?></td>
                <td><?= htmlspecialchars($last) ?></td>
                <td>
                    <!-- Botón Editar (modal) -->
                    <button class="btn btn-sm btn-info btnEdit" data-userid="<?= htmlspecialchars($p['userid']) ?>">Editar</button>

                    <!-- 🔽 Nuevo botón para mochilas -->
                    <a href="#"
                       class="btn btn-sm btn-warning"
                       onclick="$('#main').load('pages/backpacks.php?userid=<?= urlencode($p['userid']) ?>');return false;">
                       🎒 Mochilas
                    </a>
                </td>
            </tr>
        <?php endif; endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header">
        <h5 class="modal-title">Editar Jugador</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <input type="hidden" name="userid" id="userid">
          <div class="mb-3">
            <label class="form-label">Playername</label>
            <input type="text" class="form-control" name="playername" id="playername">
          </div>
          <div class="mb-3">
            <label class="form-label">Native Platform</label>
            <input type="text" class="form-control" name="nativeplatform" id="nativeplatform">
          </div>
          <div class="mb-3">
            <label class="form-label">Last Login</label>
            <input type="text" class="form-control" name="lastlogin" id="lastlogin">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" id="btnSave">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
    // Cargar datos en el modal
    $('.btnEdit').on('click', function(){
        const id = $(this).data('userid');
        $.get('pages/control_usuarios.php', {ajax_get:1, userid:id}, function(data){
            if(data.error){ alert(data.error); return; }
            $('#userid').val(data.userid);
            $('#playername').val(data.playername);
            $('#nativeplatform').val(data.nativeplatform);
            $('#lastlogin').val(data.lastlogin);
            $('#editModal').modal('show');
        }, 'json');
    });

    // Guardar edición
    $('#btnSave').on('click', function(){
        $.post('pages/control_usuarios.php',
            $('#editForm').serialize() + '&ajax_save=1',
            function(resp){
                if(resp.ok){
                    $('#editModal').modal('hide');
                    $('#main').load('pages/control_usuarios.php');
                } else {
                    alert('Error al guardar');
                }
            }, 'json');
    });
});
</script>
