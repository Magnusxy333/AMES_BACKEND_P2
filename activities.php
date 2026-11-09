<?php

include_once 'conexao.php';

// ID do usuário logado 
// mudar para $_SESSION['id_usuario'])
$id_usuario = 0; 


$mensagem = '';
$tipo_mensagem  = '';

if (($_GET['cadastro'] ?? '') === 'sucesso') {
    $mensagem = 'Atividade cadastrada com sucesso!';
    $tipo_mensagem = 'sucesso';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');

    $data_inicio = $_POST['data_hora_inicio'] ?? '';
    $data_fim = $_POST['data_hora_fim'] ?? '';
   
    if ($titulo === '' || $categoria === '') {
    $mensagem = 'Preencha o título e selecione uma categoria.';
    $tipo_mensagem = 'erro';
} else {
   try {
    // Buscar a categoria selecionada
    $sql_categoria = "SELECT id_categoria
                      FROM categoria_atividade
                      WHERE nome_categoria = :categoria";

    $stmt_categoria = $pdo->prepare($sql_categoria);

    $stmt_categoria->execute([
        ':categoria' => $categoria
    ]);

    $id_categoria = $stmt_categoria->fetchColumn();

    if ($id_categoria === false) {
        throw new Exception('Categoria não encontrada.');
    }

    // Converter datas vazias para NULL
    $data_inicio = $data_inicio !== ''
        ? str_replace('T', ' ', $data_inicio)
        : null;

    $data_fim = $data_fim !== ''
        ? str_replace('T', ' ', $data_fim)
        : null;

    // Validar a ordem das datas
    if ($data_inicio !== null && $data_fim !== null
        && strtotime($data_fim) < strtotime($data_inicio)) {
        throw new Exception(
            'A data de término não pode ser anterior à data de início.'
        );
    }

    // Cadastrar atividade
    $sql = "INSERT INTO atividade
                (id_usuario, id_categoria, titulo, descricao,
                 data_hora_inicio, data_hora_fim, status)
            VALUES
                (:id_usuario, :id_categoria, :titulo, :descricao,
                 :data_inicio, :data_fim, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_categoria' => $id_categoria,
        ':titulo' => $titulo,
        ':descricao' => $descricao !== '' ? $descricao : null,
        ':data_inicio' => $data_inicio,
        ':data_fim' => $data_fim,
        ':status' => 'pendente'
    ]);

    // Registrar a ação no histórico
    registrarLog(
        $pdo,
        $id_usuario,
        'Cadastro de atividade',
        'Cadastrou a atividade: ' . $titulo
    );

    header('Location: activities.php?cadastro=sucesso', true, 303);
    exit;

} catch (Exception $e) {
    $mensagem = 'Erro ao cadastrar: ' . $e->getMessage();
    $tipo_mensagem = 'erro';
}
}
}


// Busca as atividades e o nome da categoria relacionada no banco
$sql = "SELECT a.*, c.nome_categoria 
        FROM atividade a 
        LEFT JOIN categoria_atividade c ON a.id_categoria = c.id_categoria 
        WHERE a.id_usuario = ? 
        ORDER BY a.data_hora_criacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

$atividadesPendentes = array_filter(
    $atividades,
    fn($a) => strtolower($a['status'] ?? '') === 'pendente'
);

$atividadesConcluidas = array_filter(
    $atividades,
    fn($a) => strtolower($a['status'] ?? '') === 'concluida'
);

// Contadores dinâmicos para o resumo
$totalAtividades = count($atividades);
$totalPendentes = count($atividadesPendentes);
$totalConcluidas = count($atividadesConcluidas);

// Funções auxiliares para definir as classes CSS e os ícones com base na categoria
function obterClasseTipo($nomeCategoria) {
    switch (mb_strtolower(trim($nomeCategoria ?? ''))) {
        case 'medicamento': return 'type-medicamento';
        case 'tarefa domestica': return 'type-domestica';
        case 'compromisso': return 'type-compromisso';
        default: return 'type-outro';
    }
}

function obterIconeTipo($nomeCategoria) {
    switch (mb_strtolower(trim($nomeCategoria ?? ''))) {
        case 'medicamento': return 'fa-pills';
        case 'tarefa domestica': return 'fa-house';
        case 'compromisso': return 'fa-calendar-check';
        default: return 'fa-bookmark';
    }
}

function formatarData($data) {
    if (empty($data)) {
        return 'Não informada';
    }

    return date('d/m/Y, H:i', strtotime($data));
}





