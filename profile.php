<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';
$id_usuario = $_SESSION['id_usuario'];
$mensagem = '';

// ==========================================
// 1. PROCESSAMENTO DO UPLOAD DA FOTO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_foto'])) {
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoes_permitidas)) {
            $novo_nome = "perfil_" . $id_usuario . "_" . time() . "." . $extensao;
            $diretorio = "uploads/";
            
            // Cria a pasta uploads se ela não existir
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $diretorio . $novo_nome)) {
                $sql = "UPDATE usuario SET foto_perfil = :foto WHERE id_usuario = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':foto' => $novo_nome, ':id' => $id_usuario]);
                
                // REGISTRA O LOG DE ALTERAÇÃO DE FOTO
                registrarLog($pdo, $id_usuario, 'ATUALIZACAO_FOTO', 'Usuário alterou a foto de perfil.');
                
                $mensagem = "<div class='alert alert-success'>Foto de perfil atualizada!</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>Apenas arquivos JPG, PNG ou GIF são permitidos.</div>";
        }
    }
}

// ==========================================
// 2. ATUALIZAÇÃO DOS DADOS DE TEXTO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_perfil'])) {
    $novo_nome = trim($_POST['txt_nome']);
    $novo_sobrenome = trim($_POST['txt_sobrenome']);
    $novo_email = trim($_POST['txt_email']);
    $novo_telefone = trim($_POST['txt_telefone']);
    $nova_data_nasc = trim($_POST['txt_data_nascimento']);
    $novo_endereco = trim($_POST['txt_endereco']);
    
    if(empty($nova_data_nasc)) {
        $nova_data_nasc = null;
    }

    try {
        $sql_update = "UPDATE usuario SET 
                        nome = :nome, 
                        sobrenome = :sobrenome, 
                        email = :email, 
                        telefone = :telefone, 
                        data_nascimento = :data_nascimento, 
                        endereco = :endereco 
                      WHERE id_usuario = :id";
                      
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            ':nome' => $novo_nome,
            ':sobrenome' => $novo_sobrenome,
            ':email' => $novo_email,
            ':telefone' => $novo_telefone,
            ':data_nascimento' => $nova_data_nasc,
            ':endereco' => $novo_endereco,
            ':id' => $id_usuario
        ]);
        
        $_SESSION['nome_usuario'] = $novo_nome;
        
        // REGISTRA O LOG DE ALTERAÇÃO DE DADOS CADASTRAIS
        registrarLog($pdo, $id_usuario, 'ATUALIZACAO_PERFIL', 'Usuário atualizou seus dados cadastrais.');
        
        $mensagem = "<div class='alert alert-success'>Perfil atualizado com sucesso!</div>";
    } catch (PDOException $e) {
        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar: " . $e->getMessage() . "</div>";
    }
}

// ==========================================
// 3. BUSCA DOS DADOS PARA EXIBIÇÃO
// ==========================================
try {
    $stmt = $pdo->prepare("SELECT nome, sobrenome, email, telefone, data_nascimento, endereco, foto_perfil FROM usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    $dados_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $nome_atual = $dados_usuario['nome'];
    $sobrenome_atual = $dados_usuario['sobrenome'];
    $email_atual = $dados_usuario['email'];
    $telefone_atual = $dados_usuario['telefone'] ?? '';
    $data_nascimento_atual = $dados_usuario['data_nascimento'] ?? '';
    $endereco_atual = $dados_usuario['endereco'] ?? '';
    $foto_atual = $dados_usuario['foto_perfil'] ?? '';
    
    // Define qual foto mostrar
    if (!empty($foto_atual) && file_exists("uploads/" . $foto_atual)) {
        $caminho_foto = "uploads/" . $foto_atual;
    } else {
        $caminho_foto = "https://ui-avatars.com/api/?name=" . urlencode($nome_atual . ' ' . $sobrenome_atual) . "&background=0EC5E1&color=fff&size=150";
    }
    
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMES - Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/profile.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body> 
    
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand navbar-dark bg-primary custom-navbar"> 
      <div class="container"> 
        <ul class="navbar-nav w-100 justify-content-evenly text-center"> 
          <li class="nav-item">
            <a class="nav-link text-white" href="home.php"><i class="fas fa-home me-2"></i>Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="activities.php"><i class="fas fa-tasks me-2"></i>Atividades</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="location.php"><i class="fas fa-map-marker-alt me-2"></i>Localização</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="notifications.php"><i class="fas fa-bell me-2"></i>Lembretes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white fw-bold" href="profile.php"><i class="fas fa-user me-2"></i>Perfil</a>
          </li>
        </ul> 
      </div> 
    </nav>

    <!-- CONTEÚDO PRINCIPAL DO PERFIL -->
    <div class="container mt-5 mb-5">
        <div class="row">
            
            <!-- COLUNA ESQUERDA: FOTO E RESUMO -->
            <div class="col-lg-4 mb-4">
                <div class="card profile-card border-0 shadow-sm text-center p-4">
                    <div class="profile-img-container mx-auto mb-3">
                        <img src="<?php echo $caminho_foto; ?>" alt="Foto de Perfil" class="rounded-circle img-fluid profile-img" style="width: 150px; height: 150px; object-fit: cover;">
                        
                        <form method="POST" action="" enctype="multipart/form-data" id="form-foto" style="display: none;">
                            <input type="file" name="foto_perfil" id="input-foto" accept="image/*" onchange="document.getElementById('form-foto').submit();">
                            <input type="hidden" name="upload_foto" value="1">
                        </form>
                        
                        <button type="button" class="btn btn-sm btn-light change-photo-btn" title="Alterar Foto" onclick="document.getElementById('input-foto').click();">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h4 class="mb-1"><?php echo htmlspecialchars($nome_atual . ' ' . $sobrenome_atual); ?></h4>
                    <p class="text-muted mb-3">Membro do AMES</p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary"><i class="fas fa-key me-2"></i>Alterar Senha</button>
                        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Sair da Conta</a>
                    </div>
                </div>
            </div>

            <!-- COLUNA DIREITA: FORMULÁRIO DE DADOS -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="mb-4 pb-2 border-bottom">Meus Dados</h4>
                    
                    <?php echo $mensagem; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nome</label>
                                <input type="text" name="txt_nome" class="form-control bg-light" value="<?php echo htmlspecialchars($nome_atual); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sobrenome</label>
                                <input type="text" name="txt_sobrenome" class="form-control bg-light" value="<?php echo htmlspecialchars($sobrenome_atual); ?>" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">E-mail</label>
                                <input type="email" name="txt_email" class="form-control bg-light" value="<?php echo htmlspecialchars($email_atual); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Telefone de Contato</label>
                                <input type="text" name="txt_telefone" class="form-control bg-light" value="<?php echo htmlspecialchars($telefone_atual); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Data de Nascimento</label>
                                <input type="date" name="txt_data_nascimento" class="form-control bg-light" value="<?php echo htmlspecialchars($data_nascimento_atual); ?>">
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold">Endereço Principal</label>
                                <input type="text" name="txt_endereco" class="form-control bg-light" value="<?php echo htmlspecialchars($endereco_atual); ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" name="atualizar_perfil" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>