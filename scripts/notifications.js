
document.addEventListener("DOMContentLoaded", () => {

    const campoBusca = document.getElementById("reminderSearch");
    const filtroStatus = document.getElementById("reminderStatusFilter");
    const lembretes = document.querySelectorAll(".reminder-item");

    function normalizarTexto(texto) {
        return texto
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function filtrarLembretes() {
        const busca = normalizarTexto(campoBusca.value);
        const statusSelecionado = filtroStatus.value;

        lembretes.forEach((lembrete) => {

            const titulo = normalizarTexto(
                lembrete.querySelector("h4")?.textContent ?? ""
            );

            const observacao = normalizarTexto(
                lembrete.querySelector(".reminder-observation")?.textContent ?? ""
            );

            const status = lembrete.dataset.status;

            const correspondeBusca =
                titulo.includes(busca) || observacao.includes(busca);

            const correspondeStatus =
                statusSelecionado === "todos" ||
                status === statusSelecionado;

            const deveExibir = correspondeBusca && correspondeStatus;

            lembrete.hidden = !deveExibir;
            lembrete.style.display = deveExibir ? "" : "none";
        });
    }

    campoBusca.addEventListener("input", filtrarLembretes);
    filtroStatus.addEventListener("change", filtrarLembretes);

});
