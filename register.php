<?php
// Inclui o arquivo de conexão (que agora também possui a função registrarLog)
require_once 'conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bt_incluir'])) {
    // Coleta os dados do formulário
    $nome = $_POST['txt_nome'];
    $sobrenome = $_POST['txt_sobrenome'];
    $email = $_POST['txt_email'];
    $senha_digitada = $_POST['txt_password'];
    
    // Cria o hash seguro da senha
    $senha_hash = password_hash($senha_digitada, PASSWORD_DEFAULT);
    
    try {
        // Prepara o INSERT na tabela usuario
        $sql = "INSERT INTO usuario (nome, sobrenome, email, senha) VALUES (:nome, :sobrenome, :email, :senha)";
        $stmt = $pdo->prepare($sql);
        
        // Substitui os parâmetros
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':sobrenome', $sobrenome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hash);
        
        if ($stmt->execute()) {
            // CAPTURA O ID GERADO AUTOMATICAMENTE PELO BANCO
            $id_novo_usuario = $pdo->lastInsertId();
            
            // REGISTRA O LOG DE CADASTRO
            registrarLog($pdo, $id_novo_usuario, 'CADASTRO_NOVO', 'Usuário realizou o cadastro no sistema.');
            
            // Exibe alerta e manda o usuário para a tela de login (index.php)
            echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='index.php';</script>";
        }
    } catch (PDOException $e) {
        // Tratamento de erro (ex: e-mail já cadastrado)
        echo "<script>alert('Erro ao cadastrar: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EMAS - Cadastro</title>
    <link rel="stylesheet" href="styles/register.css" />
  </head>
  <body>
    <div class="main">
        <div class="register-container">
          <div class="register-header">
            <h1 class="register-main-title">
              CADASTRO
            </h1>
            <p class="register-subtitle">Crie sua conta para acessar o AMES</p>
            <p class="register-subtitle">Lembre-se de criar uma senha forte para maior segurança!</p>
          </div>

          <form class="form-content register-form" method="POST" action="" onsubmit="return validateForm();">
            <div class="form-group">
              <label for="username" class="form-label">
                Usuário
              </label>
              <input
                type="text"
                id="username"
                class="form-input"
                placeholder="Digite seu username (3-50 caracteres)"
                name="txt_username"
                pattern="[A-Za-z0-9_]{3,50}"
                title="Username deve conter apenas letras, números e underscore (3-50 caracteres)"
                required
              />
              <small class="email-hint">Apenas letras, números e _ (underline)</small>
            </div>

            <div class="form-row">
              <div class="form-group1">
                <label for="nome" class="form-label">
                  Nome
                </label>
                <input
                  type="text"
                  id="nome"
                  class="form-input"
                  placeholder="Digite o seu nome"
                  name="txt_nome"
                  pattern="[A-Za-zÀ-ÿ\s]{2,100}"
                  title="Nome deve conter apenas letras (2-100 caracteres)"
                  required
                />
                <small class="email-hint">Campos obrigatórios</small>
              </div>

              <div class="form-group1">
                <label for="sobrenome" class="form-label">
                  Sobrenome
                </label>
                <input
                  type="text"
                  id="sobrenome"
                  class="form-input"
                  placeholder="Digite o seu sobrenome"
                  name="txt_sobrenome"
                  pattern="[A-Za-zÀ-ÿ\s]{2,100}"
                  title="Sobrenome deve conter apenas letras (2-100 caracteres)"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">
                Email
              </label>
              <input
                type="email"
                id="email"
                class="form-input"
                placeholder="Digite o seu Email (ex: usuario@gmail.com)"
                name="txt_email"
                pattern="[a-z0-9._%+-]+@(gmail\.com|outlook\.com|hotmail\.com|yahoo\.com|icloud\.com|protonmail\.com|live\.com|msn\.com)$"
                title="Use Gmail, Outlook, Hotmail, Yahoo, iCloud ou ProtonMail"
                required
              />
              <small class="email-hint">Aceitamos: Gmail, Outlook, Hotmail, Yahoo, iCloud, ProtonMail</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="password" class="form-label">
                  Senha
                </label>
                
                <div class="password-container">
                  <input
                    type="password"
                    id="password"
                    class="form-input"
                    placeholder="Digite a sua senha"
                    name="txt_password"
                    minlength="6"
                    required
                    oninput="checkPasswordStrength()"
                  />
                  
                  <button type="button" class="password-toggle" id="passwordToggle" aria-label="Mostrar senha">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash"></i>
                  </button>
      
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                <small class="email-hint">No mínimo 6 caracteres</small>
              </div>

              <div class="form-group">
                <label for="confirmPassword" class="form-label">
                  Confirmar Senha
                </label>
                <div class="password-container">
                  <input
                    type="password"
                    id="confirmPassword"
                    class="form-input"
                    placeholder="Sua senha novamente"
                    name="txt_confirmPassword"
                    minlength="6"
                    required
                  />
                  <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Mostrar senha">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash"></i>
                  </button>
                </div>
                <div id="passwordMatch" class="password-strength"></div>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn-clean" onclick="clean();">
                LIMPAR
              </button>

              <button type="submit" class="btn-submit" name="bt_incluir" value="CADASTRAR">
                ENVIAR
              </button>
            </div>
          </form> 

          <div class="login-redirect">
            <p>Já possui uma conta?</p>
            <a href="index.php" class="login-link">
              Fazer login
            </a>
          </div>
        </div>
    </div>
    <script>
      // ===== SISTEMA DE MOSTRAR/OCULTAR SENHA =====
      function setupPasswordToggle(passwordInputId, toggleButtonId) {
        const passwordToggle = document.getElementById(toggleButtonId);
        const passwordInput = document.getElementById(passwordInputId);
        
        if (!passwordToggle || !passwordInput) return;
        
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
        
        if(eyeIcon) eyeIcon.style.display = 'inline-block';
        if(eyeSlashIcon) eyeSlashIcon.style.display = 'none';
      }
      
      // ===== VALIDAÇÃO DE FORÇA DA SENHA =====
      function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthText = document.getElementById('passwordStrength');
        const confirmPassword = document.getElementById('confirmPassword').value;
        const matchText = document.getElementById('passwordMatch');
        
        if (password.length === 0) {
          strengthText.innerHTML = '';
          strengthText.className = 'password-strength';
          return;
        }
        
        let strength = 0;
        let tips = [];
        
        if (password.length < 6) {
          tips.push('Mínimo 6 caracteres');
        } else if (password.length < 8) {
          strength += 1;
        } else {
          strength += 2;
        }
        
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        let strengthClass, strengthMessage;
        if (strength < 3) {
          strengthClass = 'strength-weak';
          strengthMessage = 'Senha fraca';
          tips.push('Adicione letras maiúsculas, números ou símbolos');
        } else if (strength < 5) {
          strengthClass = 'strength-medium';
          strengthMessage = 'Senha média';
        } else {
          strengthClass = 'strength-strong';
          strengthMessage = 'Senha forte';
        }
        
        strengthText.innerHTML = `<span class="${strengthClass}">${strengthMessage}</span>`;
        strengthText.className = `password-strength ${strengthClass}`;
        
        if (confirmPassword.length > 0) {
          if (password === confirmPassword) {
            matchText.innerHTML = '<span class="strength-strong">✓ Senhas coincidem</span>';
            matchText.className = 'password-strength strength-strong';
          } else {
            matchText.innerHTML = '<span class="strength-weak">✗ Senhas não coincidem</span>';
            matchText.className = 'password-strength strength-weak';
          }
        }
      }
      
      document.getElementById('confirmPassword').addEventListener('input', checkPasswordStrength);
      
      // ===== VALIDAÇÃO DO FORMULÁRIO =====
      function validateForm() {
        const username = document.getElementById('username').value;
        const nome = document.getElementById('nome').value;
        const sobrenome = document.getElementById('sobrenome').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        const allowedDomains = [
          'gmail.com', 'outlook.com', 'hotmail.com', 
          'yahoo.com', 'icloud.com', 'protonmail.com',
          'live.com', 'msn.com'
        ];
        
        if (username.length < 3 || username.length > 50) {
          alert('O username deve ter entre 3 e 50 caracteres');
          return false;
        }
        
        if (!/^[A-Za-z0-9_]+$/.test(username)) {
          alert('O username deve conter apenas letras, números e underscore (_)');
          return false;
        }
        
        if (!/^[A-Za-zÀ-ÿ\s]{2,100}$/.test(nome)) {
          alert('O nome deve conter apenas letras (2-100 caracteres)');
          return false;
        }
        
        if (!/^[A-Za-zÀ-ÿ\s]{2,100}$/.test(sobrenome)) {
          alert('O sobrenome deve conter apenas letras (2-100 caracteres)');
          return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          alert('Por favor, insira um email válido');
          return false;
        }
        
        const emailParts = email.split('@');
        if (emailParts.length !== 2) {
          alert('Formato de email inválido');
          return false;
        }
        
        const domain = emailParts[1].toLowerCase();
        if (!allowedDomains.includes(domain)) {
          alert('Por favor, use um email dos provedores permitidos.');
          return false;
        }
        
        const tempDomains = ['mailinator.com', 'tempmail.com', 'guerrillamail.com', '10minutemail.com'];
        if (tempDomains.includes(domain)) {
          alert('Emails temporários não são permitidos');
          return false;
        }
        
        if (password.length < 6) {
          alert('A senha deve ter no mínimo 6 caracteres');
          return false;
        }
        
        if (password.length > 72) {
          alert('A senha deve ter no máximo 72 caracteres');
          return false;
        }
        
        if (password !== confirmPassword) {
          alert('As senhas não coincidem');
          return false;
        }
        
        return true; 
      }
      
      // ===== FUNÇÃO LIMPAR FORMULÁRIO =====
      function clean() {
        if (confirm('Tem certeza que deseja limpar todos os campos?')) {
          document.querySelector('.register-form').reset();
          document.getElementById('passwordStrength').innerHTML = '';
          document.getElementById('passwordMatch').innerHTML = '';
        }
      }
      
      document.addEventListener('DOMContentLoaded', function() {
        if(typeof initTheme === 'function') {
           initTheme(); 
        }
        setupPasswordToggle('password', 'passwordToggle');
        setupPasswordToggle('confirmPassword', 'confirmPasswordToggle');
        
        if (document.getElementById('password') && document.getElementById('password').value) {
          checkPasswordStrength();
        }
      });
    </script>
  </body>
</html>