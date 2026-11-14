<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado. Se não estiver, redireciona para o login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

require_once 'conexao.php';

$id_usuario = $_SESSION['id_usuario'];
$nome_usuario = $_SESSION['nome_usuario'];

try {
    // 1. Busca a foto do usuário
    $stmt_foto = $pdo->prepare("SELECT foto_perfil FROM usuario WHERE id_usuario = :id");
    $stmt_foto->execute([':id' => $id_usuario]);
    $foto_usuario = $stmt_foto->fetchColumn();

    // Define a imagem de exibição
    if (!empty($foto_usuario) && file_exists("uploads/" . $foto_usuario)) {
        $img_boas_vindas = "uploads/" . $foto_usuario;
    } else {
        // Foto padrão caso não tenha enviado nada
        $img_boas_vindas = "https://ui-avatars.com/api/?name=" . urlencode($nome_usuario) . "&background=0EC5E1&color=fff&size=150";
    }

    // 2. Conta o total de atividades do usuário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM atividade WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    $total_atividades = str_pad($stmt->fetchColumn(), 2, '0', STR_PAD_LEFT);

    // 3. Conta o total de lembretes do usuário
    $stmt = $pdo->prepare("SELECT COUNT(l.id_lembrete) FROM lembrete l JOIN atividade a ON l.id_atividade = a.id_atividade WHERE a.id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    $total_lembretes = str_pad($stmt->fetchColumn(), 2, '0', STR_PAD_LEFT);

    // 4. Conta os locais salvos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM atividade WHERE id_usuario = :id AND latitude IS NOT NULL AND longitude IS NOT NULL");
    $stmt->execute([':id' => $id_usuario]);
    $total_locais = str_pad($stmt->fetchColumn(), 2, '0', STR_PAD_LEFT);

    // 5. Busca as próximas 3 atividades
    $stmt = $pdo->prepare("
        SELECT titulo, descricao, data_hora_inicio 
        FROM atividade 
        WHERE id_usuario = :id AND data_hora_inicio >= CURRENT_DATE 
        ORDER BY data_hora_inicio ASC 
        LIMIT 3
    ");
    $stmt->execute([':id' => $id_usuario]);
    $proximas_atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Busca o próximo lembrete futuro
    $stmt = $pdo->prepare("
        SELECT l.data, l.horario, a.titulo 
        FROM lembrete l 
        JOIN atividade a ON l.id_atividade = a.id_atividade 
        WHERE a.id_usuario = :id AND CONCAT(l.data, ' ', l.horario) >= NOW() 
        ORDER BY l.data ASC, l.horario ASC 
        LIMIT 1
    ");
    $stmt->execute([':id' => $id_usuario]);
    $proximo_lembrete = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Em caso de erro, define valores padrão
    $img_boas_vindas = "https://ui-avatars.com/api/?name=" . urlencode($nome_usuario) . "&background=0EC5E1&color=fff&size=150";
    $total_atividades = $total_lembretes = $total_locais = '00';
    $proximas_atividades = [];
    $proximo_lembrete = false;
}

// Array auxiliar para traduzir o mês
$meses_ptbr = [
    '01' => 'JAN', '02' => 'FEV', '03' => 'MAR', '04' => 'ABR', 
    '05' => 'MAI', '06' => 'JUN', '07' => 'JUL', '08' => 'AGO', 
    '09' => 'SET', '10' => 'OUT', '11' => 'NOV', '12' => 'DEZ'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - AMES</title>
    <link rel="icon" href="images/logotipoB-AMES.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand navbar-dark bg-primary custom-navbar"> 
      <div class="container"> 
        <ul class="navbar-nav w-100 justify-content-evenly text-center"> 
          <li class="nav-item">
            <a class="nav-link text-white fw-bold" href="home.php"><i class="fas fa-home me-2"></i>Home</a>
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
            <a class="nav-link text-white" href="profile.php"><i class="fas fa-user me-2"></i>Perfil</a>
          </li>
        </ul> 
      </div> 
    </nav>

    <main class="home-page">
        <div class="dashboard">
            <section class="welcome-card">
                <div>
                    <span class="eyebrow">PAINEL PRINCIPAL</span>
                    <h1>Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
                    <p>Organize seu dia e acompanhe suas atividades de forma simples.</p>
                </div>
                
                <!-- AQUI FOI ALTERADO: Exibe a imagem de perfil -->
                <div class="profile-circle" style="background: transparent; padding: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <img src="<?php echo $img_boas_vindas; ?>" alt="Perfil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                </div>
            </section>

            <!-- Cards de Estatísticas com dados reais do banco -->
            <section class="stats-grid">
                <div class="stat-card cyan">
                    <div class="stat-icon"><i class="fa-solid fa-list-check"></i></div>
                    <div><strong><?php echo $total_atividades; ?></strong><span>Atividades</span></div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-icon"><i class="fa-solid fa-bell"></i></div>
                    <div><strong><?php echo $total_lembretes; ?></strong><span>Lembretes</span></div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div><strong><?php echo $total_locais; ?></strong><span>Locais salvos</span></div>
                </div>
            </section>

            <section class="content-grid">
                <div class="panel tasks-panel">
                    <div class="panel-heading">
                        <div><span class="section-label">HOJE</span><h2>Próximas atividades</h2></div>
                        <a href="activities.php">Ver todas <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <?php if (count($proximas_atividades) > 0): ?>
                        <?php foreach ($proximas_atividades as $atividade): ?>
                            <?php 
                                // Extrai apenas a hora e minuto se data_hora_inicio não for nulo
                                $hora_formatada = !empty($atividade['data_hora_inicio']) ? date('H:i', strtotime($atividade['data_hora_inicio'])) : '--:--'; 
                            ?>
                            <div class="activity-row">
                                <div class="activity-icon"><i class="fa-solid fa-thumbtack"></i></div>
                                <div class="activity-info">
                                    <strong><?php echo htmlspecialchars($atividade['titulo']); ?></strong>
                                    <span><?php echo htmlspecialchars($atividade['descricao']); ?></span>
                                </div>
                                <span class="time"><?php echo $hora_formatada; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-row">
                            <div class="activity-info"><strong>Nenhuma atividade agendada.</strong><span>Aproveite o seu dia livre!</span></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="panel reminder-panel">
                    <div class="panel-heading">
                        <div><span class="section-label">LEMBRETE</span><h2>Próximo compromisso</h2></div>
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>
                    
                    <?php if ($proximo_lembrete): ?>
                        <?php 
                            $dia = date('d', strtotime($proximo_lembrete['data']));
                            $mes_num = date('m', strtotime($proximo_lembrete['data']));
                            $mes_nome = $meses_ptbr[$mes_num];
                            $hora_lembrete = date('H:i', strtotime($proximo_lembrete['horario']));
                        ?>
                        <div class="reminder-box">
                            <div class="calendar"><strong><?php echo $dia; ?></strong><span><?php echo $mes_nome; ?></span></div>
                            <div>
                                <strong><?php echo htmlspecialchars($proximo_lembrete['titulo']); ?></strong>
                                <p>Agendado para às <?php echo $hora_lembrete; ?></p>
                            </div>
                        </div>
                        <a class="location-button" href="location.php"><i class="fa-solid fa-route"></i> Ver localização</a>
                    <?php else: ?>
                        <div class="reminder-box">
                            <div>
                                <strong>Sem lembretes</strong>
                                <p>Você não possui lembretes futuros.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="bottom-panel">
                <div>
                    <span class="section-label">ACESSO RÁPIDO</span>
                    <h2>O que você precisa fazer?</h2>
                </div>
                <div class="quick-actions">
                    <a href="activities.php"><i class="fa-solid fa-plus"></i><span>Nova atividade</span></a>
                    <a href="location.php"><i class="fa-solid fa-map-location-dot"></i><span>Ver mapa</span></a>
                    <a href="notifications.php"><i class="fa-solid fa-clock"></i><span>Lembretes</span></a>
                </div>
            </section>
        </div>
    </main>
</body>
</html>