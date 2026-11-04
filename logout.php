<?php
// Inicia a sessão para ter acesso às variáveis do usuário logado
session_start();

// Importa a conexão com o banco e a função registrarLog()
require_once 'conexao.php';

// Verifica se o usuário realmente estava logado antes de tentar registrar o log
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    
    // REGISTRA O LOG DE LOGOUT
    registrarLog($pdo, $id_usuario, 'LOGOUT', 'Usuário encerrou a sessão no sistema.');
}

// Limpa o array de variáveis da sessão (remove id_usuario, nome_usuario, etc)
$_SESSION = array();

// Se desejar destruir completamente a sessão, apaga também o cookie de sessão do navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona o usuário para a página de login
header("Location: index.php");
exit;
?>