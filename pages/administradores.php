<?php
require_once __DIR__ . '/../config.php';

$xml = simplexml_load_file(SERVER_ADMIN_XML);
if (!$xml) {
    echo "<div class='alert alert-danger'>No se pudo cargar el archivo de administradores.</div>";
    exit;
}
?>
<div class="container mt-4 text-light">
    <h2 class="mb-4 text-center">🧟‍♂️ Administración de Usuarios</h2>

    <!-- Mensajes -->
    <div id="msgBox"></div>

    <!-- Tabla de usuarios -->
    <table class="table table-dark table-striped align-middle text-center">
        <thead>
            <tr>
                <th>Plataforma</th>
                <th>UserID</th>
                <th>Nombre</th>
                <th>Nivel de Permiso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($xml->users->user as $i => $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['platform']) ?></td>
                <td><?= htmlspecialchars($user['userid']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['permission_level']) ?></td>
                <td>
                    <button 
                        class="btn btn-sm btn-danger btn-delete"
                        data-index="<?= $i ?>">
                        Eliminar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulario para agregar -->
    <div class="card bg-dark text-light mt-5 p-4">
        <h4 class="mb-3">➕ Agregar Nuevo Administrador</h4>
        <form id="addForm">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label class="form-label">Plataforma</label>
                <input type="text" class="form-control bg-secondary text-light" name="platform" required>
            </div>
            <div class="mb-3">
                <label class="form-label">UserID</label>
                <input type="text" class="form-control bg-secondary text-light" name="userid" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre (opcional)</label>
                <input type="text" class="form-control bg-secondary text-light" name="name">
            </div>
            <div class="mb-3">
                <label class="form-label">Nivel de Permiso</label>
                <input type="number" class="form-control bg-secondary text-light" name="permission_level" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar</button>
        </form>
    </div>
</div>

<script>
function showMsg(html, type='success'){
  const box = document.getElementById('msgBox');
  const cls = type==='success' ? 'alert-success' : (type==='error' ? 'alert-danger' : 'alert-warning');
  box.innerHTML = `<div class="alert ${cls}">${html}</div>`;
}

function reloadSection(){
  if (window.$ && $('#main').length) $('#main').load('pages/administradores.php');
  else location.reload();
}

// Envío del formulario de agregar (AJAX robusto)
document.getElementById('addForm').addEventListener('submit', e=>{
  e.preventDefault();
  const data = new FormData(e.target);

  fetch('pages/administradores_save.php', {
    method:'POST',
    body: data,
    credentials: 'same-origin'
  })
  .then(r => r.text())
  .then(t => {
    let j;
    try { j = JSON.parse(t); }
    catch (e) {
      // Si llegó algo no-JSON, lo mostramos truncado para depurar
      throw new Error('Respuesta no válida del servidor: ' + t.slice(0,200));
    }
    if (j.ok) {
      showMsg('✅ Administrador agregado con éxito');
      setTimeout(reloadSection, 1000);
    } else {
      showMsg('❌ ' + (j.error || 'Error al agregar'), 'error');
    }
  })
  .catch(err => showMsg('⚠️ ' + err.message, 'warning'));
});

// Botones eliminar (AJAX robusto)
document.querySelectorAll('.btn-delete').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    if(!confirm('¿Eliminar este usuario?')) return;

    const body = new URLSearchParams();
    body.set('action','delete');
    body.set('index', btn.dataset.index);

    fetch('pages/administradores_save.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body: body.toString(),
      credentials: 'same-origin'
    })
    .then(r => r.text())
    .then(t => {
      let j;
      try { j = JSON.parse(t); }
      catch (e) { throw new Error('Respuesta no válida del servidor: ' + t.slice(0,200)); }
      if (j.ok) {
        showMsg('✅ Usuario eliminado');
        setTimeout(reloadSection, 800);
      } else {
        showMsg('❌ ' + (j.error || 'Error al eliminar'), 'error');
      }
    })
    .catch(err => showMsg('⚠️ ' + err.message, 'warning'));
  });
});
</script>

