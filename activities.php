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

    <button
        type="button"
        class="new-activity-btn"
        aria-disabled="true"
    >
        <i class="fas fa-plus"></i>
        Nova atividade
    </button>

</section>

        <!-- RESUMO ESTÁTICO -->
        <section class="activities-summary">

            <article class="summary-card">
                <div class="summary-icon total-icon">
                    <i class="fas fa-layer-group"></i>
                </div>

                <div>
                    <span>Total</span>
                    <strong>4</strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon pending-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <span>Pendentes</span>
                    <strong>2</strong>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-icon completed-icon">
                    <i class="fas fa-circle-check"></i>
                </div>

                <div>
                    <span>Concluídas</span>
                    <strong>2</strong>
                </div>
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

                <div class="activity-form">

<div class="activity-form-grid">

    <div class="form-field full-field">
        <label for="activityTitle">
            Título da atividade
        </label>

        <input
            type="text"
            id="activityTitle"
            placeholder="Ex.: Tomar medicamento"
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
            placeholder="Inclua informações importantes sobre a atividade"
        ></textarea>
    </div>

    <div class="form-field">
        <label for="activityType">
            Tipo de atividade
        </label>

        <select id="activityType">
            <option selected>Selecione o tipo</option>
            <option>Tarefa doméstica</option>
            <option>Compromisso</option>
            <option>Medicamento</option>
            <option>Outro</option>
        </select>
    </div>

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

<div class="form-field">
    <label for="startDateTime">
        Data e hora de início
    </label>

    <input
        type="datetime-local"
        id="startDateTime"
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
        step="60"
    >
</div>

</div>

<p class="form-message"></p>

<button
    type="button"
    class="save-activity-btn"
    disabled
>
    <i class="fas fa-plus-circle"></i>
    Cadastrar atividade
</button>
                </div>
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

                    <!-- PENDENTES -->
                    <section class="activity-panel pending-panel">

                        <div class="panel-heading">
                            <div>
                                <span class="status-dot pending-dot"></span>
                                <h3>Pendentes</h3>
                            </div>

                            <span class="panel-count">2</span>
                        </div>

                        <div class="activity-list">

                            <article class="activity-item">

                                <div class="activity-item-top">
                                    <span class="activity-type-badge type-medicamento">
                                        <i class="fas fa-pills"></i>
                                        Medicamento
                                    </span>

                                    <span class="activity-status">
                                        Pendente
                                    </span>
                                </div>

                                <h4>Tomar medicamento da manhã</h4>

                                <p class="activity-description">
                                    Tomar o medicamento após o café da manhã.
                                </p>

                                <dl class="activity-dates">
                                    <div>
                                        <dt>
                                            <i class="fas fa-calendar-plus"></i>
                                            Criada em
                                        </dt>
                                        <dd>14/09/2026, 07:30</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-play"></i>
                                            Início
                                        </dt>
                                        <dd>14/09/2026, 08:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-flag-checkered"></i>
                                            Término
                                        </dt>
                                        <dd>14/09/2026, 08:15</dd>
                                    </div>
                                </dl>

                                <button
                                    type="button"
                                    class="status-button"
                                    disabled
                                >
                                    <i class="fas fa-check"></i>
                                    Marcar como concluída
                                </button>

                            </article>

                            <article class="activity-item">

                                <div class="activity-item-top">
                                    <span class="activity-type-badge type-domestica">
                                        <i class="fas fa-house"></i>
                                        Tarefa doméstica
                                    </span>

                                    <span class="activity-status">
                                        Pendente
                                    </span>
                                </div>

                                <h4>Organizar documentos pessoais</h4>

                                <p class="activity-description">
                                    Separar contas, documentos e comprovantes importantes.
                                </p>

                                <dl class="activity-dates">
                                    <div>
                                        <dt>
                                            <i class="fas fa-calendar-plus"></i>
                                            Criada em
                                        </dt>
                                        <dd>13/09/2026, 17:30</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-play"></i>
                                            Início
                                        </dt>
                                        <dd>15/09/2026, 14:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-flag-checkered"></i>
                                            Término
                                        </dt>
                                        <dd>15/09/2026, 15:30</dd>
                                    </div>
                                </dl>

                                <button
                                    type="button"
                                    class="status-button"
                                    disabled
                                >
                                    <i class="fas fa-check"></i>
                                    Marcar como concluída
                                </button>

                            </article>

                        </div>
                    </section>

                    <!-- CONCLUÍDAS -->
                    <section class="activity-panel completed-panel">

                        <div class="panel-heading">
                            <div>
                                <span class="status-dot completed-dot"></span>
                                <h3>Concluídas</h3>
                            </div>

                            <span class="panel-count">2</span>
                        </div>

                        <div class="activity-list">

                            <article class="activity-item is-completed">

                                <div class="activity-item-top">
                                    <span class="activity-type-badge type-compromisso">
                                        <i class="fas fa-calendar-check"></i>
                                        Compromisso
                                    </span>

                                    <span class="activity-status">
                                        Concluída
                                    </span>
                                </div>

                                <h4>Consulta médica</h4>

                                <p class="activity-description">
                                    Levar documentos e resultados dos últimos exames.
                                </p>

                                <dl class="activity-dates">
                                    <div>
                                        <dt>
                                            <i class="fas fa-calendar-plus"></i>
                                            Criada em
                                        </dt>
                                        <dd>10/09/2026, 10:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-play"></i>
                                            Início
                                        </dt>
                                        <dd>13/09/2026, 09:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-flag-checkered"></i>
                                            Término
                                        </dt>
                                        <dd>13/09/2026, 10:00</dd>
                                    </div>
                                </dl>

                                <button
                                    type="button"
                                    class="status-button"
                                    disabled
                                >
                                    <i class="fas fa-rotate-left"></i>
                                    Reabrir atividade
                                </button>

                            </article>

                            <article class="activity-item is-completed">

                                <div class="activity-item-top">
                                    <span class="activity-type-badge type-domestica">
                                        <i class="fas fa-house"></i>
                                        Tarefa doméstica
                                    </span>

                                    <span class="activity-status">
                                        Concluída
                                    </span>
                                </div>

                                <h4>Guardar as compras</h4>

                                <p class="activity-description">
                                    Organizar os alimentos e produtos nos armários.
                                </p>

                                <dl class="activity-dates">
                                    <div>
                                        <dt>
                                            <i class="fas fa-calendar-plus"></i>
                                            Criada em
                                        </dt>
                                        <dd>11/09/2026, 13:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-play"></i>
                                            Início
                                        </dt>
                                        <dd>12/09/2026, 17:00</dd>
                                    </div>

                                    <div>
                                        <dt>
                                            <i class="fas fa-flag-checkered"></i>
                                            Término
                                        </dt>
                                        <dd>12/09/2026, 17:30</dd>
                                    </div>
                                </dl>

                                <button
                                    type="button"
                                    class="status-button"
                                    disabled
                                >
                                    <i class="fas fa-rotate-left"></i>
                                    Reabrir atividade
                                </button>

                            </article>

                        </div>
                    </section>

                </div>
            </article>

        </section>
    </main>

</body>
</html>