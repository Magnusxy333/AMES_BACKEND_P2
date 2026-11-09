
document.addEventListener("DOMContentLoaded", () => {
    const campoBusca = document.getElementById("activitySearch");
    const filtroCategoria = document.getElementById("activityCategoryFilter");

    const atividades = document.querySelectorAll(".activity-item");

    function normalizarTexto(texto) {
        return texto
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function filtrarAtividades() {
        const busca = normalizarTexto(campoBusca.value);
        const categoriaSelecionada = normalizarTexto(filtroCategoria.value);

        atividades.forEach((atividade) => {
            const titulo = normalizarTexto(
                atividade.querySelector("h4")?.textContent ?? ""
            );

            const descricao = normalizarTexto(
                atividade.querySelector(".activity-description")?.textContent ?? ""
            );

            const categoria = normalizarTexto(
                atividade.dataset.category ?? ""
            );

            const correspondeBusca =
                titulo.includes(busca) || descricao.includes(busca);

            const correspondeCategoria =
                categoriaSelecionada === "todos" ||
                categoria === categoriaSelecionada;

            atividade.hidden = !(correspondeBusca && correspondeCategoria);
            atividade.style.display =
                correspondeBusca && correspondeCategoria ? "" : "none";
        });
    }

    campoBusca.addEventListener("input", filtrarAtividades);
    filtroCategoria.addEventListener("change", filtrarAtividades);
});
