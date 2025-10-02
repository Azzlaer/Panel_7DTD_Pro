<?php
require_once __DIR__ . '/../config.php';
?>
<div class="container text-light mt-4">
    <h2 class="mb-4 text-center">🖥️ Consola Telnet – 7 Days to Die</h2>
	<p style="text-align: center;"><a title="Comandos TELNET para enviar al servidor" href="https://commands.gg/7dtd" target="_blank">Comandos TELNET para enviar al servidor</a></p>

    <div class="card bg-dark border-secondary p-3">
        <div id="console-output"
             style="background:#000; color:#0f0; height:300px; overflow-y:auto;
                    font-family:monospace; padding:10px; border:1px solid #444;">
            [Consola lista para recibir comandos...]
        </div>

        <form id="telnet-form" class="mt-3 d-flex">
            <input type="text" id="command" class="form-control me-2 bg-dark text-light"
                   placeholder="Escribe un comando Telnet…" autocomplete="off">
            <button class="btn btn-primary">Enviar</button>
        </form>
    </div>
</div>

<script>
$(function(){
    $('#telnet-form').on('submit', function(e){
        e.preventDefault();
        const cmd = $('#command').val().trim();
        if(!cmd) return;

        $('#console-output').append('<div style="color:#fff;">> ' + cmd + '</div>');
        $('#command').val('');

        $.post('pages/telnet_backend.php', { command: cmd }, function(res){
            $('#console-output').append(
                '<pre style="color:#0f0;">' + $('<div>').text(res.output).html() + '</pre>'
            );
            $('#console-output').scrollTop($('#console-output')[0].scrollHeight);
        }, 'json').fail(function(xhr){
            $('#console-output').append('<div style="color:red;">Error: ' + xhr.responseText + '</div>');
        });
    });
});
</script>
