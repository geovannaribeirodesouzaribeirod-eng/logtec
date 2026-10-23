<?php
/**
 * LogTec - Encerramento de Sessão
 */
require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Reinicia temporariamente para mensagem flash
session_start();
definirToast('info', 'Você encerrou sua sessão no LogTec com segurança.', 'Sessão Finalizada');

header('Location: login.php');
exit;
