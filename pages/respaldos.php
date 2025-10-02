<?php
require_once __DIR__ . '/../config.php';
?>
<div class="container text-light mt-4">
    <h2 class="mb-4 text-center">🗂️ Respaldos del Servidor</h2>

    <!-- Botones para iniciar backups -->
    <div class="d-flex justify-content-center mb-4 gap-3">
        <button class="btn btn-primary" onclick="startBackup('mods')">💾 Crear Backup de MODS</button>
        <button class="btn btn-success" onclick="startBackup('world')">🌍 Crear Backup del Mundo</button>
    </div>

    <!-- Barra de progreso -->
    <div class="progress mb-3" style="height: 30px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
             id="progressbar" style="width:0%">0%</div>
    </div>
    <div id="progressText" class="text-center mb-4"></div>

    <!-- Tabla de respaldos existentes -->
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $backups = glob(BACKUPS_DIR . '/*.zip');
        if ($backups):
            foreach ($backups as $b):
                $bn = basename($b); ?>
                <tr>
                    <td><?= htmlspecialchars($bn) ?></td>
                    <td>
                        <a href="download.php?file=<?= urlencode($bn) ?>" class="btn btn-sm btn-info">⬇️ Descargar</a>
                        <a href="#" class="btn btn-sm btn-warning"
                           onclick="if(confirm('¿Restaurar <?= $bn ?>?')) $('#main').load('pages/restore_backup.php?file=<?= urlencode($bn) ?>'); return false;">
                           ♻️ Restaurar
                        </a>
                    </td>
                </tr>
        <?php
            endforeach;
        else:
            echo '<tr><td colspan="2">Sin respaldos aún.</td></tr>';
        endif;
        ?>
        </tbody>
    </table>
</div>

<script>
function startBackup(tipo){
    $('#progressbar').css('width','0%').text('0%');
    $('#progressText').text('Iniciando respaldo de ' + tipo + ' ...');

    $.post('pages/backup_start.php', {type: tipo}, function(){
        const interval = setInterval(function(){
            $.getJSON('pages/backup_status.php', function(data){
                $('#progressbar').css('width', data.percent + '%').text(data.percent + '%');
                $('#progressText').text(data.msg);
                if(data.percent >= 100){
                    clearInterval(interval);
                    $('#progressText').append('<br>✅ Respaldo completado');
                    setTimeout(function(){ $('#main').load('pages/respaldos.php'); }, 1500);
                }
            });
        }, 1000);
    });
}
</script>
