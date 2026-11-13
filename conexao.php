<?php
// Define o fuso horário correto para garantir que o horário do log fique exato
date_default_timezone_set('America/Sao_Paulo');

$host = 'localhost';
$dbname = 'ames3'; // Confirme se o banco é 'ames3' ou 'ames_db'
$username = 'root'; // Usuário padrão do XAMPP
$password = '';     // Senha padrão do XAMPP geralmente é vazia

try {
    // Cria a conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erros SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Função responsável por gravar ações na tabela log_usuario
function registrarLog($pdo, $id_usuario, $acao, $descricao) {
    try {
        // Pega a data e hora exatas do momento da ação
        $data_atual = date('Y-m-d');
        $horario_atual = date('H:i:s');

        // Prepara o INSERT combinando com as colunas da tabela log_usuario
        $sql = "INSERT INTO log_usuario (id_usuario, data, horario, acao, descricao) 
                VALUES (:id_usuario, :data, :horario, :acao, :descricao)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':data' => $data_atual,
            ':horario' => $horario_atual,
            ':acao' => $acao,
            ':descricao' => $descricao
        ]);
        
        return true;
    } catch (PDOException $e) {
        // GRAVA O ERRO NO CONSOLE/LOG DO PHP SEM QUEBRAR A TELA
        // Dessa forma você consegue ver o motivo de não estar funcionando
        error_log("ERRO NO registrarLog: " . $e->getMessage());
        
        // Se quiser ver o erro direto na tela TEMPORARIAMENTE para descobrir o problema, 
        // descomente a linha abaixo, faça uma ação no sistema e veja a mensagem que aparece:
        // echo "<script>alert('Erro no log: " . $e->getMessage() . "');</script>";
        
        return false; 
    }
}
?>