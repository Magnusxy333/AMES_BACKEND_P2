<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localização - AMES</title>
    <link rel="icon" href="images/logotipoB-AMES.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/location.css">
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

    <main class="location-page">
        <section class="location-content">
            <div class="left-panel">
                <h1>Tarefas</h1>

                <div class="task-card market" data-task="mercado">
                    <div class="task-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div>
                        <h2>Ir ao Mercado</h2>
                        <p>Encontre mercados próximos.</p>
                    </div>
                </div>

                <div class="task-card doctor" data-task="consulta">
                    <div class="task-icon"><i class="fa-solid fa-user-doctor"></i></div>
                    <div>
                        <h2>Marcar consulta</h2>
                        <p>Localize clínicas e consultas.</p>
                    </div>
                </div>

                <div class="task-card work" data-task="trabalho">
                    <div class="task-icon"><i class="fa-solid fa-briefcase"></i></div>
                    <div>
                        <h2>Ir Trabalhar</h2>
                        <p>Veja o caminho até o trabalho.</p>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <h2 class="page-title">Localização das Tarefas</h2>

                <div class="search-box">
                    <div class="search-label">Minhas tarefas:</div>
                    <input id="taskSearch" type="search" placeholder="Qual tarefa você procura?" aria-label="Qual tarefa você procura?">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <div class="map-card">
                    <img src="images/location-map.png" alt="Mapa de localização das tarefas">
                </div>
            </div>
        </section>
    </main>

    <script>
        const cards = document.querySelectorAll('.task-card');
        const search = document.getElementById('taskSearch');

        cards.forEach(card => {
            card.addEventListener('click', () => {
                cards.forEach(item => item.classList.remove('selected'));
                card.classList.add('selected');
            });
        });

        search.addEventListener('input', () => {
            const value = search.value.toLowerCase().trim();
            cards.forEach(card => {
                const text = card.innerText.toLowerCase();
                card.hidden = value !== '' && !text.includes(value);
            });
        });
    </script>
</body>
</html>
