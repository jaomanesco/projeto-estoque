<?php
session_start();

// Destroi todas as variáveis de sessão
$_SESSION = array();

// Se necessário, também destrua a sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrua a sessão
session_destroy();

// Redireciona para a página de login ou outra página após logout
header("Location: ../login.php");
exit();