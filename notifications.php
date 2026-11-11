<?php
include_once 'conexao.php';
$id_usuario = 0; // Substitua pelo ID do usuário logado


$sql = "SELECT id_atividade, titulo
        FROM atividade
        WHERE id_usuario = :id_usuario
        ORDER BY titulo ASC";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id_usuario' => $id_usuario
]);

$atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);


$mensagem = '';
$tipo_mensagem = '';

if (($_GET['acao'] ?? '') === 'excluir') {
    $mensagem = 'Lembrete excluído com sucesso!';
    $tipo_mensagem = 'sucesso';
}


if (($_GET['cadastro'] ?? '') === 'sucesso') {
    $mensagem = 'Lembrete cadastrado com sucesso!';
    $tipo_mensagem = 'sucesso';
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !isset($_POST['acao_lembrete'])
) {

    $id_atividade = filter_var(
        $_POST['id_atividade'] ?? null,
        FILTER_VALIDATE_INT
    );

    $mensagem_lembrete = trim($_POST['mensagem'] ?? '');
    $data_hora = $_POST['data_hora_lembrete'] ?? '';

    if (!$id_atividade || $data_hora === '') {
        $mensagem = 'Selecione uma atividade e informe a data do lembrete.';
        $tipo_mensagem = 'erro';

    } else {
        try {
            // Verificar se a atividade pertence ao usuário
            $sql = "SELECT id_atividade
                    FROM atividade
                    WHERE id_atividade = :id_atividade
                    AND id_usuario = :id_usuario";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_atividade' => $id_atividade,
                ':id_usuario' => $id_usuario
            ]);

            if (!$stmt->fetchColumn()) {
                throw new Exception('Atividade não encontrada.');
            }

            // Validar e separar data e horário
            $dataObjeto = DateTimeImmutable::createFromFormat(
                '!Y-m-d\TH:i',
                $data_hora
            );

            if (
                !$dataObjeto ||
                $dataObjeto->format('Y-m-d\TH:i') !== $data_hora
            ) {
                throw new Exception('Data ou horário inválido.');
            }

            $data = $dataObjeto->format('Y-m-d');
            $horario = $dataObjeto->format('H:i:s');

            // Cadastrar lembrete
            $sql = "INSERT INTO lembrete
                        (id_atividade, data, horario, mensagem, status)
                    VALUES
                        (:id_atividade, :data, :horario, :mensagem, :status)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':id_atividade' => $id_atividade,
                ':data' => $data,
                ':horario' => $horario,
                ':mensagem' => $mensagem_lembrete !== ''
                    ? $mensagem_lembrete
                    : null,
                ':status' => 'agendado'
            ]);

            registrarLog(
                $pdo,
                $id_usuario,
                'Cadastro de lembrete',
                'Cadastrou um lembrete para a atividade ' . $id_atividade
            );

            header('Location: notifications.php?cadastro=sucesso', true, 303);
            exit;

        } catch (Exception $e) {
            $mensagem = 'Erro ao cadastrar: ' . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}
// Buscar lembretes do usuário
$sql = "SELECT
            l.*,
            a.titulo AS titulo_atividade,
            c.nome_categoria
        FROM lembrete l
        INNER JOIN atividade a
            ON l.id_atividade = a.id_atividade
        LEFT JOIN categoria_atividade c
            ON a.id_categoria = c.id_categoria
        WHERE a.id_usuario = :id_usuario
        ORDER BY l.data ASC, l.horario ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_usuario' => $id_usuario
]);

$lembretes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lembretesAgendados = array_filter(
    $lembretes,
    fn($l) => strtolower($l['status'] ?? '') === 'agendado'
);

$lembretesEnviados = array_filter(
    $lembretes,
    fn($l) => strtolower($l['status'] ?? '') === 'enviado'
);

$totalLembretes = count($lembretes);
$totalAgendados = count($lembretesAgendados);
$totalEnviados = count($lembretesEnviados);


function formatarDataLembrete($data, $horario) {
    if (empty($data) || empty($horario)) {
        return 'Não informado';
    }

    return date('d/m/Y \à\s H:i', strtotime($data . ' ' . $horario));
}



