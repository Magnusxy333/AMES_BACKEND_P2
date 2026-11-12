
document.addEventListener("DOMContentLoaded", () => {

    const intervalo = 15000; // 15 segundos
    let verificando = false;

    function mostrarNotificacao(lembrete) {

        const notificacao = document.createElement("div");

        notificacao.className = "ames-notificacao";

        const titulo = document.createElement("strong");
        titulo.textContent = "🔔 " + lembrete.titulo;

        const mensagem = document.createElement("p");
        mensagem.textContent =
            lembrete.mensagem || "Chegou a hora da sua atividade!";

        const fechar = document.createElement("button");
        fechar.type = "button";
        fechar.textContent = "×";
        fechar.setAttribute("aria-label", "Fechar notificação");

        fechar.addEventListener("click", () => {
            notificacao.remove();
        });

        notificacao.appendChild(fechar);
        notificacao.appendChild(titulo);
        notificacao.appendChild(mensagem);

        document.body.appendChild(notificacao);
    }

    async function verificarLembretes() {

        if (verificando) return;

        verificando = true;

        try {
            const resposta = await fetch(
                "check_notifications.php",
                { cache: "no-store" }
            );

            if (!resposta.ok) {
                throw new Error("Falha na consulta");
            }

            const dados = await resposta.json();

            if (dados.sucesso) {
                dados.lembretes.forEach(mostrarNotificacao);
            }

        } catch (erro) {
            console.error(
                "Erro ao verificar lembretes:",
                erro
            );
        } finally {
            verificando = false;
        }
    }

    // Verificar assim que a página abrir
    verificarLembretes();

    // Continuar verificando periodicamente
    setInterval(verificarLembretes, intervalo);

});
