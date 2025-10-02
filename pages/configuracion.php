<?php
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit('Acceso denegado');
}

// Usa la constante si ya la definiste en config.php; si no, fallback:
$XML_PATH = defined('SERVERCONFIG_XML_PATH')
    ? SERVERCONFIG_XML_PATH
    : 'C:\\Servidores\\Steam\\steamapps\\common\\7 Days to Die Dedicated Server\\serverconfig.xml';

function loadXMLSafe(string $file): ?SimpleXMLElement {
    libxml_use_internal_errors(true);
    if (!is_file($file)) return null;
    $xml = simplexml_load_file($file);
    return $xml ?: null;
}

$xml = loadXMLSafe($XML_PATH);
?>
<div class="container-fluid p-3 text-light">
  <div class="d-flex align-items-center mb-3">
    <h2 class="mb-0">⚙️ Configuración del Servidor</h2>
    <span class="ms-3 badge <?= (is_writable($XML_PATH) ? 'bg-success' : 'bg-warning') ?>">
      <?= is_writable($XML_PATH) ? 'Escribible' : 'Solo lectura' ?>
    </span>
    <small class="ms-3 text-muted">Archivo: <?= htmlspecialchars($XML_PATH) ?></small>
  </div>

  <div id="cfgMsg"></div>

  <?php if (!$xml): ?>
    <div class="alert alert-danger">
      ❌ No se pudo cargar <code><?= htmlspecialchars($XML_PATH) ?></code>.
      Verifica la ruta y permisos.
    </div>
  <?php else: ?>
    <form id="cfgForm" class="bg-dark p-4 rounded">
      <input type="hidden" name="__xml_path" value="<?= htmlspecialchars($XML_PATH) ?>">
      <?php foreach ($xml->property as $property): ?>
        <?php
          $name  = (string)$property['name'];
          $value = (string)$property['value'];
        ?>
        <div class="mb-3">
          <label for="p_<?= htmlspecialchars($name) ?>" class="form-label fw-bold">
            <?= htmlspecialchars($name) ?>
          </label>
          <input
            type="text"
            class="form-control bg-secondary text-light"
            id="p_<?= htmlspecialchars($name) ?>"
            name="<?= htmlspecialchars($name) ?>"
            value="<?= htmlspecialchars($value) ?>"
          >
        </div>
      <?php endforeach; ?>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
        <button type="button" class="btn btn-outline-light" id="btnReload">↻ Recargar</button>
      </div>
    </form>
  <?php endif; ?>
</div>

<script>
(function(){
  const form   = document.getElementById('cfgForm');
  const msgBox = document.getElementById('cfgMsg');
  const showMsg = (html, cls) => {
    msgBox.className = 'alert ' + (cls || 'alert-info');
    msgBox.innerHTML = html;
  };

  const reloadSection = ()=> {
    // Recarga solo esta sección en #main (mantiene el dashboard)
    if (window.$ && $('#main').length) {
      $('#main').load('pages/configuracion.php');
    } else {
      location.reload();
    }
  };

  const btnReload = document.getElementById('btnReload');
  if (btnReload) btnReload.addEventListener('click', reloadSection);

  if (!form) return;

  form.addEventListener('submit', function(e){
    e.preventDefault();
    const data = new URLSearchParams(new FormData(form));
    fetch('pages/configuracion_save.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: data.toString(),
      credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(j => {
      if (j && j.ok) {
        showMsg('✅ Configuración guardada con éxito.', 'alert-success');
      } else {
        showMsg('❌ ' + (j && j.error ? j.error : 'Error al guardar'), 'alert-danger');
      }
    })
    .catch(()=>{
      showMsg('⚠️ Error de red al guardar.', 'alert-warning');
    });
  });
})();
</script>