if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['acao_lembrete'] ?? '') === 'excluir'
) {
    $id_lembrete = filter_var(
        $_POST['id_lembrete'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id_lembrete) {
        die('Lembrete inválido.');
    }

    try {
        // Buscar o lembrete e verificar se pertence ao usuário
        $sql = "SELECT l.id_lembrete, a.titulo
                FROM lembrete l
                INNER JOIN atividade a
                    ON l.id_atividade = a.id_atividade
                WHERE l.id_lembrete = :id_lembrete
                AND a.id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_lembrete' => $id_lembrete,
            ':id_usuario' => $id_usuario
        ]);

        $lembrete = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lembrete) {
            throw new Exception('Lembrete não encontrado.');
        }

        // Excluir o lembrete pertencente ao usuário
        $sql = "DELETE l FROM lembrete l
                INNER JOIN atividade a
                    ON l.id_atividade = a.id_atividade
                WHERE l.id_lembrete = :id_lembrete
                AND a.id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_lembrete' => $id_lembrete,
            ':id_usuario' => $id_usuario
        ]);

        registrarLog(
            $pdo,
            $id_usuario,
            'Exclusão de lembrete',
            'Excluiu um lembrete da atividade: ' . $lembrete['titulo']
        );

        header('Location: notifications.php?acao=excluir', true, 303);
        exit;

    } catch (Exception $e) {
        die('Erro ao excluir lembrete: ' .
            htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AMES - Lembretes</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link rel="stylesheet" href="styles/notification.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="styles/check_notifications.css">
    <script src="scripts/notifications.js" defer></script>
    <script src="scripts/check_notifications.js" defer></script>
</head>

<body>

   <!-- NAVBAR -->
    <nav class="navbar navbar-expand navbar-dark bg-primary custom-navbar"> 
      <div class="container"> 
        <ul class="navbar-nav w-100 justify-content-evenly text-center"> 
          <li class="nav-item">
            <a class="nav-link text-white " href="home.php"><i class="fas fa-home me-2"></i>Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="activities.php"><i class="fas fa-tasks me-2"></i>Atividades</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="location.php"><i class="fas fa-map-marker-alt me-2"></i>Localização</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white fw-bold" href="notifications.php"><i class="fas fa-bell me-2"></i>Lembretes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="profile.php"><i class="fas fa-user me-2"></i>Perfil</a>
          </li>
        </ul> 
      </div> 
    </nav>

    <main class="notifications-page">

        <!-- TÍTULO -->
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
            Não deixe passar
        </span>

        <h1>Meus lembretes</h1>

        <p>
            Crie lembretes relacionados às suas atividades cadastradas.
        </p>
    </div>

</div>

        <!-- RESUMO ESTÁTICO -->
        <section class="reminders-summary">

            <article class="summary-card">
                <div class="summary-icon total-icon">
                    <i class="fas fa-bell"></i>
                </div>

                <div>
                    <span>Total</span>
                    <strong><?= $totalLembretes ?></strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon scheduled-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <span>Agendados</span>
                    <strong><?= $totalAgendados ?></strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon sent-icon">
                    <i class="fas fa-circle-check"></i>
                </div>

                <div>
                    <span>Enviados</span>
                    <strong><?= $totalEnviados ?></strong>
                </div>
            </article>

        </section>

        <section class="reminders-workspace">

            <!-- CADASTRO DO LEMBRETE -->
            <article class="reminder-form-card">

                <div class="card-heading">
                    <div class="card-heading-icon">
                        <i class="fas fa-bell"></i>
                    </div>

                    <div>
                        <h2>Cadastrar lembrete</h2>

                        <p>
                            Relacione o lembrete a uma atividade.
                        </p>
                    </div>
                </div>

                <!-- DIV EM VEZ DE FORM:
                     impede qualquer envio de informações -->
               <form class="reminder-form" method="POST" action="">

                    <div class="reminder-form-grid">

                        
<div class="form-field full-field">
    <label for="reminderActivity">
        Atividade relacionada
    </label>

    <select id="reminderActivity" name="id_atividade" required>
        <option value="" selected>Selecione uma atividade</option>

        <?php foreach ($atividades as $atividade): ?>
            <option value="<?= (int) $atividade['id_atividade'] ?>">
                <?= htmlspecialchars($atividade['titulo'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>

    </select>

    <small class="field-help">
        Somente atividades cadastradas serão apresentadas.
    </small>
</div>

                        <div class="form-field full-field">
                            <label for="reminderMessage">
                                Observação
                                <span class="optional-label">(opcional)</span>
                            </label>

                            <textarea
                                id="reminderMessage"
                                name="mensagem"
                                rows="3"
                                maxlength="500"
                                placeholder="Ex.: Separar o medicamento antes do café da manhã"
                            ></textarea>
                        </div>

                        <div class="form-field full-field">
                            <label for="reminderDateTime">
                                Data e hora do lembrete
                            </label>

                            <input
                                type="datetime-local"
                                id="reminderDateTime"
                                name="data_hora_lembrete"
                                step="60"
                                required
                            >
                        </div>
<!---
                        <div class="form-field">
                            <label for="reminderAdvance">
                                Antecedência
                            </label>

                            <select id="reminderAdvance">
                                <option selected>
                                    No horário escolhido
                                </option>

                                <option>5 minutos antes</option>
                                <option>15 minutos antes</option>
                                <option>30 minutos antes</option>
                                <option>1 hora antes</option>
                                <option>1 dia antes</option>
                            </select>
                        </div>
--->
                        <div class="form-field full-field">
                            <label for="reminderChannel">
                                Forma de aviso
                            </label>

                            <select id="reminderChannel">
                                <option selected>
                                    Notificação no AMES
                                </option>

                                <option>E-mail</option>
                            </select>
                        </div>

                    </div>

                    <div class="activity-preview">
                        <i class="fas fa-link"></i>

                        <div>
                            <strong>Vínculo com a atividade</strong>

                            <p>
                                O lembrete será associado à atividade selecionada.
                            </p>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="save-reminder-btn"
                    >
                        <i class="fas fa-bell"></i>
                        Cadastrar lembrete
                    </button>

                    <?php if ($mensagem !== ''): ?>
    <p class="backend-message <?= $tipo_mensagem ?>">
        <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

                </div>
               </form>
            </article>

            <!-- VISUALIZAÇÃO DOS LEMBRETES -->
            <article class="reminders-list-card">

                <div class="card-heading">
                    <div class="card-heading-icon">
                        <i class="fas fa-calendar-days"></i>
                    </div>

                    <div>
                        <h2>Acompanhar lembretes</h2>

                        <p>
                            Consulte os próximos avisos e os já enviados.
                        </p>
                    </div>
                </div>

                <!-- CAMPOS PREENCHÍVEIS, MAS SEM FILTRAGEM -->
                <div class="filter-toolbar">

                    <div class="search-field">
                        <i class="fas fa-search"></i>

                        <input
                            type="search"
                            id="reminderSearch"
                            placeholder="Buscar lembrete"
                            autocomplete="off"
                        >
                    </div>

                    <select id="reminderStatusFilter">
                        <option value="todos" selected>Todos os status</option>
                        <option value="agendado">Agendados</option>
                        <option value="enviado">Enviados</option>
                    </select>

                </div>

             
<div class="reminder-columns">

    <?php
    $paineis = [
        [
            'titulo' => 'Próximos lembretes',
            'lembretes' => $lembretesAgendados,
            'classe' => 'scheduled',
            'status' => 'Agendado',
            'icone' => 'fa-clock'
        ],
        [
            'titulo' => 'Lembretes enviados',
            'lembretes' => $lembretesEnviados,
            'classe' => 'sent',
            'status' => 'Enviado',
            'icone' => 'fa-circle-check'
        ]
    ];
    ?>

    <?php foreach ($paineis as $painel): ?>

        <section class="reminder-panel">

            <div class="panel-heading">
                <div>
                    <span class="status-dot <?= $painel['classe'] ?>-dot"></span>
                    <h3><?= htmlspecialchars($painel['titulo']) ?></h3>
                </div>

                <span class="panel-count">
                    <?= count($painel['lembretes']) ?>
                </span>
            </div>

            <div class="reminder-list">

                <?php if (empty($painel['lembretes'])): ?>

                    <p>Nenhum lembrete encontrado.</p>

                <?php else: ?>

                    <?php foreach ($painel['lembretes'] as $lembrete): ?>

                        <article class="reminder-item <?= $painel['classe'] === 'sent' ? 'is-sent' : '' ?>"
                        data-status="<?= htmlspecialchars($lembrete['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                           

                        <form method="POST"
                              action="notifications.php"
                              class="delete-reminder-form"
                              onsubmit="return confirm('Deseja realmente excluir este lembrete?');">
                                            
                            <input type="hidden"
                                   name="id_lembrete"
                                   value="<?= (int) $lembrete['id_lembrete'] ?>">
                                            
                            <button type="submit"
                                    name="acao_lembrete"
                                    value="excluir"
                                    class="delete-reminder-btn"
                                    title="Excluir lembrete"
                                    aria-label="Excluir lembrete">
                                            
                                <i class="fas fa-xmark"></i>
                                            
                            </button>
                        </form>


                        <div class="reminder-item-top">

                                <span class="reminder-status <?= $painel['classe'] ?>-status">
                                    <i class="fas <?= $painel['icone'] ?>"></i>
                                    <?= $painel['status'] ?>
                                </span>

                                <span class="reminder-date">
                                    <?= !empty($lembrete['data'])
                                        ? date('d/m', strtotime($lembrete['data']))
                                        : '--/--' ?>
                                </span>

                            </div>

                            <h4>
                                <?= htmlspecialchars($lembrete['titulo_atividade'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </h4>

                            <p class="reminder-observation">
                                <?= htmlspecialchars($lembrete['mensagem'] ?: 'Sem observação', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="reminder-information">

                                <div>
                                    <i class="fas fa-bell"></i>

                                    <span>
                                        <small>
                                            <?= $painel['classe'] === 'sent'
                                                ? 'Horário programado'
                                                : 'Lembrar em' ?>
                                        </small>

                                        <strong>
                                            <?= formatarDataLembrete(
                                                $lembrete['data'],
                                                $lembrete['horario']
                                            ) ?>
                                        </strong>
                                    </span>
                                </div>

                                <div>
                                    <i class="fas fa-bookmark"></i>

                                    <span>
                                        <small>Atividade</small>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $lembrete['nome_categoria'] ?? 'Outro',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </span>
                                </div>

                            </div>

                            <!-- Funcionalidades futuras -->
                            

                        </article>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>
        </section>

    <?php endforeach; ?>

</div>

            </article>

        </section>
    </main>

</body>
</html>