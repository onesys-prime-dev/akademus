import { state } from './state.js';
import { fetchApplicationContent } from './api.js';
import { setActiveTab, renderContent, showLoading, showError, updatePagination, updateFilterCounts, resetFilters, updateTableOnly, showTableLoading, openDocModal, closeDocModal } from './ui.js';

let debounceTimer;

async function update({ fullRender = false } = {}) {

    if (fullRender) {
        showLoading();
    } else {
        showTableLoading();
    }

    let res;
    try {
        res = await fetchApplicationContent();
    } catch (err) {
        console.error('fetchApplicationContent error:', err);
        showError('Failed to load content. Please refresh the page.');
        return;
    }

    if (!res.success) {
        showError(res.message || 'Failed to load content.');
        return;
    }

    state.totalPages = Math.max(1, Math.ceil((res.total || 0) / res.limit));

    if (state.page > state.totalPages) {
        state.page = state.totalPages;
    }

    setActiveTab(state.activeTab);

    if (fullRender) {
        renderContent(res.html);
    } else {
        const success = updateTableOnly(res.html);

        // ❗ fallback if table missing
        if (!success) {
            renderContent(res.html);
        }
    }

    // ✅ ALWAYS sync filter UI (not just full render)
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.toggle('filter-active', btn.dataset.filter === state.status);
    });

    updatePagination(res);
    updateFilterCounts(res.counts || {});
}

export function initTabs() {
    document.addEventListener('click', (e) => {
        const tab = e.target.closest('[data-tab]');
        if (!tab) return;

        const nextTab = tab.dataset.tab;
        if (!nextTab || nextTab === state.activeTab) return;

        state.activeTab = nextTab;
        state.status = 'all';
        state.search = '';
        state.page = 1;

        resetFilters();

        update({ fullRender: true }); // ✅ FULL
    });
}

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

export function initDocModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.ao-view-doc-btn');
        if (btn) {
            openDocModal(btn.dataset.docUrl, btn.dataset.docName);
            return;
        }

        const closeBtn = e.target.closest('#ao-doc-modal-close');
        if (closeBtn) { closeDocModal(); return; }

        const modal = document.getElementById('ao-doc-modal');
        if (modal && e.target === modal) { closeDocModal(); }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDocModal();
    });
}

export function init() {
    setActiveTab(state.activeTab);
    update({ fullRender: true });
}