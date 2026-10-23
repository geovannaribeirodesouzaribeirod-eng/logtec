<?php
/**
 * LogTec - Container e Renderizador de Mensagens Toast
 */
$flashToast = obterToast();
?>
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

<?php if ($flashToast): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Toast) {
            Toast.show({
                type: <?= json_encode($flashToast['tipo']) ?>,
                title: <?= json_encode($flashToast['titulo'] ?? '') ?>,
                message: <?= json_encode($flashToast['mensagem']) ?>
            });
        }
    });
</script>
<?php endif; ?>
