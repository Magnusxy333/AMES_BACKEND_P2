<?php
// Inicia a sessão para manter o usuário logado
session_start();
require_once 'conexao.php';

$mensagem_erro = '';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['txt_email'];
    $senha_digitada = $_POST['txt_password'];
    
    try {
        // Busca o usuário no banco de dados pelo e-mail
        $sql = "SELECT id_usuario, nome, senha FROM usuario WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Verifica se encontrou algum registro
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Compara a senha digitada com o hash salvo no banco
            if (password_verify($senha_digitada, $usuario['senha'])) {
                // Senha correta: cria as variáveis de sessão
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                
                // REGISTRA O LOG DE LOGIN
                registrarLog($pdo, $usuario['id_usuario'], 'LOGIN', 'Usuário autenticou no sistema com sucesso.');
                
                // Redireciona para home.php após o login
                header("Location: home.php");
                exit;
            } else {
                $mensagem_erro = "E-mail ou senha incorretos.";
            }
        } else {
            $mensagem_erro = "E-mail ou senha incorretos.";
        }
    } catch (PDOException $e) {
        $mensagem_erro = "Erro no sistema: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMES - Login</title>
    <link rel="icon" href="images/logotipoB-AMES.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/login.css">
    <style>
      .error-message {
        color: #d32f2f;
        background-color: #fde0e0;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
        font-size: 0.9rem;
      }
    </style>
  </head>
  <body>
    
    <div class="page-container">
      
      <!-- LADO ESQUERDO: INFORMAÇÕES DO SISTEMA -->
      <div class="info-side">
        <h1 class="info-title">O que o AMES resolve?</h1>
        <p class="info-description">
          O AMES ajuda pessoas a organizar e acompanhar suas tarefas diárias, evitando esquecimentos e desorganização.
        </p>
        
        <ul class="info-features">
          <li>
            <i class="fas fa-tasks" style="color: #0F317B; margin-right: 10px; font-size: 1.2rem;"></i>
            <strong>Organização Total:</strong> Controle atividades como tarefas domésticas, compromissos e medicamentos.
          </li>
          <li>
            <i class="fas fa-check-circle" style="color: #0F317B; margin-right: 10px; font-size: 1.2rem;"></i>
            <strong>Acompanhamento:</strong> Veja facilmente o que já foi feito e o que ainda precisa ser realizado.
          </li>
          <li>
            <i class="fas fa-heart" style="color: #0F317B; margin-right: 10px; font-size: 1.2rem;"></i>
            <strong>Feito para Todos:</strong> Especialmente útil para idosos, pessoas com dificuldades de memória e pessoas com uma rotina muito cheia.
          </li>
        </ul>
      </div>

      <!-- LADO DIREITO: LOGIN -->
      <div class="login-side">
        <div class="login-container">
          <div class="login-header">
            <h1 class="login-main-title">
              LOGIN
            </h1>
            <p class="login-subtitle">Entre com suas credenciais para acessar o AMES</p>
            <p class="login-subtitle">e ter acesso as suas funções!</p>
          </div>
          
          <!-- Exibição de erros de login -->
          <?php if (!empty($mensagem_erro)): ?>
            <div class="error-message">
                <?php echo $mensagem_erro; ?>
            </div>
          <?php endif; ?>

          <!-- Formulário apontando para a própria página -->
          <form name="form2" method="post" action="" class="login-form">
            <div class="form-group">
              <label for="email" class="form-label">
                <!-- <i class="fas fa-envelope"></i> --->
                E-mail
              </label>
              <!-- Alterado de text para email e txt_username para txt_email -->
              <input 
                type="email" 
                id="email" 
                name="txt_email" 
                class="form-input"
                placeholder="Digite seu e-mail cadastrado"
                required
              >
            </div>

            <div class="form-group">
              <label for="password" class="form-label">
                 <!-- <i class="fas fa-lock"></i> -->            
                Senha
              </label>
              <div class="password-container">
                <input 
                  type="password" 
                  id="password" 
                  name="txt_password" 
                  class="form-input"
                  placeholder="Digite sua senha"
                  required
                >
                <button type="button" class="password-toggle" id="passwordToggle" aria-label="Mostrar senha">
                  <i class="fas fa-eye"></i>
                  <i class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>

            <div class="form-options">
              <a href="forgot_email_form.php" class="forgot-password">
                 <!-- <i class="fas fa-key"></i> --->
                Esqueceu sua senha?
              </a>
            </div>

            <button type="submit" name="bt_logar" class="login-btn">
              ENTRAR
            </button>
          </form>

          <div class="register-section">
            <!-- Corrigido o link para register.php se você renomeou o arquivo HTML para PHP -->
            <a class="register-link2" href="register.php">
              <div class="register-div">
                <p class="register-text">Ainda não é cadastrado?</p>
                <label class="register-label">
                  <i class="fas fa-user-plus"></i>
                  Cadastre-se aqui !
                </label>
              </div>
            </a>
          </div>

          <div class="login-info">
            <p class="info-text">
              Somos um site voltado para a organização das suas tarefas! 
            </p>
          </div>
        </div>
      </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script>      
      const passwordToggle = document.getElementById('passwordToggle');
      const passwordInput = document.getElementById('password');
      
      if(passwordToggle && passwordInput) {
        const eyeIcon = passwordToggle.querySelector('.fa-eye');
        const eyeSlashIcon = passwordToggle.querySelector('.fa-eye-slash');
        
        passwordToggle.addEventListener('click', function() {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          if (type === 'text') {
            if(eyeIcon) eyeIcon.style.display = 'none';
            if(eyeSlashIcon) eyeSlashIcon.style.display = 'inline-block';
            passwordToggle.setAttribute('aria-label', 'Ocultar senha');
          } else {
            if(eyeIcon) eyeIcon.style.display = 'inline-block';
            if(eyeSlashIcon) eyeSlashIcon.style.display = 'none';
            passwordToggle.setAttribute('aria-label', 'Mostrar senha');
          }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
          if(typeof initTheme === 'function') {
             initTheme();
          }
          if(eyeIcon && eyeSlashIcon){
              eyeIcon.style.display = 'inline-block';
              eyeSlashIcon.style.display = 'none';
          }
        });
      }
    </script>
  </body>
</html>