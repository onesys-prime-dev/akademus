const { baseUrl } = window.applicantsConfig || {};


// ==============================
// PROFILE UI
// ==============================
export function setActiveTab(tab) {
    document.querySelectorAll('[data-tab]').forEach(el => {
        el.classList.toggle('application-tabs__item--active', el.dataset.tab === tab);
    });
}

export function renderContent(html) {
    const el = document.getElementById('application-content');
    if (el) el.innerHTML = html;
}

export function showContentLoading() {
    const el = document.getElementById('application-content');
    if (!el) return;

    el.innerHTML = `
        <div class="application-content__loading" style="padding:24px;text-align:center;">
            Loading...
        </div>
    `;
}

export function updateTableOnly(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;

    const newTbody = temp.querySelector('#application-table-body');
    const current = document.querySelector('#application-table-body');

    if (!newTbody || !current) {
        console.warn('Table not found, fallback to full render');
        return false;
    }

    current.innerHTML = newTbody.innerHTML;
    return true;
}


// ==============================
// LIST UI
// ==============================
export function updateApplicantsTable(applicants) {
    const tbody = document.getElementById('applicants-table-body');
    if (!tbody) return;

    if (!applicants.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" style="text-align:center;">No applicants found</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = applicants.map(app => `
        <tr class="applicants-table__row" data-row data-id="${app.applicant_id}">

            <td class="applicants-table__cell applicants-table__cell--checkbox">
                <input type="checkbox" data-select>
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_name)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.email)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_number)}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.applicant_type || '-')}
            </td>

            <td class="applicants-table__cell">
                ${escapeHtml(app.program)}
            </td>

            <td class="applicants-table__cell">
                ${renderStatus(app.status)}
            </td>

            <td class="applicants-table__cell">
                ${formatDate(app.date_added)}
            </td>

            <td class="applicants-table__cell applicants-table__cell--actions">
                ${renderActions(app.applicant_id)}
            </td>

        </tr>
    `).join('');
}


// ==============================
// SHARED UI
// ==============================
export function showTableLoading() {
    const tbody =
        document.getElementById('application-table-body') ||
        document.getElementById('applicants-table-body');

    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td colspan="10" style="text-align:center;">Loading...</td>
        </tr>
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
        const el = btn.querySelector('.filters-button_count');
        if (el) el.textContent = counts[key] ?? 0;
    });
}

export function resetFilters() {
    const input = document.querySelector('[data-search]');
    if (input) input.value = '';

    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('filter-active');
        if (btn.dataset.filter === 'all') {
            btn.classList.add('filter-active');
        }
    });
}

export function resetBulkSelection() {
    const selectAll = document.querySelector('.bulk-actions__btn--select-all input');
    if (selectAll) selectAll.checked = false;
}

export function updateBulkButtons() {
    const count = document.querySelectorAll('[data-select]:checked').length;

    document.querySelectorAll(
        '.bulk-actions__btn--cancel, .bulk-actions__btn--reject, .bulk-actions__btn--approve'
    ).forEach(btn => {
        btn.disabled = count === 0;
        btn.classList.toggle('btn-disabled', count === 0);
    });
}


// ==============================
// INTERNAL HELPERS
// ==============================
function renderStatus(status) {
    const map = {
        approved: 'status-badge status-badge--approved',
        pending: 'status-badge status-badge--pending',
        rejected: 'status-badge status-badge--rejected',
        in_progress: 'status-badge status-badge--in_progress',
        completed: 'status-badge status-badge--completed'
    };

    const cls = map[status] || 'status-badge';

    return `<span class="${cls}">
        ${formatStatus(status)}
    </span>`;
}

function formatStatus(status) {
    if (!status) return '-';

    return status
        .replace('_', ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
}

function renderActions(id) {
    return `
        <img src="${baseUrl}/assets/images/icons/view.svg" data-action="view" data-id="${id}">
        <img src="${baseUrl}/assets/images/icons/pen-edit.svg" data-action="edit" data-id="${id}">
        <img src="${baseUrl}/assets/images/icons/trash.svg" data-action="delete" data-id="${id}">
    `;
}

function escapeHtml(str) {
    if (!str) return '';

    return str.replace(/[&<>"']/g, m => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[m]));
}

function formatDate(date) {
    if (!date) return '-';

    return new Date(date).toISOString().split('T')[0];
}