function getProductsPage() {
    const params = new URLSearchParams(window.location.search);
    return parseInt(params.get("page")) || 1;
}

function escapeHTML(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}

async function loadProducts(page) {
    const res = await fetch(`api/products.php?page=${page}`);
    const data = await res.json();

    const info = document.getElementById("products-info");
    const tbody = document.getElementById("products-tbody");
    const pagination = document.getElementById("products-pagination");

    info.innerHTML = `
                <span>Mostrando ${data.products.length} de ${data.total} registros</span>
                <span>Página ${data.page} de ${data.totalPages}</span>
            `;

    if (data.products.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Nenhum produto encontrado.</td></tr>`;
    } else {
        tbody.innerHTML = data.products.map(p => `
                    <tr>
                        <th scope="row"><span class="badge-id">${p.id}</span></th>
                        <td>${escapeHTML(p.description)}</td>
                        <td><span class="badge-qtd">${p.qtd}</span></td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a class="btn btn-warning btn-action" href="./pages/productEdit.php?id=${p.id}">Editar</a>
                                <a class="btn btn-danger btn-action" href="index.php?id=${p.id}">Deletar</a>
                            </div>
                        </td>
                    </tr>
                `).join("");
    }

    if (data.totalPages > 1) {
        const start = Math.max(1, data.page - 2);
        const end = Math.min(data.totalPages, data.page + 2);
        let items = "";

        items += `<li class="page-item ${data.page <= 1 ? "disabled" : ""}"><a class="page-link" href="?page=${data.page - 1}">Anterior</a></li>`;

        if (start > 1) {
            items += `<li class="page-item"><a class="page-link" href="?page=1">1</a></li>`;
            if (start > 2) items += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }

        for (let i = start; i <= end; i++) {
            items += `<li class="page-item ${i === data.page ? "active" : ""}"><a class="page-link" href="?page=${i}">${i}</a></li>`;
        }

        if (end < data.totalPages) {
            if (end < data.totalPages - 1) items += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            items += `<li class="page-item"><a class="page-link" href="?page=${data.totalPages}">${data.totalPages}</a></li>`;
        }

        items += `<li class="page-item ${data.page >= data.totalPages ? "disabled" : ""}"><a class="page-link" href="?page=${data.page + 1}">Próximo</a></li>`;

        pagination.innerHTML = `<ul class="pagination">${items}</ul>`;
    } else {
        pagination.innerHTML = "";
    }
}

loadProducts(getProductsPage());