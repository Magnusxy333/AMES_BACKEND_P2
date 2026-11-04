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
                    <strong>4</strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon scheduled-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <span>Agendados</span>
                    <strong>3</strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon sent-icon">
                    <i class="fas fa-circle-check"></i>
                </div>

                <div>
                    <span>Enviados</span>
                    <strong>1</strong>
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
                <div class="reminder-form">

                    <div class="reminder-form-grid">

                        <div class="form-field full-field">
                            <label for="reminderActivity">
                                Atividade relacionada
                            </label>

                            <select id="reminderActivity">
                                <option selected>
                                    Selecione uma atividade
                                </option>

                                <option>
                                    Tomar medicamento da manhã
                                </option>

                                <option>
                                    Organizar documentos pessoais
                                </option>

                                <option>
                                    Consulta médica
                                </option>

                                <option>
                                    Guardar as compras
                                </option>
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
                                rows="3"
                                placeholder="Ex.: Separar o medicamento antes do café da manhã"
                            ></textarea>
                        </div>

                        <div class="form-field">
                            <label for="reminderDateTime">
                                Data e hora do lembrete
                            </label>

                            <input
                                type="datetime-local"
                                id="reminderDateTime"
                                step="60"
                            >
                        </div>

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
                        type="button"
                        class="save-reminder-btn"
                    >
                        <i class="fas fa-bell"></i>
                        Cadastrar lembrete
                    </button>

                    <p class="backend-message">
                        O cadastro será disponibilizado após a integração
                        com o banco de dados.
                    </p>

                </div>
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
                        <option selected>Todos os status</option>
                        <option>Agendados</option>
                        <option>Enviados</option>
                    </select>

                </div>

                <div class="reminder-columns">

                    <!-- LEMBRETES AGENDADOS -->
                    <section class="reminder-panel">

                        <div class="panel-heading">
                            <div>
                                <span class="status-dot scheduled-dot"></span>
                                <h3>Próximos lembretes</h3>
                            </div>

                            <span class="panel-count">3</span>
                        </div>

                        <div class="reminder-list">

                            <article class="reminder-item">

                                <div class="reminder-item-top">
                                    <span class="reminder-status scheduled-status">
                                        <i class="fas fa-clock"></i>
                                        Agendado
                                    </span>

                                    <span class="reminder-date">
                                        Hoje
                                    </span>
                                </div>

                                <h4>Tomar medicamento da manhã</h4>

                                <p class="reminder-observation">
                                    Separar o medicamento antes do café da manhã.
                                </p>

                                <div class="reminder-information">

                                    <div>
                                        <i class="fas fa-bell"></i>

                                        <span>
                                            <small>Lembrar em</small>
                                            <strong>14/09/2026 às 07:50</strong>
                                        </span>
                                    </div>

                                    <div>
                                        <i class="fas fa-pills"></i>

                                        <span>
                                            <small>Atividade</small>
                                            <strong>Medicamento</strong>
                                        </span>
                                    </div>

                                </div>

                                <div class="reminder-actions">
                                    <button
                                        type="button"
                                        class="secondary-action-btn"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        class="danger-action-btn"
                                    >
                                        <i class="fas fa-xmark"></i>
                                        Cancelar
                                    </button>
                                </div>

                            </article>

                            <article class="reminder-item">

                                <div class="reminder-item-top">
                                    <span class="reminder-status scheduled-status">
                                        <i class="fas fa-clock"></i>
                                        Agendado
                                    </span>

                                    <span class="reminder-date">
                                        Amanhã
                                    </span>
                                </div>

                                <h4>Consulta médica</h4>

                                <p class="reminder-observation">
                                    Separar documentos e resultados dos exames.
                                </p>

                                <div class="reminder-information">

                                    <div>
                                        <i class="fas fa-bell"></i>

                                        <span>
                                            <small>Lembrar em</small>
                                            <strong>15/09/2026 às 07:30</strong>
                                        </span>
                                    </div>

                                    <div>
                                        <i class="fas fa-calendar-check"></i>

                                        <span>
                                            <small>Atividade</small>
                                            <strong>Compromisso</strong>
                                        </span>
                                    </div>

                                </div>

                                <div class="reminder-actions">
                                    <button
                                        type="button"
                                        class="secondary-action-btn"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        class="danger-action-btn"
                                    >
                                        <i class="fas fa-xmark"></i>
                                        Cancelar
                                    </button>
                                </div>

                            </article>

                            <article class="reminder-item">

                                <div class="reminder-item-top">
                                    <span class="reminder-status scheduled-status">
                                        <i class="fas fa-clock"></i>
                                        Agendado
                                    </span>

                                    <span class="reminder-date">
                                        Amanhã
                                    </span>
                                </div>

                                <h4>Organizar documentos pessoais</h4>

                                <p class="reminder-observation">
                                    Começar a organização antes do compromisso.
                                </p>

                                <div class="reminder-information">

                                    <div>
                                        <i class="fas fa-bell"></i>

                                        <span>
                                            <small>Lembrar em</small>
                                            <strong>15/09/2026 às 13:30</strong>
                                        </span>
                                    </div>

                                    <div>
                                        <i class="fas fa-house"></i>

                                        <span>
                                            <small>Atividade</small>
                                            <strong>Tarefa doméstica</strong>
                                        </span>
                                    </div>

                                </div>

                                <div class="reminder-actions">
                                    <button
                                        type="button"
                                        class="secondary-action-btn"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        class="danger-action-btn"
                                    >
                                        <i class="fas fa-xmark"></i>
                                        Cancelar
                                    </button>
                                </div>

                            </article>

                        </div>
                    </section>

                    <!-- LEMBRETES ENVIADOS -->
                    <section class="reminder-panel">

                        <div class="panel-heading">
                            <div>
                                <span class="status-dot sent-dot"></span>
                                <h3>Lembretes enviados</h3>
                            </div>

                            <span class="panel-count">1</span>
                        </div>

                        <div class="reminder-list">

                            <article class="reminder-item is-sent">

                                <div class="reminder-item-top">
                                    <span class="reminder-status sent-status">
                                        <i class="fas fa-circle-check"></i>
                                        Enviado
                                    </span>

                                    <span class="reminder-date">
                                        12/09
                                    </span>
                                </div>

                                <h4>Guardar as compras</h4>

                                <p class="reminder-observation">
                                    Organizar os produtos nos armários.
                                </p>

                                <div class="reminder-information">

                                    <div>
                                        <i class="fas fa-bell"></i>

                                        <span>
                                            <small>Enviado em</small>
                                            <strong>12/09/2026 às 16:45</strong>
                                        </span>
                                    </div>

                                    <div>
                                        <i class="fas fa-house"></i>

                                        <span>
                                            <small>Atividade</small>
                                            <strong>Tarefa doméstica</strong>
                                        </span>
                                    </div>

                                </div>

                                <div class="reminder-actions">
                                    <button
                                        type="button"
                                        class="secondary-action-btn"
                                    >
                                        <i class="fas fa-rotate-left"></i>
                                        Reagendar
                                    </button>

                                    <button
                                        type="button"
                                        class="danger-action-btn"
                                    >
                                        <i class="fas fa-trash"></i>
                                        Excluir
                                    </button>
                                </div>

                            </article>

                        </div>
                    </section>

                </div>
            </article>

        </section>
    </main>

</body>
</html>