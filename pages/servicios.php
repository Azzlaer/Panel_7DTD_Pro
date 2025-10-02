<?php
// Mostrar todos los errores de PHP en pantalla (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

// --- Acción iniciar/detener desde AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['service'])) {
    $service = escapeshellarg($_POST['service']);
    $action  = $_POST['action'] === 'start' ? 'start' : 'stop';
    $output  = [];
    exec("sc $action $service 2>&1", $output, $code);
    header('Content-Type: application/json');
    echo json_encode([
        'ok'     => $code === 0,
        'output' => implode("\n", $output)
    ]);
    exit;
}

// --- Obtener listado de servicios ---
$out = [];
exec('sc query type= service state= all', $out);

// Parseo básico: cada servicio se muestra con SERVICE_NAME y STATE
$servicios = [];
$nombre = '';
$estado = '';
foreach ($out as $line) {
    if (preg_match('/SERVICE_NAME:\s+(.+)/', $line, $m)) {
        $nombre = trim($m[1]);
        $estado = '';
    } elseif (preg_match('/STATE\s*:\s*\d+\s+(\w+)/', $line, $m)) {
        $estado = strtoupper($m[1]);
        $servicios[] = ['name' => $nombre, 'state' => $estado];
    }
}
?>
<div class="container mt-4">
  <h2>🛠️ Servicios de Windows</h2>
  <p class="text-secondary">Listado de servicios en este servidor. Usa los botones para iniciar o detener.</p>

  <table class="table table-dark table-striped align-middle text-center">
    <thead>
      <tr>
        <th>Nombre del Servicio</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($servicios as $svc): ?>
        <tr>
          <td><?= htmlspecialchars($svc['name']) ?></td>
          <td>
            <?= $svc['state']==='RUNNING'
                  ? '<span class="badge bg-success">Activo</span>'
                  : '<span class="badge bg-danger">Detenido</span>' ?>
          </td>
          <td>
            <button class="btn btn-sm btn-success svc-btn"
                    data-svc="<?= htmlspecialchars($svc['name']) ?>"
                    data-act="start"
                    <?= $svc['state']==='RUNNING'?'disabled':'' ?>>
              Iniciar
            </button>
            <button class="btn btn-sm btn-danger svc-btn"
                    data-svc="<?= htmlspecialchars($svc['name']) ?>"
                    data-act="stop"
                    <?= $svc['state']!=='RUNNING'?'disabled':'' ?>>
              Detener
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
$(function(){
  $('.svc-btn').click(function(){
    const svc = $(this).data('svc');
    const act = $(this).data('act');
    if(!confirm(`¿Seguro que quieres ${act} el servicio "${svc}"?`)) return;

    $.post('pages/servicios.php',
      {service: svc, action: act},
      function(r){
        if(r.ok){
          alert(`✅ Servicio ${svc} ${act==='start'?'iniciado':'detenido'} correctamente`);
          location.reload();
        } else {
          alert('❌ Error: ' + (r.output || 'Desconocido'));
        }
      },
      'json'
    ).fail(()=>alert('⚠️ Error de red o permisos insuficientes.'));
  });
});
</script>