?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AMES - Atividades</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link rel="stylesheet" href="styles/activities.css">

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
            <a class="nav-link text-white fw-bold" href="activities.php"><i class="fas fa-tasks me-2"></i>Atividades</a>
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

    <main class="activities-page">

<section class="page-heading">

    <div class="page-brand">

        <div class="page-icon-container">
            <img
                src="images/logotipoB-AMES.png"
                alt="Logotipo do AMES"
                class="page-icon"
            >
        </div>

        <div class="page-brand-text">
            <span class="page-eyebrow">
                Organização diária
            </span>

            <h1>Minhas atividades</h1>

            <p>
                Cadastre, acompanhe e conclua suas atividades em um só lugar.
            </p>
        </div>

    </div>
<!--
    <button
        type="button"
        class="new-activity-btn"
        aria-disabled="true"
    >
        <i class="fas fa-plus"></i>
        Nova atividade
    </button>
-->
</section>

    <!-- RESUMO DINÂMICO -->
        <section class="activities-summary">
            <article class="summary-card">
                <div class="summary-icon total-icon"><i class="fas fa-layer-group"></i></div>
                <div><span>Total</span><strong><?= $totalAtividades ?></strong></div>
            </article>
            <article class="summary-card">
                <div class="summary-icon pending-icon"><i class="fas fa-clock"></i></div>
                <div><span>Pendentes</span><strong><?= $totalPendentes ?></strong></div>
            </article>
            <article class="summary-card">
                <div class="summary-icon completed-icon"><i class="fas fa-circle-check"></i></div>
                <div><span>Concluídas</span><strong><?= $totalConcluidas ?></strong></div>
            </article>
        </section>

        <section class="activities-workspace">

            <!-- FORMULÁRIO APENAS VISUAL -->
           
            <article class="activity-form-card">

                <div class="card-heading">
                    <div class="card-heading-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>

                    <div>
                        <h2>Cadastrar atividade</h2>
                        <p>Preencha os dados para adicionar uma nova atividade.</p>
                    </div>
                </div>

               
<form class="activity-form" method="POST" action="">

<div class="activity-form-grid">

    <div class="form-field full-field">
        <label for="activityTitle">
            Título da atividade
        </label>

        <input
            type="text"
            id="activityTitle"
            placeholder="Ex.: Tomar medicamento"
            name = "titulo"
            maxlength="150"
            required
        >
    </div>

    <div class="form-field full-field">
        <label for="activityDescription">
            Descrição
            <span class="optional-label">(opcional)</span>
        </label>

        <textarea
            id="activityDescription"
            rows="3"
            name = "descricao"
            placeholder="Inclua informações importantes sobre a atividade"
            maxlength="500"
        ></textarea>
    </div>

    <div class="form-field full-field">
        <label for="activityType">
            Tipo de atividade
        </label>

        <select id="activityType" name="categoria" required>
            <option value="" selected>Selecione o tipo</option>
            <option value="tarefa domestica">Tarefa doméstica</option>
            <option value="compromisso">Compromisso</option>
            <option value="medicamento">Medicamento</option>
            <option value="outro">Outro</option>
        </select>
    </div>
 <!--
<div class="form-field">
    <label for="creationDateTime">
        Data e hora da criação
    </label>

    <input
        type="datetime-local"
        id="creationDateTime"
        step="60"
    >
    
</div>
-->
<div class="form-field">
    <label for="startDateTime">
        Data e hora de início
    </label>

    <input
        type="datetime-local"
        id="startDateTime"
        name = "data_hora_inicio"
        step="60"
    >
</div>

<div class="form-field">
    <label for="endDateTime">
        Data e hora de término
    </label>

    <input
        type="datetime-local"
        id="endDateTime"
        name = "data_hora_fim"
        step="60"
    >
</div>

</div>

<?php if ($mensagem !== ''): ?>
    <p class="form-message <?= $tipo_mensagem ?>">
        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<div class="form-actions">
    <button type="reset" class="clear-activity-btn">
        <i class="fas fa-eraser"></i>
        Limpar campos
    </button>

    <button type="submit" class="save-activity-btn">
        <i class="fas fa-plus-circle"></i>
        Cadastrar atividade
    </button>
