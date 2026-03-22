const { baseUrl } = window.applicantsConfig;

export function updateTable(applicants) {
    const tbody = document.getElementById('applicants-table-body');

    if (!applicants.length) {
        tbody.innerHTML = `
            <tr class="applicants-table__row">
                <td class="applicants-table__cell" colspan="9" style="text-align:center;">
                    No applicants found
                </td>
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
        <img class="applicants-table__cell--actions-img"
            src="${baseUrl}/assets/images/icons/view.svg"
            data-action="view"
            data-id="${id}"
        >

        <img class="applicants-table__cell--actions-img"
            src="${baseUrl}/assets/images/icons/pen-edit.svg"
            data-action="edit"
            data-id="${id}"
        >

        <img class="applicants-table__cell--actions-img"
            src="${baseUrl}/assets/images/icons/trash.svg"
            data-action="delete"
            data-id="${id}"
        >
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

    const d = new Date(date);
    return d.toISOString().split('T')[0];
}

export function showLoading() {
    const tbody = document.getElementById('applicants-table-body');

    tbody.innerHTML = `
        <tr class="applicants-table__row">
            <td class="applicants-table__cell" colspan="9" style="text-align:center;">
                Loading...
            </td>
        </tr>
    `;
}

export function hideLoading() {
    document.getElementById('table-loading').hidden = true;
}

export function updatePagination({ page, total, limit }) {
    const totalPages = Math.max(1, Math.ceil(total / limit));

    const pageNumber = document.getElementById('page-number');
    const prevBtn = document.querySelector('[data-page="prev"]');
    const nextBtn = document.querySelector('[data-page="next"]');

    if (!pageNumber || !prevBtn || !nextBtn) return;

    // fix overflow
    const safePage = Math.min(page, totalPages);

    // page number
    pageNumber.textContent = safePage;

    // active class
    pageNumber.classList.add('pagination__btn--active-page');

    // visibility
    prevBtn.classList.toggle('pagination__btn--hidden', safePage <= 1);
    nextBtn.classList.toggle('pagination__btn--hidden', safePage >= totalPages);
}

export function updateFilterCounts(counts) {
    document.querySelectorAll('[data-filter]').forEach(btn => {
        const key = btn.dataset.filter;
        const countEl = btn.querySelector('.filters-button_count');

        if (!countEl) return;

        countEl.textContent = counts[key] ?? 0;
    });
}

export function resetBulkSelection() {
    const selectAll = document.querySelector('.bulk-actions__btn--select-all input');

    if (selectAll) {
        selectAll.checked = false;
    }
}

export function updateBulkButtons() {
    const selectedCount = document.querySelectorAll('[data-select]:checked').length;

    const buttons = document.querySelectorAll(
        '.bulk-actions__btn--cancel, .bulk-actions__btn--reject, .bulk-actions__btn--approve'
    );

    buttons.forEach(btn => {
        btn.disabled = selectedCount === 0;
        btn.classList.toggle('btn-disabled', selectedCount === 0);
    });
}