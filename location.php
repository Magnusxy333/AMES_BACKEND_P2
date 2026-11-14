<?php
session_start();
// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Você precisa estar logado para acessar esta página.'); window.location.href='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localização - AMES</title>
    <link rel="icon" href="images/logotipoB-AMES.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/location1.css">
    
    <!-- CSS e JS do Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            <a class="nav-link text-white fw-bold" href="location.php"><i class="fas fa-map-marker-alt me-2"></i>Localização</a>
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
            
            <!-- PAINEL ESQUERDO DINÂMICO -->
            <div class="left-panel">
                <h1>Tarefas</h1>
                <div id="tasksContainer">
                    <!-- Os cards reais do banco de dados serão injetados aqui pelo JavaScript -->
                    <p class="text-muted mt-3">Carregando tarefas...</p>
                </div>
            </div>

            <!-- PAINEL DIREITO: BUSCA E MAPA -->
            <div class="right-panel">
                <h2 class="page-title">Localização das Tarefas</h2>

                <div class="search-box">
                    <div class="search-label">Minhas tarefas:</div>
                    <input id="taskSearch" type="search" placeholder="Qual tarefa você procura?" aria-label="Qual tarefa você procura?">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <div class="map-card" style="height: 450px; width: 100%; border-radius: 12px; overflow: hidden; z-index: 1;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
            </div>
            
        </section>
    </main>

    <script>
        // Elementos da interface
        const tasksContainer = document.getElementById('tasksContainer');
        const search = document.getElementById('taskSearch');
        let allActivityCards = []; // Array para guardar os cards e facilitar o filtro de busca

        // Inicializa o mapa centrado em Mauá (coordenadas padrão caso não tenha atividades)
        const map = L.map('map').setView([-23.6661, -46.4616], 13); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Função para definir um ícone básico dependendo do título da atividade
        function getIconClass(titulo) {
            const t = titulo.toLowerCase();
            if (t.includes('mercado') || t.includes('compra')) return 'fa-cart-shopping';
            if (t.includes('médic') || t.includes('consulta') || t.includes('remédio') || t.includes('medicamento')) return 'fa-user-doctor';
            if (t.includes('trabalho') || t.includes('reunião')) return 'fa-briefcase';
            if (t.includes('casa') || t.includes('limpeza')) return 'fa-house';
            return 'fa-thumbtack'; // Ícone padrão
        }

       async function carregarAtividadesNoMapa() {
            try {
                const response = await fetch('api/atividades.php');
                
                if (!response.ok) {
                    let errorMsg = 'Falha ao comunicar com a API';
                    try {
                        const errorData = await response.json();
                        if (errorData.message) errorMsg = errorData.message;
                    } catch(e) {}
                    throw new Error(errorMsg + ` (Status HTTP: ${response.status})`);
                }
                
                // CORREÇÃO: Lê o JSON e acessa a chave 'atividades' enviada pelo PHP
                const dados = await response.json();
                const atividades = dados.atividades || []; 
                
                const bounds = [];
                
                tasksContainer.innerHTML = ''; // Limpa o "Carregando tarefas..."
                
                if (atividades.length === 0) {
                    tasksContainer.innerHTML = '<p class="text-muted mt-3">Você não possui atividades cadastradas.</p>';
                    return;
                }

                atividades.forEach(atividade => {
                    let marker = null;
                    const hasLocation = atividade.latitude && atividade.longitude;
                    const lat = hasLocation ? parseFloat(atividade.latitude) : null;
                    const lng = hasLocation ? parseFloat(atividade.longitude) : null;

                    // 1. Cria o Pino no mapa
                    if (hasLocation) {
                        marker = L.marker([lat, lng]).addTo(map);
                        marker.bindPopup(`
                            <strong>${atividade.titulo}</strong><br>
                            ${atividade.descricao ? atividade.descricao + '<br>' : ''}
                            <em style="color: gray;">Status: ${atividade.status}</em>
                        `);
                        bounds.push([lat, lng]);
                    }

                    // 2. Cria o Card
                    const card = document.createElement('div');
                    card.className = 'task-card';
                    card.dataset.task = atividade.titulo.toLowerCase();
                    
                    const iconClass = getIconClass(atividade.titulo);
                    
                    card.innerHTML = `
                        <div class="task-icon"><i class="fa-solid ${iconClass}"></i></div>
                        <div>
                            <h2>${atividade.titulo}</h2>
                            <p style="font-size: 0.85rem; color: #666; margin: 0;">
                                ${hasLocation ? 'Ver no mapa <i class="fa-solid fa-location-arrow ml-1"></i>' : 'Sem local definido'}
                            </p>
                        </div>
                    `;

                    // 3. Clique no card
                    card.addEventListener('click', () => {
                        allActivityCards.forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');
                        
                        if (hasLocation && marker) {
                            map.flyTo([lat, lng], 16, { animate: true, duration: 1.5 });
                            marker.openPopup();
                        } else {
                            alert('Esta atividade não possui um endereço ou localização registrada.');
                        }
                    });

                    tasksContainer.appendChild(card);
                    allActivityCards.push(card);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

            } catch (error) {
                console.error('Erro detalhado:', error);
                tasksContainer.innerHTML = `
                    <div class="alert alert-danger mt-3" style="font-size: 0.9rem;">
                        <strong>Erro ao carregar:</strong><br>
                        ${error.message}
                    </div>`;
            }
        }

        // Executa a busca assim que digita no input
        search.addEventListener('input', () => {
            const value = search.value.toLowerCase().trim();
            
            allActivityCards.forEach(card => {
                const text = card.innerText.toLowerCase();
                // Oculta ou exibe baseado no termo digitado
                card.style.display = (value === '' || text.includes(value)) ? '' : 'none';
            });
        });

        // Inicia a aplicação
        carregarAtividadesNoMapa();
    </script>
</body>
</html>