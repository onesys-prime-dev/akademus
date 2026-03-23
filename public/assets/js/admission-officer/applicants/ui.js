export function setActiveTab(tab) {
    document.querySelectorAll('[data-tab]').forEach(el => {
        el.classList.toggle('application-tabs__item--active', el.dataset.tab === tab);
    });
}

export function renderContent(html) {
    const container = document.getElementById('application-content');
    if (!container) return;

    container.innerHTML = html;
}

export function showLoading() {
    const container = document.getElementById('application-content');
    if (!container) return;

    container.innerHTML = `
        <div class="application-content__loading" style="padding: 24px; text-align: center;">
            Loading...
        </div>
    `;
}

export function updatePagination({ page, total, limit }) {
    const totalPages = Math.max(1, Math.ceil(total / limit));

    const pageNumber = document.getElementById('page-number');
    const prevBtn = document.querySelector('[data-page="prev"]');
    const nextBtn = document.querySelector('[data-page="next"]');

    if (!pageNumber || !prevBtn || !nextBtn) return;

    const safePage = Math.min(page, totalPages);

    pageNumber.textContent = safePage;
    pageNumber.classList.add('pagination__btn--active-page');

    prevBtn.classList.toggle('pagination__btn--hidden', safePage <= 1);
    nextBtn.classList.toggle('pagination__btn--hidden', safePage >= totalPages);
}

export function updateFilterCounts(counts = {}) {
    document.querySelectorAll('[data-filter]').forEach(btn => {
        const key = btn.dataset.filter;
        const countEl = btn.querySelector('.filters-button_count');

        if (!countEl) return;

        countEl.textContent = counts[key] ?? 0;
    });
}

export function resetFilters() {
    const searchInput = document.querySelector('[data-search]');
    if (searchInput) {
        searchInput.value = '';
    }

    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('filter-active');
        if (btn.dataset.filter === 'all') {
            btn.classList.add('filter-active');
        }
    });
}

export function updateTableOnly(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;

    const newTbody = temp.querySelector('#application-table-body');
    const currentTbody = document.querySelector('#application-table-body');

    // ❗ guard
    if (!newTbody || !currentTbody) {
        console.warn('Table not found, fallback to full render');
        return false;
    }

    currentTbody.innerHTML = newTbody.innerHTML;
    return true;
}

export function showTableLoading() {
    const tbody = document.getElementById('application-table-body');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr class="application-table__row">
            <td class="application-table__cell" colspan="8" style="text-align:center;">
                Loading...
            </td>
        </tr>
    `;
}