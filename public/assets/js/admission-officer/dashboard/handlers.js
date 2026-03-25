import { state } from './state.js';
import { fetchApplicants, bulkUpdateApplicants, goToApplicant } from './api.js';
import { updateTable, updatePagination, updateFilterCounts, showLoading, hideLoading, resetBulkSelection, updateBulkButtons } from './ui.js';

let debounceTimer;

async function update() {
    showLoading();

    resetBulkSelection();
    updateBulkButtons();

    const res = await fetchApplicants();

    state.totalPages = Math.max(1, Math.ceil(res.total / res.limit));

    if (state.page > state.totalPages) {
        state.page = state.totalPages;
    }

    updateTable(res.data);
    updatePagination(res);
    updateFilterCounts(res.counts);

    hideLoading();
}

async function bulkUpdate(ids, status) {
    try {
        await bulkUpdateApplicants(ids, status);

        // refresh table
        update();

    } catch (err) {
        console.error(err);
    }
}

// FILTER
export function initFilters() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-filter]');
        if (!btn) return;

        state.status = btn.dataset.filter;
        state.page = 1;

        document.querySelectorAll('[data-filter]').forEach(b => {
            b.classList.remove('filter-active');
        });

        btn.classList.add('filter-active');

        update();
    });
}

// PROGRAM
export function initProgram() {
    document.addEventListener('change', (e) => {
        if (!e.target.matches('[data-program]')) return;

        state.program = e.target.value;
        state.page = 1;

        update();
    });
}

// SEARCH
export function initSearch() {
    document.addEventListener('input', (e) => {
        if (!e.target.matches('[data-search]')) return;

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            state.search = e.target.value;
            state.page = 1;
            update();
        }, 400);
    });
}

// PAGINATION
export function initPagination() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-page]');
        if (!btn) return;

        if (btn.dataset.page === 'next' && state.page < state.totalPages) {
            state.page++;
        }

        if (btn.dataset.page === 'prev' && state.page > 1) {
            state.page--;
        }

        update();
    });
}

// BULK SELECTION
export function initBulkSelect() {
    const selectAll = document.querySelector('.bulk-actions__btn--select-all input');

    document.addEventListener('change', (e) => {
        // 🔘 select all checkbox clicked
        if (e.target === selectAll) {
            const checked = e.target.checked;

            document.querySelectorAll('[data-select]').forEach(cb => {
                cb.checked = checked;
            });
        }

        // 🔘 individual checkbox clicked
        if (e.target.matches('[data-select]')) {
            const all = document.querySelectorAll('[data-select]');
            const checked = document.querySelectorAll('[data-select]:checked');

            // sync select all checkbox
            selectAll.checked = all.length === checked.length;
        }

        updateBulkButtons();
    });
}

export function initBulkActions() {
    document.addEventListener('click', async (e) => {

        // ❌ CANCEL
        if (e.target.closest('.bulk-actions__btn--cancel')) {
            resetBulkSelection();

            document.querySelectorAll('[data-select]').forEach(cb => {
                cb.checked = false;
            });

            updateBulkButtons();

            return;
        }

        // 🔴 REJECT
        if (e.target.closest('.bulk-actions__btn--reject')) {
            const ids = getSelectedIds();

            if (!ids.length) return alert('No applicants selected');

            await bulkUpdate(ids, 'rejected');
        }

        // 🟢 APPROVE
        if (e.target.closest('.bulk-actions__btn--approve')) {
            const ids = getSelectedIds();

            if (!ids.length) return alert('No applicants selected');

            await bulkUpdate(ids, 'approved');
        }
    });
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('[data-select]:checked'))
        .map(cb => cb.closest('[data-row]')?.dataset.id)
        .filter(Boolean);
}

// ROW CLICK
export function initRowClick() {
    document.addEventListener('click', (e) => {

        const row = e.target.closest('[data-row]');
        if (!row) return;

        // ❗ prevent trigger when clicking checkbox or actions
        if (
            e.target.closest('[data-select]') ||
            e.target.closest('[data-action]')
        ) {
            return;
        }

        const id = row.dataset.id;
        if (!id) return;

        goToApplicant(id);
    });
}

// INIT
export function init() {
    update();
}