</div>
</form>              

            </article>

            <!-- ATIVIDADES FICTÍCIAS E ESTÁTICAS -->
            <article class="activities-list-card">

                <div class="card-heading list-heading">
                    <div class="card-heading-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                    <div>
                        <h2>Acompanhar atividades</h2>
                        <p>
                            Consulte o que ainda precisa ser feito e o que foi concluído.
                        </p>
                    </div>
                </div>

                <!-- FILTROS APENAS VISUAIS -->
                <div class="filter-toolbar">

                <div class="search-field">
                    <i class="fas fa-search"></i>

                    <input
                        type="search"
                        id="activitySearch"
                        placeholder="Buscar atividade"
                        aria-label="Buscar atividade"
                        autocomplete="off"
                    >
                </div>

                    <select>
                        <option selected>Todos os tipos</option>
                        <option>Tarefa doméstica</option>
                        <option>Compromisso</option>
                        <option>Medicamento</option>
                        <option>Outro</option>
                    </select>

                </div>

            
<div class="activity-columns">

    <!-- ATIVIDADES PENDENTES -->
    <section class="activity-panel pending-panel">

        <div class="panel-heading">
            <div>
                <span class="status-dot pending-dot"></span>
                <h3>Pendentes</h3>
            </div>

            <span class="panel-count">
                <?= $totalPendentes ?>
            </span>
        </div>

        <div class="activity-list">

            <?php if (empty($atividadesPendentes)): ?>
                <p>Nenhuma atividade pendente.</p>
            <?php else: ?>

                <?php foreach ($atividadesPendentes as $atividade): ?>

                    <article class="activity-item">

                        <div class="activity-item-top">

                            <span class="activity-type-badge <?= obterClasseTipo($atividade['nome_categoria']) ?>">
                                <i class="fas <?= obterIconeTipo($atividade['nome_categoria']) ?>"></i>

                                <?= htmlspecialchars($atividade['nome_categoria'] ?? 'Outro') ?>
                            </span>

                            <span class="activity-status">
                                Pendente
                            </span>

                        </div>

                        <h4>
                            <?= htmlspecialchars($atividade['titulo'] ?? '') ?>
                        </h4>

                        <p class="activity-description">
                            <?= htmlspecialchars($atividade['descricao'] ?? '') ?>
                        </p>

                        <dl class="activity-dates">

                            <div>
                                <dt>
                                    <i class="fas fa-calendar-plus"></i>
                                    Criada em
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_criacao']) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="fas fa-play"></i>
                                    Início
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_inicio']) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="fas fa-flag-checkered"></i>
                                    Término
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_fim']) ?>
                                </dd>
                            </div>

                        </dl>

                        <button type="button" class="status-button" disabled>
                            <i class="fas fa-check"></i>
                            Marcar como concluída
                        </button>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>


    <!-- ATIVIDADES CONCLUÍDAS -->
    <section class="activity-panel completed-panel">

        <div class="panel-heading">
            <div>
                <span class="status-dot completed-dot"></span>
                <h3>Concluídas</h3>
            </div>

            <span class="panel-count">
                <?= $totalConcluidas ?>
            </span>
        </div>

        <div class="activity-list">

            <?php if (empty($atividadesConcluidas)): ?>
                <p>Nenhuma atividade concluída.</p>
            <?php else: ?>

                <?php foreach ($atividadesConcluidas as $atividade): ?>

                    <article class="activity-item is-completed">

                        <div class="activity-item-top">

                            <span class="activity-type-badge <?= obterClasseTipo($atividade['nome_categoria']) ?>">
                                <i class="fas <?= obterIconeTipo($atividade['nome_categoria']) ?>"></i>

                                <?= htmlspecialchars($atividade['nome_categoria'] ?? 'Outro') ?>
                            </span>

                            <span class="activity-status">
                                Concluída
                            </span>

                        </div>

                        <h4>
                            <?= htmlspecialchars($atividade['titulo'] ?? '') ?>
                        </h4>

                        <p class="activity-description">
                            <?= htmlspecialchars($atividade['descricao'] ?? '') ?>
                        </p>

                        <dl class="activity-dates">

                            <div>
                                <dt>
                                    <i class="fas fa-calendar-plus"></i>
                                    Criada em
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_criacao']) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="fas fa-play"></i>
                                    Início
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_inicio']) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>
                                    <i class="fas fa-flag-checkered"></i>
                                    Término
                                </dt>
                                <dd>
                                    <?= formatarData($atividade['data_hora_fim']) ?>
                                </dd>
                            </div>

                        </dl>

                        <button type="button" class="status-button" disabled>
                            <i class="fas fa-rotate-left"></i>
                            Reabrir atividade
                        </button>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>

</div>

            </article>

        </section>
    </main>

</body>
</html>